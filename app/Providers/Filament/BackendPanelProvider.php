<?php

namespace App\Providers\Filament;

use App\Enums\FilamentPanelEnum;
use App\Filament\Backend\Pages\Profile;
use App\Http\Middleware\EnsureBackendUserMiddleware;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

class BackendPanelProvider extends PanelProvider
{
    /**
     * @throws \Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id(FilamentPanelEnum::BACKEND->value)
            ->default()
            ->domain(config('project.filament.panels.backend.domain'))
            ->path(config('project.filament.panels.backend.path'))
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/backend/theme.css')
            ->databaseTransactions()
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Backend/Resources'), for: 'App\\Filament\\Backend\\Resources')
            ->discoverPages(in: app_path('Filament/Backend/Pages'), for: 'App\\Filament\\Backend\\Pages')
            ->profile(Profile::class)
            ->userMenuItems([
                Action::make('log-viewer')
                    ->label(__('navigations.backend.logs'))
                    ->url(fn (): string => '/log-viewer', true)
                    ->icon('heroicon-o-cog'),
                Action::make('horizon')
                    ->label('Horizon')
                    ->url(fn (): string => '/horizon', true)
                    ->icon('heroicon-o-queue-list'),
            ])
            ->login()
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable(),
            ])
            ->emailVerification()
            ->passwordReset()
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Backend/Widgets'), for: 'App\\Filament\\Backend\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugin(
                (new RenewPasswordPlugin)
                    ->forceRenewPassword()
                    ->timestampColumn()
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureBackendUserMiddleware::class,
            ]);
    }
}
