<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Login - VetSanJuan</title>
    <link rel="icon" href="favicon.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background-color: #f8f9fa;
        }

        .titulo-login {
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
            color: #080286;
        }

        .btn-primario {
            background-color: #e2ae23;
            color: #080286;
            font-weight: 600;
        }

        .btn-primario:hover {
            background-color: #d1a020;
        }

        .enlace-secundario {
            color: #080286;
            transition: color 0.2s;
        }

        .enlace-secundario:hover {
            color: #06016d;
        }

        .card-login {
            border-radius: 15px;
            border: none;
        }

        .input-login {
            border-radius: 50px;
            padding: 10px 20px;
            border: 1px solid #ced4da;
        }

        .input-login:focus {
            border-color: #e2ae23;
            box-shadow: 0 0 0 0.25rem rgba(226, 174, 35, 0.25);
        }

        .input-group .btn-outline-secondary {
            border-color: #ced4da;
            color: #6c757d;
        }

        .input-group .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #080286;
        }

        .input-group .input-login {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group .btn {
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
        }

        /* Opciones de perfil en recuadros (5 recuadros) */
        .perfil-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }
        @media (max-width: 992px) {
            .perfil-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
        }
        @media (max-width: 576px) {
            .perfil-grid { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); }
        }
        .perfil-box {
            border: 2px solid #e2ae23;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out, transform 0.1s ease-in-out;
            user-select: none;
        }
        .perfil-box:hover { background-color: rgba(226, 174, 35, 0.08); }
        .perfil-box .titulo { font-weight: 700; color: #080286; }
        .perfil-box .sub { color: #6c757d; font-size: 0.9rem; }
    </style>
</head>

<body>


    <!-- Contenido Principal -->
    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card card-login shadow-lg p-4 p-md-5">
                    <!-- Logo y Título -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/favicon.png') }}" alt="Logo VetSanJuan" width="100" height="100"
                            class="rounded-circle mb-3" style="object-fit: cover;">
                        <h3 class="titulo-login">Selecciona tu Perfil</h3>
                    </div>

                    <!-- Mensajes de Sesión -->
                    <x-auth-session-status class="alert alert-info mb-4" :status="session('status')" />

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Perfil de usuario -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Selecciona tu perfil</label>
                            <div class="perfil-grid">
                                <div class="perfil-box" data-target="#perfil_cliente" data-url="{{ route('dashboard') }}">
                                    <input class="form-check-input d-none" type="radio" name="perfil" id="perfil_cliente" value="cliente" checked>
                                    <div class="titulo">Cliente</div>
                                    <div class="sub">Eres cliente</div>
                                </div>
                                <div class="perfil-box" data-target="#perfil_administrador" data-url="/admin">
                                    <input class="form-check-input d-none" type="radio" name="perfil" id="perfil_administrador" value="administrador">
                                    <div class="titulo">Administrador</div>
                                    <div class="sub">Eres administrador</div>
                                </div>
                                <div class="perfil-box" data-target="#perfil_sucursal" data-url="/sucursal">
                                    <input class="form-check-input d-none" type="radio" name="perfil" id="perfil_sucursal" value="sucursal">
                                    <div class="titulo">Sucursal</div>
                                    <div class="sub">Eres sucursal</div>
                                </div>
                                <div class="perfil-box" data-target="#perfil_veterinario" data-url="/veterinario">
                                    <input class="form-check-input d-none" type="radio" name="perfil" id="perfil_veterinario" value="veterinario">
                                    <div class="titulo">Veterinario</div>
                                    <div class="sub">Eres veterinario</div>
                                </div>
                                <div class="perfil-box" data-target="#perfil_peluquero" data-url="/peluquero">
                                    <input class="form-check-input d-none" type="radio" name="perfil" id="perfil_peluquero" value="peluquero">
                                    <div class="titulo">Peluquero</div>
                                    <div class="sub">Eres peluquero</div>
                                </div>
                                <div class="perfil-box" data-target="#perfil_facturador" data-url="/facturador">
                                    <input class="form-check-input d-none" type="radio" name="perfil" id="perfil_facturador" value="facturador">
                                    <div class="titulo">Facturador</div>
                                    <div class="sub">Eres facturador</div>
                                </div>
                            </div>
                        </div>

                        <!-- Enlaces Secundarios -->
                        <div class="text-center mt-3">
                            @if (Route::has('password.request'))
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="d-block enlace-secundario text-decoration-none">
                                    ¿No tienes cuenta? <span class="fw-bold">Regístrate</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Selección y redirección al hacer clic en recuadros
        document.querySelectorAll('.perfil-box').forEach(function(box) {
            box.addEventListener('click', function() {
                var targetSelector = this.dataset.target;
                var url = this.dataset.url;
                var radio = document.querySelector(targetSelector);
                if (radio) {
                    radio.checked = true;
                }
                if (url) {
                    window.location.href = url;
                }
            });
        });
    </script>
</body>

</html>