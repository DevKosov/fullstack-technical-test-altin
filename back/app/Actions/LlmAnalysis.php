<?php

namespace App\Actions;

use App\Events\LlmAnalysisDone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class LlmAnalysis
{
    use AsAction;

    public function asController(Request $request): JsonResponse
    {
        $this->dispatch(
            $request->input('id'),
            $request->input('prompt')
        );

        return response()->json(['status' => 'ok']);
    }

    public function handle(string $jobId, string $prompt): void
    {
        $result = [
            'summary' => '…',
            'sentiment' => 'neutral',
            'keywords' => ['microservices'],
            'tokens' => 12,
            'duration_ms' => 2048,
        ];

        LlmAnalysisDone::dispatch($jobId, $result);
    }
}
