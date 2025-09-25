<?php

namespace App\Services\Moderation;

use App\Contracts\ModerationClient;
use App\DTO\ModerationResult;

class FakeModerationClient implements ModerationClient
{
    public array $promptInjections = [
        'ignore previous instructions',
        'disregard previous instructions',
        'override system prompt',
        'you are now',
        'pretend to be',
        'jailbreak',
    ];

    public array $dangerousCommands = [
        'rm -rf /',
        '<script',
        'drop table',
        'truncate table',
        'powershell ',
        'invoke-webrequest',
        'curl http',
        'wget http',
    ];

    public function moderate(string $text): ModerationResult
    {
        $t = strtolower($text);
        $violations = [];

        foreach ($this->promptInjections as $needle) {
            if (str_contains($t, $needle)) {
                $violations[] = 'Injection';
                break;
            }
        }

        foreach ($this->dangerousCommands as $needle) {
            if (str_contains($t, $needle)) {
                $violations[] = 'DangerousCommand';
                break;
            }
        }

        if (str_contains($t, 'hate')) {
            $violations[] = 'HateSpeech';
        }
        if (str_contains($t, 'violence')) {
            $violations[] = 'Violence';
        }
        if (str_contains($t, 'self-harm') || str_contains($t, 'suicide')) {
            $violations[] = 'SelfHarm';
        }

        $violations = array_values(array_unique($violations));

        // Simplest policy: ANY violation => block
        return new ModerationResult(
            isSafe: empty($violations),
            violations: $violations
        );
    }
}
