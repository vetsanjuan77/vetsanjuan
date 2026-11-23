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
use App\Http\Middleware\VeterinarioMiddleware;

class VeterinarioPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('veterinario')
            ->path('veterinario')
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Veterinario/Resources'), for: 'App\\Filament\\Veterinario\\Resources')
            ->discoverPages(in: app_path('Filament/Veterinario/Pages'), for: 'App\\Filament\\Veterinario\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Veterinario/Widgets'), for: 'App\\Filament\\Veterinario\\Widgets')
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
                VeterinarioMiddleware::class,
            ])
            ->brandName('VetSanJuan - Panel Veterinario')
            ->brandLogo(asset('images/favicon.png'))
            ->brandLogoHeight("60px")
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make('Mi Trabajo')
                    ->label('Mi Trabajo')
                    ->icon('heroicon-o-briefcase')
                    ->collapsed(false),
                NavigationGroup::make('Pacientes')
                    ->label('Pacientes')
                    ->icon('heroicon-o-heart')
                    ->collapsed(false),
                NavigationGroup::make('Consultas Médicas')
                    ->label('Consultas Médicas')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsed(false),
                NavigationGroup::make('Procedimientos')
                    ->label('Procedimientos')
                    ->icon('heroicon-o-scissors')
                    ->collapsed(true),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->spa();
    }
}
