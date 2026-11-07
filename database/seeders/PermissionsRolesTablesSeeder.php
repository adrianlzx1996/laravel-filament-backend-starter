<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsRolesTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionService = app(PermissionService::class);

        $rolePermissions = [
            RolesEnum::BACKEND->value => [
                User::class => [
                    PermissionEnum::VIEW_ANY,
                    PermissionEnum::CREATE,
                    PermissionEnum::UPDATE,
                    PermissionEnum::DELETE,
                    PermissionEnum::DELETE_ANY,
                ],
            ],
        ];

        $guardName = $this->getGuardName();

        foreach ($rolePermissions as $roleValue => $modelPermissions) {
            $role = Role::query()
                ->updateOrCreate(['name' => $roleValue, 'guard_name' => $guardName]);

            $permissionsToSync = collect();
            foreach ($modelPermissions as $modelClass => $permissions) {

                foreach ($permissions as $permissionValue) {
                    $permissionName = $permissionService->getPermissionName($permissionValue, $modelClass);

                    $permission = Permission::query()
                        ->updateOrCreate([
                            'name' => $permissionName,
                            'guard_name' => $guardName,
                        ]);

                    $permissionsToSync->push($permission->getKey());
                }
            }
            $role->permissions()->sync($permissionsToSync);
        }
    }

    private function getGuardName(): string
    {
        return 'web';
    }
}
