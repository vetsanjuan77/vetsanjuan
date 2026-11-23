<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Balance General</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        .sub { color: #666; margin-bottom: 16px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f7f7f7; text-align: left; }
        .section { margin-top: 16px; }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
    <h1>Balance General</h1>
    <div class="sub">Fecha de corte: {{ $corte->format('d/m/Y') }}</div>

    <div class="section">
        <h3>Activos</h3>
        <table>
            <tbody>
                <tr>
                    <th>Caja</th>
                    <td>${{ number_format($caja, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Cuentas por Cobrar</th>
                    <td>${{ number_format($cuentasPorCobrar, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Inventario</th>
                    <td>${{ number_format($inventarioValor, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Activos</th>
                    <td><strong>${{ number_format($activos, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Pasivos</h3>
        <table>
            <tbody>
                <tr>
                    <th>Cuentas por Pagar</th>
                    <td>${{ number_format($cuentasPorPagar, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Pasivos</th>
                    <td><strong>${{ number_format($pasivos, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Patrimonio</h3>
        <table>
            <tbody>
                <tr>
                    <th>Patrimonio</th>
                    <td><strong>${{ number_format($patrimonio, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <p style="margin-top:16px;color:#666;">Nota: Cuentas por pagar basadas en compras pendientes; inventario calculado por stock × precio compra.</p>
</body>
</html>