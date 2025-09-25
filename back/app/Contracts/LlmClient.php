<?php

// app/Contracts/LlmClient.php
namespace App\Contracts;

interface LlmClient
{
   
      /**
      * Analyze the given prompt and return structured data.
      *
      * @param string $prompt The text prompt to analyze.
      */
   public function analyze(string $prompt): array;
}
