<?php

namespace App\Providers;

use App\Enums\RolesEnum;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Amber,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        Gate::define('viewLogViewer', function (?User $user) {
            if ($user && $user->hasRole(RolesEnum::BACKEND->value)) {
                return true;
            }

            return false;
        });

        DateTimePicker::configureUsing(function (DateTimePicker $component): void {
            $component->timezone(auth()->user() ? auth()->user()->timezone : config('app.timezone'));
        });

        TextColumn::configureUsing(function (TextColumn $component): void {
            if (in_array($component->getName(), ['created_at', 'updated_at', 'email_verified_at'])) {
                $component->timezone(auth()->user() ? auth()->user()->timezone : config('app.timezone'))
                    ->dateTime('d/m/Y H:i');
            }
        });

        Gate::after(function (User $user, string $ability, ?bool $result, mixed $arguments) {
            if ($result !== false && $user->hasRole(RolesEnum::BACKEND->value)) {
                return true;
            }

            return is_null($result) ? false : $result;
        });
    }
}
