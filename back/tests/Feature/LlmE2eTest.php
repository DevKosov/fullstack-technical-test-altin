<?php

use App\Models\User;
use Symfony\Component\Process\Process;
use WebSocket\Client;

/**
 * Hooks ────────────────────────────────────────────────────────────────
 */
beforeEach(function () {
    // random port so we can run many tests in parallel
    $this->port = random_int(9000, 9500);

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $env = array_merge($_SERVER, [
        'APP_ENV'          => 'testing',
        'REVERB_PORT'      => $this->port,
        'REVERB_HOST'      => '127.0.0.1',
        'BROADCAST_CONNECTION' => 'reverb',
        'QUEUE_CONNECTION' => 'redis',
    ]);

    /* Reverb server */
    $this->reverb = new Process(
        [PHP_BINARY, 'artisan', 'reverb:start', "--port={$this->port}", '--debug'],
        base_path(),
        $env
    );
    $this->reverb->start();

    /* Queue worker */
    $this->worker = new Process(
        [PHP_BINARY, 'artisan', 'queue:work', '--sleep=0', '--quiet', '--tries=0'],
        base_path(),
        $env
    );
    $this->worker->start();

    // Wait until the banner appears
    while (! str_contains($this->reverb->getIncrementalOutput(), "Starting server on 0.0.0.0:{$this->port}")) {
        usleep(100_000);
    }
});

afterEach(function () {
    $this->reverb?->stop();
    $this->worker?->stop();
});

/**
 * Actual test ─────────────────────────────────────────────────────────
 */
it('prompt goes all the way to llm.result over the wire', function () {
    $port    = $this->port;
    $key     = env('REVERB_APP_KEY', 'local');
    $chan    = 'private-analysis';
    $payload = ['id' => 'e2e-1', 'prompt' => 'laravel websockets'];

    /* 1️⃣  Establish WebSocket connection */
    $ws = new Client("ws://127.0.0.1:{$port}/app/{$key}?protocol=7&client=php-e2e&version=1&flash=false");

    $established = json_decode($ws->receive(), true);
    $socketId = json_decode($established['data'], true)['socket_id'];

    $response = $this->postJson(
        '/broadcasting/auth',
        ['socket_id' => $socketId, 'channel_name' => $chan],// and whatever cookies/headers you need
    );

    $auth = $response->json('auth');

    /* 3️⃣  Subscribe */
    $ws->send(json_encode([
        'event' => 'pusher:subscribe',
        'data'  => ['channel' => $chan, 'auth' => $auth],
    ]));
    do {
        $ack = json_decode($ws->receive(), true);
    } while ($ack['event'] !== 'pusher_internal:subscription_succeeded');

    $response = $this->postJson('/api/prompt', $payload);
    $response->assertStatus(200);
    $frame = json_decode($ws->receive(), true);

    $result = json_decode($frame['data'], true);
    expect($frame['event'])->toBe('llm.result')
        ->and($result['job_id'])->toBe('e2e-1')
        ->and($result['result'])->toHaveKeys(['summary', 'sentiment', 'keywords']);
});
