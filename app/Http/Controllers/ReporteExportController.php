<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteFinancieroExport;
use App\Exports\ReporteInventarioExport;

class ReporteExportController
{
    public function financieroPdf()
    {
        $data = [
            'fecha' => now(),
            'ingresos' => null,
            'gastos' => null,
            'utilidad' => null,
            'items' => [],
        ];

        $html = view('reports.financiero', compact('data'))->render();
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

        return Pdf::loadHTML($html)->download('reporte_financiero.pdf');
    }

    public function inventarioPdf()
    {
        $data = [
            'fecha' => now(),
            'items' => [],
        ];

        $html = view('reports.inventario', compact('data'))->render();
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

        return Pdf::loadHTML($html)->download('reporte_inventario.pdf');
    }

    public function financieroExcel()
    {
        $data = [
            'fecha' => now(),
            'ingresos' => null,
            'gastos' => null,
            'utilidad' => null,
            'items' => [],
        ];

        return Excel::download(new ReporteFinancieroExport($data), 'reporte_financiero.xlsx');
    }

    public function inventarioExcel()
    {
        $data = [
            'fecha' => now(),
            'items' => [],
        ];

        return Excel::download(new ReporteInventarioExport($data), 'reporte_inventario.xlsx');
    }
}