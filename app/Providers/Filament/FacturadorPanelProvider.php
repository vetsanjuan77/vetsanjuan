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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Facturador\Resources\FacturaResource;
use App\Filament\Facturador\Resources\CotizacionResource;
use App\Filament\Facturador\Resources\ClienteResource;
use App\Filament\Facturador\Resources\CajaResource;
use App\Filament\Facturador\Resources\MovimientoCajaResource;

class FacturadorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('facturador')
            ->path('facturador')
            ->authGuard('web')
            ->brandName('Facturador')
            ->colors([
                'primary' => Color::Amber,
            ])
            // Registrar explícitamente los recursos permitidos para evitar inventario u otros módulos
            ->resources([
                FacturaResource::class,
                CotizacionResource::class,
                ClienteResource::class,
                CajaResource::class,
                MovimientoCajaResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Facturador/Pages'), for: 'App\\Filament\\Facturador\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Facturador/Widgets'), for: 'App\\Filament\\Facturador\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                \App\Http\Middleware\FacturadorMiddleware::class,
            ]);
    }
}
