<?php

namespace App\Services\Moderation;

use App\Contracts\ModerationClient;
use App\DTO\ModerationResult;
use Illuminate\Support\Facades\Http;

class HttpModerationClient implements ModerationClient
{

   private string $endpoint;
   private ?string $apiKey;
   public function __construct(string $endpoint, ?string $apiKey = null) {
      $this->endpoint = $endpoint;
      $this->apiKey = $apiKey;
   }

   public function moderate(string $prompt): ModerationResult
   {
      $resp = Http::withHeaders($this->headers())
         ->post($this->endpoint, ['prompt' => $prompt])
         ->throw();

      $data = $resp->json();

      return new ModerationResult(
         isSafe: (bool)($data['allowed'] ?? false),
         violations: $data['violations'] ?? []
      );
   }

   private function headers(): array
   {
      return array_filter([
         'Authorization' => $this->apiKey ? "Bearer {$this->apiKey}" : null,
         'Accept'        => 'application/json',
      ]);
   }
}
