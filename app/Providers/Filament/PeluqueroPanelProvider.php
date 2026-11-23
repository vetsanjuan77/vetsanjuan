<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\PeluqueroMiddleware;

class PeluqueroPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('peluquero')
            ->path('peluquero')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Peluquero/Resources'), for: 'App\\Filament\\Peluquero\\Resources')
            ->discoverPages(in: app_path('Filament/Peluquero/Pages'), for: 'App\\Filament\\Peluquero\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Peluquero/Widgets'), for: 'App\\Filament\\Peluquero\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                PeluqueroMiddleware::class,
            ])
            ->brandName('VetSanJuan - Panel Peluquero')
            ->brandLogo(asset('images/favicon.png'))
            ->brandLogoHeight("60px")
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make('Mi Trabajo')
                    ->label('Mi Trabajo')
                    ->icon('heroicon-o-briefcase')
                    ->collapsed(false),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->spa();
    }
}
