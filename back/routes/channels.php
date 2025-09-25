<?php

use App\Models\Analysis;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('analysis.{analysisId}', function ($user, string $analysisId) {
    // Only allow the owner to subscribe
    return Analysis::whereKey($analysisId)
        ->where('user_id', $user->id)
        ->exists();
});