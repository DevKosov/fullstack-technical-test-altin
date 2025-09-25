<?php

namespace App\Providers;

use App\Contracts\LlmClient;
use App\Services\FakeLlmClient;
use App\Services\OpenAiLlmClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LlmClient::class, function () {
            return match (config('llm.driver')) {
                'openai' => new OpenAiLlmClient(config('llm.openai.api_key'), config('llm.openai.model')),
                'fake'  => new FakeLlmClient(),
                default   => new FakeLlmClient(),
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
