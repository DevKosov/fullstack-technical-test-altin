<?php

use App\Models\User;

beforeEach(function() {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
});

it('should login successfully', function () {
    $response = $this->postJson('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);
});
