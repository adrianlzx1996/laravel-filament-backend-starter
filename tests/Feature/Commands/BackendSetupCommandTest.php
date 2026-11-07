<?php

use App\Enums\RolesEnum;
use App\Models\User;

it('creates a backend user via artisan command', function () {
    $creatingUser = User::factory()->make();

    Artisan::call('backend:setup', [
        '--email' => $creatingUser->email,
        '--name' => $creatingUser->name,
        '--password' => $creatingUser->password,
    ]);

    $user = User::query()->where('email', $creatingUser->email)->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe($creatingUser->name)
        ->and(Hash::check($creatingUser->password, $user->password))->toBeTrue()
        ->and($user->hasRole(RolesEnum::BACKEND->value))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->force_renew_password)->toBeFalse();
});

it('creates a backend user and generates a random password if none is provided', function () {
    $creatingUser = User::factory()->make();

    Artisan::call('backend:setup', [
        '--email' => $creatingUser->email,
        '--name' => $creatingUser->name,
    ]);

    $user = User::query()->where('email', $creatingUser->email)->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe($creatingUser->name)
        ->and($user->force_renew_password)->toBeTrue();
});

it('fails if user with same email already exists', function () {
    $createdUser = User::factory()->create();
    $creatingUser = User::factory()->make(['email' => $createdUser->email]);

    Artisan::call('backend:setup', [
        '--email' => $creatingUser->email,
        '--name' => $creatingUser->name,
        '--password' => $creatingUser->password,
    ]);

    $this->assertDatabaseCount('users', 1);
});
