<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'SecureUiakPassword123!',
            'password_confirmation' => 'SecureUiakPassword123!',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('SecureUiakPassword123!', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'SecureUiakPassword123!',
            'password_confirmation' => 'SecureUiakPassword123!',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});
