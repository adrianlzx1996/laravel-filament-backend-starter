<?php

namespace Tests\Feature\BackendPanel;

use App\Enums\FilamentPanelEnum;
use Database\Seeders\PermissionsRolesTablesSeeder;
use Filament\Facades\Filament;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsRolesTablesSeeder::class);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Filament::setCurrentPanel(
            Filament::getPanel(FilamentPanelEnum::BACKEND->value)
        );
    }
}
