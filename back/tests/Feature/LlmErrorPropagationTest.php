<?php

use App\Actions\LlmAnalysis;
use App\Contracts\LlmClient;
use App\Contracts\ModerationClient;
use App\DTO\ModerationResult;
use App\Events\LlmAnalysisDone;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    // Make sure the job runs inline so the event is emitted immediately
    config(['queue.default' => 'sync']);
    Event::fake();
});

it('broadcasts an error when the LLM client throws', function () {
    // Bind a permissive moderation
    app()->bind(ModerationClient::class, fn () => new class implements ModerationClient {
        public function moderate(string $prompt): ModerationResult {
            return new ModerationResult(isSafe: true, violations: []);
        }
    });

    // Bind an LLM client that throws
    app()->bind(LlmClient::class, fn () => new class implements LlmClient {
        public function analyze(string $prompt, array $options = []): array {
            throw new RuntimeException('Simulated LLM failure');
        }
    });

    $jobId = 'job-err-1';
    $prompt = 'any prompt';

    // Act
    LlmAnalysis::run($jobId, $prompt);


    // Assert: error frame was broadcast
    Event::assertDispatched(LlmAnalysisDone::class, function ($e) {
        return $e->jobId === 'job-err-1'
            && ($e->result['status'] ?? null) === 'error'
            && ($e->result['error'] ?? '') === 'Simulated LLM failure';
    });
});
