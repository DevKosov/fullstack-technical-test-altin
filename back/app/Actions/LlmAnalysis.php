<?php

namespace App\Actions;

use App\Contracts\LlmClient;
use App\Events\LlmAnalysisDone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class LlmAnalysis
{
    use AsAction;


    public function __construct(private LlmClient $llm) {}

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
        
        $result = $this->llm->analyze($prompt);

        LlmAnalysisDone::dispatch($jobId, $result);
    }
}
