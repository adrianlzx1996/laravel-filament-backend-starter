<?php

namespace Tests;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function getBackendUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->roles()->attach(Role::query()->firstWhere('name', RolesEnum::BACKEND));

        return $user;
    }
}
