<?php

use App\Enums\RolesEnum;
use App\Filament\Backend\Resources\BackendUsers\BackendUserResource;
use App\Filament\Backend\Resources\BackendUsers\Pages\CreateBackendUser;
use App\Filament\Backend\Resources\BackendUsers\Pages\EditBackendUser;
use App\Filament\Backend\Resources\BackendUsers\Pages\ListBackendUsers;
use App\Models\User;
use App\Notifications\Backend\BackendUser\Invite;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

test('cannot access backend users list without auth', function () {
    get(BackendUserResource::getUrl())
        ->assertRedirect(filament()->getLoginUrl());
});

test('cannot access backend users list without correct role', function () {
    $user = $this->getBackendUser();
    $user->removeRole(RolesEnum::BACKEND->value);

    actingAs($user)
        ->get(BackendUserResource::getUrl())
        ->assertForbidden();
});

test('can access backend users list with backend role', function () {
    $user = $this->getBackendUser();

    actingAs($user)
        ->get(BackendUserResource::getUrl())
        ->assertSeeLivewire(ListBackendUsers::class)
        ->assertSuccessful();
});

it('can delete backend user', function () {
    $user = $this->getBackendUser();
    $newUser = $this->getBackendUser();

    actingAs($user);

    livewire(ListBackendUsers::class)
        ->callAction(TestAction::make('delete')->table($newUser));

    $this->assertModelMissing($newUser);
});

it('can access create backend user page', function () {
    $user = $this->getBackendUser();

    actingAs($user)
        ->get(BackendUserResource::getUrl('create'))
        ->assertSeeLivewire(CreateBackendUser::class)
        ->assertSuccessful();
});

it('can validate input on create backend user page', function () {
    $user = $this->getBackendUser();

    actingAs($user);

    livewire(CreateBackendUser::class)
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'required',
            'timezone' => 'required',
        ]);
});

it('can create backend user and send invitation', function () {
    Notification::fake();

    $user = $this->getBackendUser();
    $newUser = User::factory()->make();

    actingAs($user);

    livewire(CreateBackendUser::class)
        ->fillForm([
            'name' => $newUser->name,
            'email' => $newUser->email,
            'timezone' => $newUser->timezone,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('users', [
        'name' => $newUser->name,
        'email' => $newUser->email,
        'timezone' => $newUser->timezone,
    ]);

    Notification::assertSentTo(User::firstWhere('email', $newUser->email), Invite::class);
});

it('can create unique backend user email', function () {
    $user = $this->getBackendUser();
    $newUser = User::factory()->make();

    actingAs($user);

    livewire(CreateBackendUser::class)
        ->fillForm([
            'name' => $newUser->name,
            'email' => $user->email,
            'timezone' => $newUser->timezone,
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'unique']);
});

it('can access edit backend user page', function () {
    $user = $this->getBackendUser();

    actingAs($user)
        ->get(BackendUserResource::getUrl('edit', [$user]))
        ->assertSeeLivewire(EditBackendUser::class)
        ->assertSuccessful();
});
