<?php

namespace App\Filament\Sucursal\Resources;

use App\Filament\Sucursal\Resources\ReporteResource\Pages;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteResource extends Resource
{
    protected static ?string $model = null; // No tiene modelo específico

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Reportes y Análisis';

    protected static ?string $navigationLabel = 'Reportes POS';

    protected static ?string $modelLabel = 'Reporte';

    protected static ?string $pluralModelLabel = 'Reportes';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Configuración del Reporte')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('tipo_reporte')
                                    ->label('Tipo de Reporte')
                                    ->options([
                                        'ventas_diarias' => 'Ventas Diarias',
                                        'ventas_semanales' => 'Ventas Semanales',
                                        'ventas_mensuales' => 'Ventas Mensuales',
                                        'productos_vendidos' => 'Productos Más Vendidos',
                                        'inventario_stock' => 'Inventario y Stock',
                                        'flujo_caja' => 'Flujo de Caja',
                                        'ingresos_gastos' => 'Ingresos vs Gastos',
                                        'clientes_frecuentes' => 'Clientes Frecuentes',
                                    ])
                                    ->required()
                                    ->native(false),

                                Select::make('periodo')
                                    ->label('Período')
                                    ->options([
                                        'hoy' => 'Hoy',
                                        'ayer' => 'Ayer',
                                        'esta_semana' => 'Esta Semana',
                                        'semana_pasada' => 'Semana Pasada',
                                        'este_mes' => 'Este Mes',
                                        'mes_pasado' => 'Mes Pasado',
                                        'personalizado' => 'Período Personalizado',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('fecha_inicio')
                                    ->label('Fecha Inicio')
                                    ->visible(fn (Forms\Get $get) => $get('periodo') === 'personalizado')
                                    ->required(fn (Forms\Get $get) => $get('periodo') === 'personalizado'),

                                DatePicker::make('fecha_fin')
                                    ->label('Fecha Fin')
                                    ->visible(fn (Forms\Get $get) => $get('periodo') === 'personalizado')
                                    ->required(fn (Forms\Get $get) => $get('periodo') === 'personalizado'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                // Query vacío ya que este recurso es principalmente para generar reportes
                Factura::query()->whereRaw('1 = 0')
            )
            ->columns([
                TextColumn::make('placeholder')
                    ->label('Reportes Disponibles')
                    ->formatStateUsing(fn () => 'Use los filtros para generar reportes específicos')
                    ->weight(FontWeight::Medium),
            ])
            ->filters([
                SelectFilter::make('tipo_reporte')
                    ->label('Tipo de Reporte')
                    ->options([
                        'ventas_diarias' => 'Ventas Diarias',
                        'ventas_semanales' => 'Ventas Semanales',
                        'ventas_mensuales' => 'Ventas Mensuales',
                        'productos_vendidos' => 'Productos Más Vendidos',
                        'inventario_stock' => 'Inventario y Stock',
                        'flujo_caja' => 'Flujo de Caja',
                        'ingresos_gastos' => 'Ingresos vs Gastos',
                    ])
                    ->native(false),

                Filter::make('fecha_rango')
                    ->form([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('desde')
                                    ->label('Desde')
                                    ->default(now()->startOfMonth()),
                                DatePicker::make('hasta')
                                    ->label('Hasta')
                                    ->default(now()),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('primary')
                    ->url(fn () => route('filament.sucursal.resources.reportes.generar')),
            ])
            ->bulkActions([])
            ->emptyStateHeading('Sistema de Reportes POS')
            ->emptyStateDescription('Seleccione los filtros y genere reportes detallados de ventas, inventario y finanzas.')
            ->emptyStateIcon('heroicon-o-chart-bar');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportes::route('/'),
            'generar' => Pages\GenerarReporte::route('/generar'),
            'ventas' => Pages\ReporteVentas::route('/ventas'),
            'inventario' => Pages\ReporteInventario::route('/inventario'),
            'financiero' => Pages\ReporteFinanciero::route('/financiero'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            \Filament\Navigation\NavigationItem::make('Reportes Generales')
                ->url(static::getUrl('index'))
                ->icon('heroicon-o-chart-bar')
                ->group('Reportes y Análisis')
                ->sort(1),
            
            \Filament\Navigation\NavigationItem::make('Reportes de Ventas')
                ->url(static::getUrl('ventas'))
                ->icon('heroicon-o-currency-dollar')
                ->group('Reportes y Análisis')
                ->sort(2),
            
            \Filament\Navigation\NavigationItem::make('Reportes de Inventario')
                ->url(static::getUrl('inventario'))
                ->icon('heroicon-o-cube')
                ->group('Reportes y Análisis')
                ->sort(3),
            
            \Filament\Navigation\NavigationItem::make('Reportes Financieros')
                ->url(static::getUrl('financiero'))
                ->icon('heroicon-o-banknotes')
                ->group('Reportes y Análisis')
                ->sort(4),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}