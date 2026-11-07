<?php

use App\Enums\PermissionEnum;
use App\Models\User;
use App\Services\PermissionService;

test('gets a good permission name for model', function () {
    $permissionService = app(PermissionService::class);

    $permissionName = $permissionService->getPermissionName(PermissionEnum::VIEW_ANY, User::class);

    expect($permissionName)->toBe('view-any user');
});
