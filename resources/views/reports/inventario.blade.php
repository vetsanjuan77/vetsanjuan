<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
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
    <h1>Reporte de Inventario</h1>
    <div class="meta">Fecha: {{ isset($data['fecha']) ? \Carbon\Carbon::parse($data['fecha'])->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>

    <h3>Resumen</h3>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Stock</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($data['items'] ?? []) as $item)
                <tr>
                    <td>{{ $item['producto'] ?? '—' }}</td>
                    <td>{{ $item['stock'] ?? '—' }}</td>
                    <td>{{ $item['precio'] ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin datos disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>