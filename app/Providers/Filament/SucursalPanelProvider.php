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

class SucursalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('sucursal')
            ->path('sucursal')
            ->authGuard('web')
            ->brandName('Sistema POS - Sucursal')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('60px')
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'danger' => Color::Red,
            ])
            ->navigationGroups([
                'Ventas y Facturación',
                'Inventario',
                'Compras y Proveedores',
                'Caja y Finanzas',
                'Clientes',
                'Informes',
                'Configuración'
            ])
            ->discoverResources(in: app_path('Filament/Sucursal/Resources'), for: 'App\\Filament\\Sucursal\\Resources')
            ->discoverPages(in: app_path('Filament/Sucursal/Pages'), for: 'App\\Filament\\Sucursal\\Pages')
            ->pages([
                \App\Filament\Sucursal\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Sucursal/Widgets'), for: 'App\\Filament\\Sucursal\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Sucursal\Widgets\EstadisticasSucursalWidget::class,
                \App\Filament\Sucursal\Widgets\VentasChartWidget::class,
                \App\Filament\Sucursal\Widgets\ProductosTopWidget::class,
                \App\Filament\Sucursal\Widgets\IngresosVsGastosWidget::class,
                \App\Filament\Sucursal\Widgets\AlertasInventarioWidget::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
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
                \App\Http\Middleware\SucursalMiddleware::class,
            ]);
    }
}
