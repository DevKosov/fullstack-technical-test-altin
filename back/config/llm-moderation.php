<?php

return [
   'driver'   => env('MODERATION_DRIVER', 'http'),
   'http'     => [
      'endpoint' => env('MODERATION_ENDPOINT', ''),
      'api_key'  => env('MODERATION_API_KEY'),
   ],
];
