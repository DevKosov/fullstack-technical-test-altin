<?php

namespace App\DTO;


class ModerationResult
{
   public bool $isSafe;
   public array $violations;

   public function __construct(bool $isSafe, array $violations = []) {
      $this->isSafe = $isSafe;
      $this->violations = $violations;
   }
}