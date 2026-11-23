<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estado de Resultados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        .sub { color: #666; margin-bottom: 16px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f7f7f7; text-align: left; }
        .negativo { color: #b91c1c; }
        .positivo { color: #15803d; }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
    <h1>Estado de Resultados</h1>
    <div class="sub">Periodo: {{ $desde->format('d/m/Y') }} - {{ $hasta->format('d/m/Y') }}</div>

    <table>
        <tbody>
            <tr>
                <th>Ingresos</th>
                <td>${{ number_format($ingresos, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Costo de Ventas</th>
                <td>${{ number_format($costoVentas, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Utilidad Bruta</th>
                <td class="{{ $utilidadBruta < 0 ? 'negativo' : 'positivo' }}">${{ number_format($utilidadBruta, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Gastos Operativos</th>
                <td>${{ number_format($gastos, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Utilidad Neta</th>
                <td class="{{ $utilidadNeta < 0 ? 'negativo' : 'positivo' }}">${{ number_format($utilidadNeta, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top:16px;color:#666;">Nota: El costo de ventas se estima con el total de compras del período.</p>
</body>
</html>