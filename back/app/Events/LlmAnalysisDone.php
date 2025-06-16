<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LlmAnalysisDone implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * @param string $jobId The ID of the LLM job.
     * @param array $result Complex JSON payload produced by FakeLLMAnalysis.
     */
    public function __construct(
        public string $jobId,
        public string  $result,
    )
    {
    }

    /**
     * Reverb / Pusher channel to broadcast on.
     *
     * Returning a PrivateChannel named “analysis” makes the actual wire-level
     * channel `private-analysis`, matching the client subscription:
     *
     *   Echo.private('analysis').listen('llm.result', …)
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('analysis');
    }

    /**
     * Custom event name (Pusher style).  Clients listen for “llm.result”.
     */
    public function broadcastAs(): string
    {
        return 'llm.result';
    }

    /**
     * Payload the client receives.
     */
    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'result' => $this->result,
        ];
    }
}
