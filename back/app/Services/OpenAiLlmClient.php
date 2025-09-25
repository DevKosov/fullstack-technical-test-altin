<?php

// app/Services/Llm/OpenAiLlmClient.php
namespace App\Services;

use App\Contracts\LlmClient;

class OpenAiLlmClient implements LlmClient
{

   private string $apiKey;
   private string $model;
   public function __construct(string $apiKey, string $model) {
      $this->apiKey = $apiKey;
      $this->model = $model;
   }

   public function analyze(string $prompt): array
   {
      
      return ['summary' => '…', 'model' => $this->model];
   }
}
