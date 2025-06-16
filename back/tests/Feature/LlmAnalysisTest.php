<?php

use App\Actions\LlmAnalysis;
use App\Events\LlmAnalysisDone;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Event::fake();
});

it('should dispatch an LlmAnalysisDone event', function () {
    $jobId = 'job-id';
    $prompt = 'This is a test prompt.';

    LlmAnalysis::run($jobId, $prompt);

    Event::assertDispatched(LlmAnalysisDone::class, function (LlmAnalysisDone $event) use ($jobId) {
        expect($event->broadcastOn()->name)->toBe('private-analysis')
            ->and($event->broadcastAs())->toBe('llm.result')
            ->and($event->jobId)->toBe($jobId)
            ->and($event->result)->toHaveKeys(['summary', 'sentiment', 'keywords']);

        return true;
    });
});
