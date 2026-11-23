<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de Acceso - Panel Facturador</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #f59e0b;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #f59e0b;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
        }
        .credentials-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .credential-item {
            margin: 10px 0;
            font-size: 16px;
        }
        .credential-label {
            font-weight: bold;
            color: #92400e;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            background-color: white;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #d97706;
            display: inline-block;
            margin-left: 10px;
            color: #1f2937;
        }
        .access-url {
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .access-link {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
        }
        .access-link:hover {
            text-decoration: underline;
        }
        .warning-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-title {
            color: #dc2626;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-section {
            background-color: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        h3 {
            color: #374151;
            margin-top: 25px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🧾 VetSanJuan</div>
            <div class="subtitle">Sistema de Facturación</div>
        </div>

        <h2>¡Bienvenido al Panel de Facturación!</h2>
        
        <p>Estimado/a <strong>{{ $facturador->name }}</strong>,</p>
        
        <p>Se ha creado su cuenta de acceso al <strong>Panel de Facturación</strong> de la sucursal <strong>{{ $sucursal->nombre }}</strong>. A continuación encontrará sus credenciales de acceso:</p>

        <div class="credentials-box">
            <h3 style="margin-top: 0; color: #92400e;">🔐 Credenciales de Acceso</h3>
            <div class="credential-item">
                <span class="credential-label">📧 Email:</span>
                <span class="credential-value">{{ $facturador->email }}</span>
            </div>
            <div class="credential-item">
                <span class="credential-label">🔑 Contraseña:</span>
                <span class="credential-value">{{ $password }}</span>
            </div>
        </div>

        <div class="access-url">
            <p style="margin: 0 0 10px 0; color: #1f2937;">🌐 <strong>Acceder al Panel:</strong></p>
            <a href="{{ $panelUrl }}" class="access-link" target="_blank">{{ $panelUrl }}</a>
        </div>

        <div class="warning-box">
            <div class="warning-title">⚠️ Importante - Seguridad</div>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Cambie su contraseña en el primer acceso</li>
                <li>No comparta sus credenciales con terceros</li>
                <li>Mantenga segura su información de acceso</li>
                <li>Si tiene problemas para acceder, contacte al administrador de la sucursal</li>
            </ul>
        </div>

        <div class="info-section">
            <h3 style="margin-top: 0;">ℹ️ Información de su Sucursal</h3>
            <p><strong>Sucursal:</strong> {{ $sucursal->nombre }}</p>
            <p><strong>Dirección:</strong> {{ $sucursal->direccion }}</p>
            <p><strong>Código:</strong> {{ $sucursal->codigo }}</p>
        </div>

        <h3>🚀 Funcionalidades Disponibles</h3>
        <p>Como facturador, tendrá acceso a:</p>
        <ul>
            <li>📋 Creación y gestión de facturas</li>
            <li>💰 Generación de cotizaciones</li>
            <li>👥 Administración de clientes</li>
            <li>💳 Control de caja y movimientos</li>
            <li>📊 Consulta de reportes de ventas</li>
            <li>🔍 Búsqueda y filtrado de documentos</li>
        </ul>

        <div class="footer">
            <p>Este es un mensaje automático del sistema VetSanJuan.</p>
            <p>Si tiene alguna pregunta, contacte al administrador de la sucursal.</p>
            <p><strong>© {{ date('Y') }} VetSanJuan - Sistema de Gestión Veterinaria</strong></p>
        </div>
    </div>
</body>
</html>