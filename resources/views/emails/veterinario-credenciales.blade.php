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
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .credentials {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            background-color: #64748b;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 VetSanJuan</h1>
        <h2>Bienvenido al Panel de Veterinarios</h2>
    </div>

    <div class="content">
        <p>Estimado/a <strong>{{ $veterinario->name }}</strong>,</p>

        <p>Se ha creado su cuenta de acceso al panel de veterinarios de VetSanJuan. A continuación encontrará sus credenciales de acceso:</p>

        <div class="credentials">
            <h3>📧 Sus Credenciales de Acceso</h3>
            <p><strong>Email:</strong> {{ $veterinario->email }}</p>
            <p><strong>Contraseña temporal:</strong> <code style="background-color: #f1f5f9; padding: 4px 8px; border-radius: 4px;">{{ $password }}</code></p>
        </div>

        <div class="warning">
            <h4>⚠️ Importante - Seguridad</h4>
            <ul>
                <li>Esta es una contraseña temporal generada automáticamente</li>
                <li>Se recomienda cambiarla en su primer inicio de sesión</li>
                <li>No comparta estas credenciales con terceros</li>
                <li>Mantenga su contraseña segura y confidencial</li>
            </ul>
        </div>

        <p>Para acceder a su panel, haga clic en el siguiente enlace:</p>
        
        <a href="{{ url('/veterinario') }}" class="button">🔐 Acceder al Panel de Veterinario</a>

        <h3>🩺 Funcionalidades Disponibles</h3>
        <ul>
            <li><strong>Mis Citas:</strong> Visualice y gestione sus citas asignadas</li>
            <li><strong>Consultas Médicas:</strong> Registre diagnósticos y tratamientos</li>
            <li><strong>Recetas Médicas:</strong> Genere recetas para sus pacientes</li>
            <li><strong>Dashboard:</strong> Estadísticas y resumen de su actividad</li>
        </ul>

        <p>Si tiene alguna pregunta o necesita asistencia técnica, no dude en contactar al administrador del sistema.</p>

        <p>¡Bienvenido al equipo de VetSanJuan!</p>

        <p>Saludos cordiales,<br>
        <strong>Equipo Administrativo VetSanJuan</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} VetSanJuan - Sistema de Gestión Veterinaria</p>
        <p>Este es un mensaje automático, por favor no responda a este correo.</p>
    </div>
</body>
</html>