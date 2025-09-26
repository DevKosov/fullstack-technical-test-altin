<?php

namespace App\Actions;

use App\Contracts\LlmClient;
use App\Contracts\ModerationClient;
use App\Events\LlmAnalysisDone;
use App\Models\Analysis;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class LlmAnalysis
{
    use AsAction;

    public function __construct(private LlmClient $llm, private ModerationClient $moderation) {}

    public function asController(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $jobId =  (string) Str::uuid();

        Analysis::create([
            'id'      => $jobId,
            'user_id' => $request->user()->id,
        ]);

        $this->dispatch($jobId, (string) $validated['prompt']);

        return response()->json(['status' => 'ok', 'analysis_id' => $jobId]);
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

            if (! app()->environment('testing')) {
                report($e);
            }

            return;
        }
    }
}
