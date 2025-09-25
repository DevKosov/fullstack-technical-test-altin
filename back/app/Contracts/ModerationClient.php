<?php

namespace App\Contracts;

use App\DTO\ModerationResult;

interface ModerationClient
{
    /**
     * Check the given prompt for any potentially harmful content.
     *
     * @param string $prompt The prompt to check.
     * @return ModerationResult The moderation results.
     */
    public function moderate(string $prompt): ModerationResult;
}
