<?php

namespace Tests\Feature\BackendPanel;

use App\Enums\RolesEnum;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

/** LOGIN */
it('can render login page', function () {
    get(Filament::getLoginUrl())
        ->assertSuccessful();
});

it('can login with validated data', function () {
    $user = $this->getBackendUser();

    livewire(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertSuccessful()
        ->assertHasNoFormErrors()
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticated(Filament::getAuthGuard());
});

it('cannot login with unvalidated data', function () {
    $user = $this->getBackendUser();

    livewire(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password1',
        ])
        ->call('authenticate')
        ->assertSuccessful()
        ->assertHasFormErrors();
});

it('cannot login without backend role', function () {
    $user = $this->getBackendUser();
    $user->removeRole(RolesEnum::BACKEND->value);

    livewire(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password1',
        ])
        ->call('authenticate')
        ->assertSuccessful()
        ->assertHasFormErrors(['email']);
});

/** Registration */
it('cannot render register page', function () {
    expect(Filament::getRegistrationUrl())->toBeNull();
});

/** Reset Password */
it('can render request password reset page', function () {
    get(Filament::getRequestPasswordResetUrl())
        ->assertSuccessful();
});
