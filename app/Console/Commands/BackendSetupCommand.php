<?php

namespace App\Console\Commands;

use App\Enums\RolesEnum;
use App\Models\User;
use Database\Seeders\PermissionsRolesTablesSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BackendSetupCommand extends Command
{
    protected $signature = 'backend:setup
                            {--email= : Email of the backend user}
                            {--name= : Name of the backend user}
                            {--password= : Password for the user (optional)}';

    protected $description = 'Setup the backend panel: migrate, seed roles, create the first user';

    public function handle(): void
    {
        $this->info('🔧 Running migrations...');
        $this->call('migrate');

        $this->info('🌱 Seeding roles and permissions...');
        $this->call(PermissionsRolesTablesSeeder::class);

        $email = $this->option('email') ?? $this->ask('Email');

        if (User::where('email', $email)->exists()) {
            $this->error('User already exists with this email.');

            return;
        }

        $name = $this->option('name') ?? $this->ask('Name');
        $password = $this->option('password') ?? Str::password(12);

        /** @var User $user */
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'force_renew_password' => ! $this->option('password'),
        ]);

        $user->markEmailAsVerified();

        $user->assignRole(RolesEnum::BACKEND->value);

        $this->info("✅ User [$email] created with 'Backend' role.");

        $this->warn("Temporary password: $password");
        $this->warn("Don't forget to update it after login.");
    }
}
