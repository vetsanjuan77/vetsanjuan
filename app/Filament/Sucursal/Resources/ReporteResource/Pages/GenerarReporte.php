<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Pages;

use App\Filament\Sucursal\Resources\ReporteResource;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerarReporte extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ReporteResource::class;

    protected static string $view = 'filament.sucursal.resources.reporte-resource.pages.generar-reporte';

    protected static ?string $title = 'Generar Reporte';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'tipo_reporte' => 'ventas_diarias',
            'periodo' => 'hoy',
            'fecha_inicio' => now()->startOfDay(),
            'fecha_fin' => now()->endOfDay(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración del Reporte')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('tipo_reporte')
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
                                    ->native(false)
                                    ->live(),

                                Forms\Components\Select::make('periodo')
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
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        match ($state) {
                                            'hoy' => [
                                                $set('fecha_inicio', now()->startOfDay()),
                                                $set('fecha_fin', now()->endOfDay()),
                                            ],
                                            'ayer' => [
                                                $set('fecha_inicio', now()->subDay()->startOfDay()),
                                                $set('fecha_fin', now()->subDay()->endOfDay()),
                                            ],
                                            'esta_semana' => [
                                                $set('fecha_inicio', now()->startOfWeek()),
                                                $set('fecha_fin', now()->endOfWeek()),
                                            ],
                                            'semana_pasada' => [
                                                $set('fecha_inicio', now()->subWeek()->startOfWeek()),
                                                $set('fecha_fin', now()->subWeek()->endOfWeek()),
                                            ],
                                            'este_mes' => [
                                                $set('fecha_inicio', now()->startOfMonth()),
                                                $set('fecha_fin', now()->endOfMonth()),
                                            ],
                                            'mes_pasado' => [
                                                $set('fecha_inicio', now()->subMonth()->startOfMonth()),
                                                $set('fecha_fin', now()->subMonth()->endOfMonth()),
                                            ],
                                            default => null,
                                        };
                                    }),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('fecha_inicio')
                                    ->label('Fecha y Hora Inicio')
                                    ->required()
                                    ->seconds(false),

                                Forms\Components\DateTimePicker::make('fecha_fin')
                                    ->label('Fecha y Hora Fin')
                                    ->required()
                                    ->seconds(false),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('generar')
                ->label('Generar Reporte')
                ->icon('heroicon-o-document-chart-bar')
                ->color('primary')
                ->action('generarReporte'),

            Action::make('exportar_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('secondary')
                ->action('exportarPDF'),

            Action::make('exportar_excel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action('exportarExcel'),
        ];
    }

    public function generarReporte(): void
    {
        $data = $this->form->getState();
        
        $fechaInicio = Carbon::parse($data['fecha_inicio']);
        $fechaFin = Carbon::parse($data['fecha_fin']);
        
        $resultado = match ($data['tipo_reporte']) {
            'ventas_diarias' => $this->generarReporteVentasDiarias($fechaInicio, $fechaFin),
            'productos_vendidos' => $this->generarReporteProductosVendidos($fechaInicio, $fechaFin),
            'flujo_caja' => $this->generarReporteFlujoCaja($fechaInicio, $fechaFin),
            'ingresos_gastos' => $this->generarReporteIngresosGastos($fechaInicio, $fechaFin),
            default => ['error' => 'Tipo de reporte no implementado'],
        };

        if (isset($resultado['error'])) {
            Notification::make()
                ->title('Error al generar reporte')
                ->body($resultado['error'])
                ->danger()
                ->send();
        } else {
            Notification::make()
                ->title('Reporte generado exitosamente')
                ->body('El reporte se ha generado con ' . count($resultado) . ' registros.')
                ->success()
                ->send();
        }
    }

    private function generarReporteVentasDiarias($fechaInicio, $fechaFin): array
    {
        return Factura::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with(['cliente', 'sucursal'])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total_facturas, SUM(total) as total_ventas')
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->get()
            ->toArray();
    }

    private function generarReporteProductosVendidos($fechaInicio, $fechaFin): array
    {
        return DB::table('detalle_facturas')
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->whereBetween('facturas.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_facturas.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_facturas.subtotal) as ingresos_totales')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function generarReporteFlujoCaja($fechaInicio, $fechaFin): array
    {
        return MovimientoCaja::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with(['caja', 'usuario'])
            ->selectRaw('
                DATE(created_at) as fecha,
                tipo_movimiento,
                SUM(monto) as total_monto,
                COUNT(*) as total_movimientos
            ')
            ->groupBy('fecha', 'tipo_movimiento')
            ->orderBy('fecha', 'desc')
            ->get()
            ->toArray();
    }

    private function generarReporteIngresosGastos($fechaInicio, $fechaFin): array
    {
        $ingresos = Factura::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, SUM(total) as total_ingresos')
            ->groupBy('fecha')
            ->get()
            ->keyBy('fecha');

        $gastos = Gasto::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total_gastos')
            ->groupBy('fecha')
            ->get()
            ->keyBy('fecha');

        $resultado = [];
        $todasFechas = collect($ingresos->keys())->merge($gastos->keys())->unique();

        foreach ($todasFechas as $fecha) {
            $resultado[] = [
                'fecha' => $fecha,
                'ingresos' => $ingresos->get($fecha)?->total_ingresos ?? 0,
                'gastos' => $gastos->get($fecha)?->total_gastos ?? 0,
                'utilidad' => ($ingresos->get($fecha)?->total_ingresos ?? 0) - ($gastos->get($fecha)?->total_gastos ?? 0),
            ];
        }

        return collect($resultado)->sortByDesc('fecha')->values()->toArray();
    }

    public function exportarPDF(): void
    {
        Notification::make()
            ->title('Función en desarrollo')
            ->body('La exportación a PDF estará disponible próximamente.')
            ->warning()
            ->send();
    }

    public function exportarExcel(): void
    {
        Notification::make()
            ->title('Función en desarrollo')
            ->body('La exportación a Excel estará disponible próximamente.')
            ->warning()
            ->send();
    }
}