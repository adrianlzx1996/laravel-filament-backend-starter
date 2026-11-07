<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function __construct(protected PermissionService $permissionService) {}

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo($this->permissionService->getPermissionName(PermissionEnum::VIEW_ANY, User::class));
    }

    public function create(User $user): ?bool
    {
        return $user->hasPermissionTo($this->permissionService->getPermissionName(PermissionEnum::CREATE, User::class));
    }

    public function update(User $user, User $model): ?bool
    {
        return $user->hasPermissionTo($this->permissionService->getPermissionName(PermissionEnum::UPDATE, User::class));
    }

    public function delete(User $user, User $model): ?bool
    {
        return $user->hasPermissionTo($this->permissionService->getPermissionName(PermissionEnum::DELETE, User::class));
    }

    public function deleteAny(User $user): ?bool
    {
        return $user->hasPermissionTo($this->permissionService->getPermissionName(PermissionEnum::DELETE_ANY, User::class));
    }
}
