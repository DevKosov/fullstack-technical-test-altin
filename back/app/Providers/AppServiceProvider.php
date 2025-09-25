<?php

namespace App\Providers;

use App\Contracts\LlmClient;
use App\Contracts\ModerationClient;
use App\Services\FakeLlmClient;
use App\Services\Moderation\FakeModerationClient;
use App\Services\Moderation\HttpModerationClient;
use App\Services\OpenAiLlmClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        // LLM Client binding
        $this->app->bind(LlmClient::class, function () {
            return match (config('llm.driver')) {
                'openai' => new OpenAiLlmClient(config('llm.openai.api_key'), config('llm.openai.model')),
                'fake'  => new FakeLlmClient(),
                default   => new FakeLlmClient(),
            };
        });

        // LLM Moderation Client binding
        $this->app->bind(ModerationClient::class, function () {
            return match (config('llm-moderation.driver')) {
                'http' => new HttpModerationClient(
                    config('llm-moderation.http.endpoint'),
                    config('llm-moderation.http.api_key')
                ),
                'fake'  => new FakeModerationClient(),
                default   => new FakeModerationClient(),
            };
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
