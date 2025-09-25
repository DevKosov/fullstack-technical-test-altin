<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
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
        'REVERB_SCHEME'        => 'http',
        'REVERB_SERVER_PORT'   => $this->port,
        'REVERB_APP_ID'        => '275151',
        'REVERB_APP_KEY'       => 'local',
        'REVERB_APP_SECRET'    => 'local',
    ]);

    /* Reverb server */
    $this->reverb = new Process(
        [PHP_BINARY, 'artisan', 'reverb:start', "--port={$this->port}", '--debug'],
        base_path(),
        $env
    );
    $this->reverb->start();

    /* Queue worker */
    // $this->worker = new Process(
    //     [PHP_BINARY, 'artisan', 'queue:work', '--sleep=0', '--quiet', '--tries=0'],
    //     base_path(),
    //     $env
    // );
    // $this->worker->start();

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
    $port = $this->port;
    $key  = env('REVERB_APP_KEY', 'local');

    // 1) Connect WS
    $ws = new Client("ws://127.0.0.1:{$port}/app/{$key}?protocol=7&client=php-e2e&version=1&flash=false");
    $established = json_decode($ws->receive(), true);
    $socketId = json_decode($established['data'], true)['socket_id'];

    // 2) Start analysis (server generates id)
    $start = $this->postJson('/api/prompt', ['prompt' => 'laravel websockets'])->assertOk();
    $analysisId = $start->json('analysis_id');

    // 3) Authorize private channel
    $chan = "private-analysis.{$analysisId}";

    $authResp = $this
        ->withHeaders([
            'Accept'             => 'application/json',
            'X-Requested-With'   => 'XMLHttpRequest',
        ])
        ->post('/broadcasting/auth', [
            'socket_id'    => $socketId,
            'channel_name' => $chan,   // e.g. "private-analysis.{uuid}"
        ])
        ->assertOk();

    $payload = $authResp->json();               // now Laravel decodes it for you
    $this->assertIsArray($payload, 'broadcasting/auth did not return JSON');
    $auth = $payload['auth'] ?? null;
    $this->assertIsString($auth, 'Missing auth signature from broadcasting/auth');

    // 4) Subscribe
    $ws->send(json_encode([
        'event' => 'pusher:subscribe',
        'data'  => ['channel' => $chan, 'auth' => $auth],
    ]));
    do {
        $ack = json_decode($ws->receive(), true);
    } while (($ack['event'] ?? null) !== 'pusher_internal:subscription_succeeded');

    $env = array_merge($_SERVER, [
        'APP_ENV'              => 'testing',
        'BROADCAST_CONNECTION' => 'reverb',
        'QUEUE_CONNECTION'     => 'redis',
        'REVERB_HOST'          => '127.0.0.1',
        'REVERB_SCHEME'        => 'http',
        'REVERB_PORT'          => $this->port,
        'REVERB_APP_ID'        => '275151',
        'REVERB_APP_KEY'       => 'local',
        'REVERB_APP_SECRET'    => 'local',
    ]);
    $this->worker = new Process(
        [PHP_BINARY, 'artisan', 'queue:work', '--sleep=0', '--quiet', '--tries=0'],
        base_path(),
        $env
    );
    $this->worker->start();
    // 5) Receive result
    $frame   = json_decode($ws->receive(), true);
    $payload = json_decode($frame['data'], true);

    expect($frame['event'])->toBe('llm.result')
        ->and($payload['job_id'])->toBe($analysisId)
        ->and($payload['result'])->toHaveKeys(['summary', 'sentiment', 'keywords']);
});


afterAll(function () {
    $this->worker?->stop();
    $this->reverb?->stop();
});