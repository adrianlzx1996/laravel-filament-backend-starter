<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use Illuminate\Support\Str;

class PermissionService
{
    public function getPermissionName(PermissionEnum $permission, string $class): string
    {
        $className = Str::kebab(class_basename($class));

        return $permission->value . ' ' . $className;
    }
}
