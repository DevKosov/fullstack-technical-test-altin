<?php

namespace App\Actions;

use App\Contracts\LlmClient;
use App\Contracts\ModerationClient;
use App\Events\LlmAnalysisDone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class LlmAnalysis
{
    use AsAction;

    public function __construct(private LlmClient $llm, private ModerationClient $moderation) {}

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
        try {
            $mod = $this->moderation->moderate($prompt);

            if (!$mod->isSafe) {
                LlmAnalysisDone::dispatch($jobId, [
                    'status'  => 'error',
                    'error'   => 'Prompt rejected by moderation',
                    'violations' => $mod->violations,
                ]);
                return;
            }

            $result = $this->llm->analyze($prompt);

            LlmAnalysisDone::dispatch($jobId, $result);
        } catch (\Throwable $e) {
            LlmAnalysisDone::dispatch($jobId, [
                'status'  => 'error',
                'error'   => $e->getMessage(), 
            ]);
            report($e);
            return;
        }
    }
}
