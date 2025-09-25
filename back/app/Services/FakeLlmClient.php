<?php

// app/Services/FakeLlmClient.php
namespace App\Services;

use App\Contracts\LlmClient;

class FakeLlmClient implements LlmClient
{
   public function analyze(string $prompt, array $options = []): array
   {
      $result = [
         'summary' => '…',
         'sentiment' => 'neutral',
         'keywords' => ['microservices'],
         'tokens' => 12,
         'duration_ms' => 2048,
      ];
      return $result;
   }
}
