<?php

return [
   'driver' => env('LLM_DRIVER', 'fake'), // 'fake' or 'openai'
   'openai' => [
       'api_key' => env('OPENAI_API_KEY', ''),
       'model' => env('OPENAI_MODEL', ''),
   ],
];