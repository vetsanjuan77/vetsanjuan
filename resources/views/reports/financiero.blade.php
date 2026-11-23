<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Financiero</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
    </head>
<body>
    <h1>Reporte Financiero</h1>
    <div class="meta">Fecha: {{ isset($data['fecha']) ? \Carbon\Carbon::parse($data['fecha'])->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>

    <h3>Resumen</h3>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Ingresos</td>
                <td>{{ $data['ingresos'] ?? '—' }}</td>
            </tr>
            <tr>
                <td>Gastos</td>
                <td>{{ $data['gastos'] ?? '—' }}</td>
            </tr>
            <tr>
                <td>Utilidad</td>
                <td>{{ $data['utilidad'] ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Detalle</h3>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($data['items'] ?? []) as $item)
                <tr>
                    <td>{{ $item['label'] ?? '—' }}</td>
                    <td>{{ $item['monto'] ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Sin datos disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>