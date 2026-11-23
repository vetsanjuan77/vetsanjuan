<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Factura;
use App\Models\Gasto;
use App\Models\Compra;
use App\Models\MovimientoCaja;

class ReportesFinancierosController extends Controller
{
    public function estadoResultados(Request $request)
    {
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');

        $desde = $desde ? Carbon::parse($desde)->startOfDay() : Carbon::now()->startOfMonth();
        $hasta = $hasta ? Carbon::parse($hasta)->endOfDay() : Carbon::now()->endOfDay();

        $sucursalId = Auth::user()->sucursal_id ?? null;

        $ingresos = Factura::whereBetween('created_at', [$desde, $hasta])
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->sum('total');

        // Aproximación de Costo de Ventas usando compras del período
        $costoVentas = Compra::whereBetween('created_at', [$desde, $hasta])
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->sum('total');

        $gastos = Gasto::whereBetween('created_at', [$desde, $hasta])
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->sum(DB::raw('COALESCE(valor, monto)'));

        $utilidadBruta = $ingresos - $costoVentas;
        $utilidadNeta = $utilidadBruta - $gastos;

        return view('reports.estado_resultados', [
            'desde' => $desde,
            'hasta' => $hasta,
            'ingresos' => (float) $ingresos,
            'costoVentas' => (float) $costoVentas,
            'gastos' => (float) $gastos,
            'utilidadBruta' => (float) $utilidadBruta,
            'utilidadNeta' => (float) $utilidadNeta,
        ]);
    }

    public function balanceGeneral(Request $request)
    {
        $corte = $request->get('corte');
        $corte = $corte ? Carbon::parse($corte)->endOfDay() : Carbon::now()->endOfDay();
        $sucursalId = Auth::user()->sucursal_id ?? null;

        // Caja: ingresos - egresos hasta la fecha de corte
        $ingresosCaja = MovimientoCaja::where('fecha_movimiento', '<=', $corte)
            ->when($sucursalId, fn($q) => $q->whereHas('caja', fn($q2) => $q2->where('sucursal_id', $sucursalId)))
            ->where('tipo', 'ingreso')
            ->sum('valor');

        $egresosCaja = MovimientoCaja::where('fecha_movimiento', '<=', $corte)
            ->when($sucursalId, fn($q) => $q->whereHas('caja', fn($q2) => $q2->where('sucursal_id', $sucursalId)))
            ->where('tipo', 'egreso')
            ->sum('valor');

        $caja = (float) ($ingresosCaja - $egresosCaja);

        // Cuentas por cobrar: facturas pendientes hasta la fecha
        $cuentasPorCobrar = Factura::where('created_at', '<=', $corte)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('estado', 'pendiente')
            ->sum('total');

        // Inventario: stock * precio_compra por presentación y sucursal
        $inventarioValor = DB::table('presentacion_sucursal')
            ->join('presentaciones', 'presentaciones.id', '=', 'presentacion_sucursal.presentacion_id')
            ->join('sucursales', 'sucursales.id', '=', 'presentacion_sucursal.sucursal_id')
            ->when($sucursalId, fn($q) => $q->where('presentacion_sucursal.sucursal_id', $sucursalId))
            ->selectRaw('SUM(COALESCE(presentacion_sucursal.stock,0) * COALESCE(presentacion_sucursal.precio_compra,0)) as total_valor')
            ->value('total_valor');

        $inventarioValor = (float) ($inventarioValor ?? 0);

        // Cuentas por pagar: compras pendientes hasta la fecha
        $cuentasPorPagar = Compra::where('created_at', '<=', $corte)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('estado', 'pendiente')
            ->sum('total');

        $activos = $caja + $cuentasPorCobrar + $inventarioValor;
        $pasivos = (float) $cuentasPorPagar;
        $patrimonio = $activos - $pasivos;

        return view('reports.balance_general', [
            'corte' => $corte,
            'caja' => (float) $caja,
            'cuentasPorCobrar' => (float) $cuentasPorCobrar,
            'inventarioValor' => (float) $inventarioValor,
            'cuentasPorPagar' => (float) $cuentasPorPagar,
            'activos' => (float) $activos,
            'pasivos' => (float) $pasivos,
            'patrimonio' => (float) $patrimonio,
        ]);
    }
}