<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de Acceso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .credentials-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .credential-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #4a5568;
            display: inline-block;
            width: 120px;
        }
        .value {
            color: #2d3748;
            font-family: monospace;
            background-color: #edf2f7;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .login-button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .login-button:hover {
            background-color: #2563eb;
        }
        .warning {
            background-color: #fef3cd;
            border: 1px solid #fecaca;
            color: #92400e;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏥 VetSanJuan</div>
            <h1>Credenciales de Acceso</h1>
            <p>Sistema de Gestión Veterinaria</p>
        </div>

        <p>Estimado/a <strong>{{ $userName }}</strong>,</p>

        <p>Se le ha asignado como administrador de la sucursal <strong>{{ $sucursalNombre }}</strong> ({{ $sucursalCodigo }}). A continuación encontrará sus credenciales de acceso al sistema:</p>

        <div class="credentials-box">
            <h3>📋 Datos de Acceso</h3>
            
            <div class="credential-item">
                <span class="label">Sucursal:</span>
                <span class="value">{{ $sucursalNombre }}</span>
            </div>
            
            <div class="credential-item">
                <span class="label">Código:</span>
                <span class="value">{{ $sucursalCodigo }}</span>
            </div>
            
            <div class="credential-item">
                <span class="label">Email:</span>
                <span class="value">{{ $userEmail }}</span>
            </div>
            
            <div class="credential-item">
                <span class="label">Contraseña:</span>
                <span class="value">{{ $password }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="login-button">🔐 Acceder al Sistema</a>
        </div>

        <div class="warning">
            <strong>⚠️ Importante:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Cambie su contraseña después del primer inicio de sesión</li>
                <li>No comparta estas credenciales con terceros</li>
                <li>Mantenga segura su información de acceso</li>
                <li>Si tiene problemas para acceder, contacte al administrador principal</li>
            </ul>
        </div>

        <h3>🚀 Funcionalidades Disponibles</h3>
        <p>Como administrador de sucursal, tendrá acceso a:</p>
        <ul>
            <li>📊 Panel de control con estadísticas de la sucursal</li>
            <li>🛒 Gestión de inventario y productos</li>
            <li>💰 Facturación y ventas</li>
            <li>👥 Administración de clientes</li>
            <li>📈 Reportes financieros</li>
            <li>💳 Control de caja</li>
            <li>🏥 Gestión de citas veterinarias</li>
        </ul>

        <div class="footer">
            <p>Este es un mensaje automático del sistema VetSanJuan.</p>
            <p>Si tiene alguna pregunta, contacte al administrador principal.</p>
            <p><strong>© {{ date('Y') }} VetSanJuan - Sistema de Gestión Veterinaria</strong></p>
        </div>
    </div>
</body>
</html>