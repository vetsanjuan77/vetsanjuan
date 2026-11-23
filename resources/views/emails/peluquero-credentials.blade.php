<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de Acceso</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4CAF50;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 16px;
        }
        .welcome {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .credentials-box {
            background-color: #e8f5e8;
            border: 2px solid #4CAF50;
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
            color: #333;
        }
        .credential-value {
            background-color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            border: 1px solid #ddd;
            display: inline-block;
            margin-left: 10px;
        }
        .login-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        .login-button:hover {
            background-color: #45a049;
        }
        .instructions {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .instructions h3 {
            color: #856404;
            margin-top: 0;
        }
        .instructions ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .security-note {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #742a2a;
        }
        .security-note h4 {
            margin-top: 0;
            color: #c53030;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🐾 VetSanJuan</div>
            <div class="subtitle">Panel de Peluquería Canina</div>
        </div>

        <div class="welcome">
            <h2>¡Bienvenido/a, {{ $peluquero->nombre }}!</h2>
            <p>Te damos la bienvenida al equipo de peluquería de VetSanJuan. Hemos creado tu cuenta de acceso al panel de gestión donde podrás administrar tus citas y servicios.</p>
        </div>

        <div class="credentials-box">
            <h3>🔑 Tus Credenciales de Acceso</h3>
            
            <div class="credential-item">
                <span class="credential-label">Usuario (Email):</span>
                <span class="credential-value">{{ $peluquero->correo_electronico }}</span>
            </div>
            
            <div class="credential-item">
                <span class="credential-label">Contraseña Temporal:</span>
                <span class="credential-value">{{ $temporaryPassword }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="login-button">
                🚀 Acceder al Panel
            </a>
        </div>

        <div class="instructions">
            <h3>📋 Instrucciones de Primer Acceso</h3>
            <ul>
                <li><strong>Accede al panel:</strong> Haz clic en el botón "Acceder al Panel" o visita: <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></li>
                <li><strong>Inicia sesión:</strong> Usa tu correo electrónico y la contraseña temporal proporcionada</li>
                <li><strong>Cambia tu contraseña:</strong> Por seguridad, te recomendamos cambiar tu contraseña en el primer acceso</li>
                <li><strong>Explora el panel:</strong> Familiarízate con las funciones de gestión de citas y servicios</li>
            </ul>
        </div>

        <div class="security-note">
            <h4>🔒 Nota de Seguridad</h4>
            <p><strong>¡Importante!</strong> Esta contraseña es temporal y solo debe ser usada por ti. Te recomendamos:</p>
            <ul>
                <li>Cambiar la contraseña inmediatamente después del primer acceso</li>
                <li>No compartir estas credenciales con nadie</li>
                <li>Usar una contraseña segura (mínimo 8 caracteres, con mayúsculas, minúsculas y números)</li>
            </ul>
        </div>

        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>📞 ¿Necesitas Ayuda?</h3>
            <p>Si tienes alguna duda o problema para acceder al panel, no dudes en contactarnos:</p>
            <ul>
                <li><strong>Teléfono:</strong> [Número de contacto]</li>
                <li><strong>Email:</strong> [Email de soporte]</li>
                <li><strong>Horario de atención:</strong> Lunes a Viernes, 8:00 AM - 6:00 PM</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>VetSanJuan - Peluquería Canina</strong></p>
            <p>Este correo fue enviado automáticamente. Por favor, no respondas a este mensaje.</p>
            <p style="font-size: 12px; color: #999;">
                Si no solicitaste estas credenciales, por favor contacta inmediatamente con el administrador.
            </p>
        </div>
    </div>
</body>
</html>