@extends('layouts.navfoot')
@section('title')
    <title>Dashboard</title>
@endsection
@section('styles')
    <style>
        .sidebar {
            padding-top: 20px;
            border-right: 1px solid #e0e0e0;
            background-color: #fff;
            /* height: 100%; */
            /* min-height: 100%; */
        }

        .main-content {
            padding: 20px;
            min-height: 100%;
        }



        .sidebar-link {
            color: #333;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
            cursor: pointer;
            text-align: left;
            text-decoration: none;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: #000;
            background-color: #f8f9fa;
        }


        .content-section {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .content-section.active {
            display: block;
            opacity: 1;
        }



        .card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .mascota-card,
        .cita-card {
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }

        .mascota-card {
            margin-bottom: 20px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .mascota-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .mascota-info {
            padding: 15px;
        }

        .mascota-nombre {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .mascota-detalles {
            color: #666;
            margin-bottom: 15px;
        }

        .modal-mascota .modal-content {
            border-radius: 15px;
        }

        .modal-mascota {
            width: 80%;
        }
    </style>
@endsection
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-2 sidebar py-5">
                    <div class="d-flex flex-column">
                        <a class="sidebar-link active" data-section="perfil">
                            <i class="fas fa-user me-2"></i> Perfil
                        </a>
                        <a class="sidebar-link" data-section="citas">
                            <i class="fas fa-calendar-alt me-2"></i> Citas
                        </a>
                        <a class="sidebar-link" data-section="mascotas">
                            <i class="fas fa-paw me-2"></i> Mascotas
                        </a>
                    </div>
                </div>

                <!-- Contenido Principal -->
                <div class="col-md-10 p-4 main-content">
                    <!-- Sección Perfil -->
                    <div id="perfil" class="content-section active">
                        <h2 class="mb-4 d-flex justify-content-between align-items-center">
                            Perfil del Cliente
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                                <i class="fas fa-edit"></i> Editar Perfil
                            </button>
                        </h2>
                        <div class="card">
                            <div class="card-body">
                                <h4>Bienvenido, {{ $user->name }}</h4>
                                <p><strong>Nombre:</strong> {{ $cliente->nombre }}</p>
                                <p><strong>Apellido:</strong> {{ $cliente->apellido }}</p>
                                <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Citas -->
                    <div id="citas" class="content-section">
                        <h2 class="mb-4">Mis Citas</h2>
                        <div class="row">
                            @php($totalCitas = ($citas->count() ?? 0) + ($citasPeluqueria->count() ?? 0))
                            @if($totalCitas === 0)
                                <div class="col-12">
                                    <p>No hay citas agendadas.</p>
                                </div>
                            @else
                                @foreach($citas as $cita)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card cita-card">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                                </h5>
                                                <p class="card-text">
                                                    <strong>Hora:</strong> {{ $cita->hora }}<br>
                                                    <strong>Mascota:</strong> {{ $cita->mascota->nombre_mascota }}<br>
                                                    <strong>Veterinario:</strong> {{ $cita->veterinario->name ?? 'No definido' }}<br>
                                                    <strong>Servicio:</strong> {{ $cita->servicio->nombre }}
                                                </p>

                                                <div class="d-flex justify-content-between mt-3">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detalleCitaModal"
                                                        data-fecha="{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}"
                                                        data-hora="{{ $cita->hora }}"
                                                        data-mascota="{{ $cita->mascota->nombre_mascota }}"
                                                        data-profesional="{{ $cita->veterinario->name ?? 'No definido' }}"
                                                        data-servicio="{{ $cita->servicio->nombre }}"
                                                        data-estado="{{ ucfirst(str_replace('_',' ', $cita->estado)) }}"
                                                        data-reserva="{{ number_format($cita->reserva_consumibles ?? 10000, 0, ',', '.') }}"
                                                        data-motivo="{{ $cita->motivo }}">
                                                        Ver detalles
                                                    </button>
                                                    <form id="cancelar-cita-{{ $cita->id }}" action="{{ route('citas.cancelar', $cita) }}" method="POST">
                                                        @csrf
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="if(confirm('¿Estás seguro de cancelar la cita? El costo de la reserva de la cita no será devuelto.')) document.getElementById('cancelar-cita-{{ $cita->id }}').submit();">
                                                            Cancelar cita
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($citasPeluqueria as $cita)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card cita-card">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                                </h5>
                                                <p class="card-text">
                                                    <strong>Hora:</strong> {{ $cita->hora }}<br>
                                                    <strong>Mascota:</strong> {{ $cita->mascota->nombre_mascota }}<br>
                                                    <strong>Peluquero:</strong> {{ $cita->peluquero->name ?? 'No definido' }}<br>
                                                    <strong>Servicio:</strong> {{ $cita->servicio->nombre ?? 'Peluquería' }}
                                                </p>

                                                <div class="d-flex justify-content-between mt-3">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detalleCitaModal"
                                                        data-fecha="{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}"
                                                        data-hora="{{ $cita->hora }}"
                                                        data-mascota="{{ $cita->mascota->nombre_mascota }}"
                                                        data-profesional="{{ $cita->peluquero->name ?? 'No definido' }}"
                                                        data-servicio="{{ $cita->servicio->nombre ?? 'Peluquería' }}"
                                                        data-estado="{{ ucfirst(str_replace('_',' ', $cita->estado)) }}"
                                                        data-reserva="{{ number_format($cita->reserva_consumibles ?? 10000, 0, ',', '.') }}"
                                                        data-motivo="{{ $cita->motivo }}">
                                                        Ver detalles
                                                    </button>
                                                    <form id="cancelar-cita-p-{{ $cita->id }}" action="{{ route('citas_peluqueria.cancelar', $cita) }}" method="POST">
                                                        @csrf
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="if(confirm('¿Estás seguro de cancelar la cita? El costo de la reserva de la cita no será devuelto.')) document.getElementById('cancelar-cita-p-{{ $cita->id }}').submit();">
                                                            Cancelar cita
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Sección Citas de Peluquería -->
                    <div id="citas-peluqueria" class="content-section">
                        <h2 class="mb-4">Mis Citas de Peluquería</h2>
                        <div class="row">
                            @forelse($citasPeluqueria as $cita)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card cita-card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                            </h5>
                                            <p class="card-text">
                                                <strong>Hora:</strong> {{ $cita->hora }}<br>
                                                <strong>Mascota:</strong> {{ $cita->mascota->nombre_mascota }}<br>
                                                <strong>Peluquero:</strong> {{ $cita->peluquero->name ?? 'No definido' }}<br>
                                                <strong>Servicio:</strong> {{ $cita->servicio->nombre ?? 'Peluquería' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p>No hay citas de peluquería agendadas.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Sección Mascotas -->
                    <div id="mascotas" class="content-section">
                        <h2 class="mb-4 d-flex justify-content-between align-items-center">
                            Mis Mascotas
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#crearMascotaModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </h2>
                        <div class="row">
                            <div class="modal fade" id="crearMascotaModal" tabindex="-1"
                                aria-labelledby="crearMascotaModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('mascotas.store') }}" method="POST"
                                        enctype="multipart/form-data" class="modal-content">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="crearMascotaModalLabel">Registrar Nueva Mascota
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="nombre_mascota" class="form-label">Nombre</label>
                                                <input type="text" name="nombre_mascota" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="fecha_nacimiento" class="form-label">Fecha de
                                                    nacimiento</label>
                                                <input type="date" name="fecha_nacimiento" class="form-control" required
                                                    onchange="calcularEdad(this.value)">
                                            </div>
                                            <input type="hidden" name="edad" id="edad">

                                            <div class="mb-3">
                                                <label for="especie_id" class="form-label">Especie</label>
                                                <select name="especie_id" id="especie_id" class="form-select" required>
                                                    <option value="">Seleccione una especie</option>
                                                    @foreach ($especies as $especie)
                                                        <option value="{{ $especie->id }}">
                                                            {{ $especie->nombre_especie }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="raza_id" class="form-label">Raza</label>
                                                <input type="text" id="buscar_raza" class="form-control mb-2" placeholder="Buscar raza..." disabled>
                                                <select name="raza_id" id="raza_id" class="form-select" required disabled>
                                                    <option value="">Seleccione primero una especie</option>
                                                </select>
                                                <small class="form-text text-muted" id="raza_result_count"></small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="tamano_id" class="form-label">Tamaño</label>
                                                <select name="tamano_id" id="tamano_id" class="form-select" required>
                                                    <option value="">Seleccione primero una especie</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="tipo_pelo_id" class="form-label">Tipo de Pelo</label>
                                                <select name="tipo_pelo_id" id="tipo_pelo_id" class="form-select" required>
                                                    <option value="">Seleccione el tipo de pelo</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="sexo_id" class="form-label">Sexo</label>
                                                <select name="sexo_id" class="form-select" required>
                                                    <option value="">Seleccione el sexo</option>
                                                    @foreach ($sexos as $sexo)
                                                        <option value="{{ $sexo->id }}">{{ $sexo->description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="peso" class="form-label">Peso (kg)</label>
                                                <input type="number" name="peso" step="0.1" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label for="vacunas" class="form-label">Vacunas</label>
                                                <div id="vacunas-container">
                                                    <p class="text-muted">Selecciona primero una especie para ver las vacunas disponibles.</p>
                                                </div>
                                                <div id="vacunas-seleccionadas" class="mt-3">
                                                    <h6>Vacunas seleccionadas:</h6>
                                                    <div id="lista-vacunas-seleccionadas" class="border rounded p-2 bg-light" style="min-height: 50px;">
                                                        <p class="text-muted mb-0">Ninguna vacuna seleccionada</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="alergias" class="form-label">Alergias</label>
                                                <input type="text" name="alergias" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label for="comentarios" class="form-label">Comentarios</label>
                                                <textarea name="comentarios" class="form-control"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="foto" class="form-label">Foto de la Mascota</label>
                                                <input type="file" name="foto" class="form-control" accept="image/*">
                                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <script>
                                function calcularEdad(fechaNacimiento) {
                                    const nacimiento = new Date(fechaNacimiento);
                                    const hoy = new Date();
                                    let edad = hoy.getFullYear() - nacimiento.getFullYear();
                                    const m = hoy.getMonth() - nacimiento.getMonth();
                                    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
                                        edad--;
                                    }
                                    document.getElementById('edad').value = edad;
                                }
                            </script>
                            @forelse($mascotas as $mascota)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card mascota-card">
                                        <img src="{{ $mascota->foto_url }}" class="mascota-imagen"
                                            alt="{{ $mascota->nombre_mascota }}">
                                        <div class="mascota-info">
                                                <h5 class="mascota-nombre">{{ $mascota->nombre_mascota }}</h5>
                                                <p class="mascota-detalles">
                                                    {{ $mascota->edad }} años -
                                                    {{ $mascota->especie->nombre_especie ?? 'N/A' }}
                                                </p>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#mascotaModal{{ $mascota->id }}">
                                                        Ver detalles
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editarMascotaModal{{ $mascota->id }}">
                                                        Editar
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="confirmarEliminacion({{ $mascota->id }}, '{{ $mascota->nombre_mascota }}')">
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <!-- Modal para cada mascota -->
                                <div class="modal fade modal-mascota" id="mascotaModal{{ $mascota->id }}"
                                    tabindex="-1" aria-labelledby="mascotaModalLabel{{ $mascota->id }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="mascotaModalLabel{{ $mascota->id }}">
                                                    {{ $mascota->nombre_mascota }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{ $mascota->foto_url }}" class="img-fluid rounded mb-3"
                                                    alt="{{ $mascota->nombre_mascota }}">
                                                <p><strong>Edad:</strong> {{ $mascota->edad }} años</p>
                                                <p><strong>Especie:</strong>
                                                    {{ $mascota->especie->nombre_especie ?? 'N/A' }}
                                                </p>
                                                <p><strong>Raza:</strong> {{ $mascota->raza->nombre_raza ?? 'N/A' }}</p>
                                                <p><strong>Sexo:</strong> {{ $mascota->sexo->descripcion ?? 'N/A' }}</p>
                                                <p><strong>Peso:</strong> {{ $mascota->peso }} kg</p>
                                                @if ($mascota->vacunas->count() > 0)
                                                    <p><strong>Vacunas:</strong> 
                                                        @foreach ($mascota->vacunas as $vacuna)
                                                            {{ $vacuna->nombre }} ({{ $vacuna->tipo }})@if (!$loop->last), @endif
                                                        @endforeach
                                                    </p>
                                                @endif
                                                @if ($mascota->alergias)
                                                    <p><strong>Alergias:</strong> {{ $mascota->alergias }}</p>
                                                @endif
                                                @if ($mascota->comentarios)
                                                    <p><strong>Comentarios:</strong> {{ $mascota->comentarios }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal para editar mascota -->
                                <div class="modal fade" id="editarMascotaModal{{ $mascota->id }}" tabindex="-1" 
                                    aria-labelledby="editarMascotaModalLabel{{ $mascota->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editarMascotaModalLabel{{ $mascota->id }}">
                                                    Editar {{ $mascota->nombre_mascota }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('mascotas.update', $mascota->id) }}" method="POST" enctype="multipart/form-data" id="editarMascotaForm{{ $mascota->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_nombre_mascota{{ $mascota->id }}" class="form-label">Nombre de la Mascota</label>
                                                                <input type="text" name="nombre_mascota" class="form-control" 
                                                                    id="edit_nombre_mascota{{ $mascota->id }}" value="{{ $mascota->nombre_mascota }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_fecha_nacimiento{{ $mascota->id }}" class="form-label">Fecha de Nacimiento</label>
                                                                <input type="date" name="fecha_nacimiento" class="form-control" 
                                                                    id="edit_fecha_nacimiento{{ $mascota->id }}" value="{{ $mascota->fecha_nacimiento }}" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="edit_especie_id{{ $mascota->id }}" class="form-label">Especie</label>
                                                                <select name="especie_id" class="form-select edit-especie-select" 
                                                                    id="edit_especie_id{{ $mascota->id }}" data-mascota-id="{{ $mascota->id }}" required>
                                                                    <option value="">Seleccione una especie</option>
                                                                    @foreach ($especies as $especie)
                                                                        <option value="{{ $especie->id }}" 
                                                                            {{ $mascota->especie_id == $especie->id ? 'selected' : '' }}>
                                                                            {{ $especie->nombre_especie }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="edit_sexo_id{{ $mascota->id }}" class="form-label">Sexo</label>
                                                                <select name="sexo_id" class="form-select" id="edit_sexo_id{{ $mascota->id }}" required>
                                                                    <option value="">Seleccione el sexo</option>
                                                                    @foreach ($sexos as $sexo)
                                                                        <option value="{{ $sexo->id }}" 
                                                                            {{ $mascota->sexo_id == $sexo->id ? 'selected' : '' }}>
                                                                            {{ $sexo->description }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="edit_edad{{ $mascota->id }}" class="form-label">Edad (años)</label>
                                                                <input type="number" name="edad" class="form-control" 
                                                                    id="edit_edad{{ $mascota->id }}" value="{{ $mascota->edad }}" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="edit_raza_id{{ $mascota->id }}" class="form-label">Raza</label>
                                                                <select name="raza_id" class="form-select edit-raza-select" 
                                                                    id="edit_raza_id{{ $mascota->id }}" data-mascota-id="{{ $mascota->id }}" required>
                                                                    <option value="">Seleccione una raza</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="edit_tamano_id{{ $mascota->id }}" class="form-label">Tamaño</label>
                                                                <select name="tamano_id" class="form-select edit-tamano-select" 
                                                                    id="edit_tamano_id{{ $mascota->id }}" data-mascota-id="{{ $mascota->id }}" required>
                                                                    <option value="">Seleccione un tamaño</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="edit_tipo_pelo_id{{ $mascota->id }}" class="form-label">Tipo de Pelo</label>
                                                                <select name="tipo_pelo_id" class="form-select edit-tipo-pelo-select" 
                                                                    id="edit_tipo_pelo_id{{ $mascota->id }}" data-mascota-id="{{ $mascota->id }}" required>
                                                                    <option value="">Seleccione tipo de pelo</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_peso{{ $mascota->id }}" class="form-label">Peso (kg)</label>
                                                                <input type="number" step="0.1" name="peso" class="form-control" 
                                                                    id="edit_peso{{ $mascota->id }}" value="{{ $mascota->peso }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_foto{{ $mascota->id }}" class="form-label">Foto de la Mascota</label>
                                                                <input type="file" name="foto" class="form-control" accept="image/*"
                                                                    id="edit_foto{{ $mascota->id }}">
                                                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                                                                @if($mascota->foto)
                                                                    <div class="mt-2">
                                                                        <img src="{{ $mascota->foto_url }}" alt="Foto actual" class="img-thumbnail" style="max-width: 100px;">
                                                                        <small class="d-block text-muted">Foto actual</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Vacunas</label>
                                                        <div id="edit-vacunas-container{{ $mascota->id }}">
                                                            <p class="text-muted">Selecciona primero una especie para ver las vacunas disponibles</p>
                                                        </div>
                                                        <div id="edit-vacunas-seleccionadas{{ $mascota->id }}" class="mt-2"></div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="edit_alergias{{ $mascota->id }}" class="form-label">Alergias</label>
                                                        <textarea name="alergias" class="form-control" id="edit_alergias{{ $mascota->id }}" 
                                                            rows="2">{{ $mascota->alergias }}</textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="edit_comentarios{{ $mascota->id }}" class="form-label">Comentarios</label>
                                                        <textarea name="comentarios" class="form-control" id="edit_comentarios{{ $mascota->id }}" 
                                                            rows="3">{{ $mascota->comentarios }}</textarea>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" form="editarMascotaForm{{ $mascota->id }}" class="btn btn-primary">
                                                    Actualizar Mascota
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div class="col-12">
                                    <p>No tienes mascotas registradas.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Aquí terminan las secciones del dashboard -->
                </div>
            </div>
            <br><br><br><br><br><br>
            <!-- Modal para editar perfil del cliente -->
            <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="editarPerfilForm" action="{{ route('cliente.update') }}" method="POST" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editarPerfilModalLabel">Editar Perfil</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" name="nombre" class="form-control" value="{{ $cliente->nombre }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" name="apellido" class="form-control" value="{{ $cliente->apellido }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" name="telefono" class="form-control" value="{{ $cliente->telefono }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="2">{{ $cliente->direccion }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const sidebarLinks = document.querySelectorAll('.sidebar-link');
                        const sections = document.querySelectorAll('.content-section');
                        let vacunasSeleccionadas = [];

                        function activateSection(sectionId) {
                            // Activar link en sidebar
                            sidebarLinks.forEach(link => {
                                if (link.getAttribute('data-section') === sectionId) {
                                    link.classList.add('active');
                                } else {
                                    link.classList.remove('active');
                                }
                            });

                            // Mostrar solo la sección correspondiente
                            sections.forEach(section => {
                                section.classList.toggle('active', section.id === sectionId);
                            });
                        }

                        // Manejo de clic en el sidebar
                        sidebarLinks.forEach(link => {
                            link.addEventListener('click', function(e) {
                                e.preventDefault();
                                const targetId = this.getAttribute('data-section');
                                history.replaceState(null, null, '#' +
                                    targetId); // actualiza el hash sin recargar
                                activateSection(targetId);
                            });
                        });

                        // Al cargar, verifica si hay un hash en la URL
                        const initialHash = window.location.hash.substring(1); // remueve el '#'
                        if (initialHash) {
                            activateSection(initialHash);
                        } else {
                            activateSection('perfil'); // valor por defecto
                        }

                        // Funcionalidad de vacunas dinámicas
                        const especieSelect = document.querySelector('select[name="especie_id"]');
                        const vacunasContainer = document.getElementById('vacunas-container');
                        const listaVacunasSeleccionadas = document.getElementById('lista-vacunas-seleccionadas');
                        
                        // Nuevos campos dinámicos
                        const razaSelect = document.getElementById('raza_id');
                        const tamanoSelect = document.getElementById('tamano_id');
                        const tipoPeloSelect = document.getElementById('tipo_pelo_id');

                        // Búsqueda y filtrado de razas
                        let razasCargadas = [];
                        const buscarRazaInput = document.getElementById('buscar_raza');
                        const razaResultCount = document.getElementById('raza_result_count');

                        // Funcionalidad para modales de edición
                        const editEspecieSelects = document.querySelectorAll('.edit-especie-select');
                        
                        editEspecieSelects.forEach(select => {
                            select.addEventListener('change', function() {
                                const mascotaId = this.getAttribute('data-mascota-id');
                                const especieId = this.value;
                                const editVacunasContainer = document.getElementById(`edit-vacunas-container${mascotaId}`);
                                
                                if (especieId) {
                                    cargarVacunasParaEdicion(especieId, mascotaId);
                                    cargarRazasParaEdicion(especieId, mascotaId);
                                    cargarTamanosParaEdicion(especieId, mascotaId);
                                    cargarTiposPeloParaEdicion(mascotaId);
                                } else {
                                    editVacunasContainer.innerHTML = '<p class="text-muted">Selecciona primero una especie para ver las vacunas disponibles</p>';
                                    limpiarSelectEdicion(`edit_raza_id${mascotaId}`);
                                    limpiarSelectEdicion(`edit_tamano_id${mascotaId}`);
                                    limpiarSelectEdicion(`edit_tipo_pelo_id${mascotaId}`);
                                }
                            });
                        });

                        if (especieSelect) {
                            especieSelect.addEventListener('change', function() {
                                const especieId = this.value;
                                if (especieId) {
                                    cargarVacunasPorEspecie(especieId);
                                    cargarRazasPorEspecie(especieId);
                                    cargarTamanosPorEspecie(especieId);
                                    cargarTiposPelo();
                                } else {
                                    vacunasContainer.innerHTML = '<p class="text-muted">Selecciona primero una especie para ver las vacunas disponibles.</p>';
                                    limpiarSelect('raza_id');
                                    limpiarSelect('tamano_id');
                                    limpiarSelect('tipo_pelo_id');
                                    // Deshabilitar y limpiar buscador de razas
                                    if (razaSelect) { razaSelect.disabled = true; }
                                    if (buscarRazaInput) { buscarRazaInput.value=''; buscarRazaInput.disabled = true; }
                                    if (razaResultCount) { razaResultCount.textContent=''; }
                                    razasCargadas = [];
                                }
                            });
                        }

                        // Funciones para cargar datos dinámicos en formulario de creación
                        function cargarRazasPorEspecie(especieId) {
                            fetch(`/razas/especie/${especieId}`)
                                .then(response => response.json())
                                .then(razas => {
                                    // Guardar y renderizar listado de razas
                                    razasCargadas = razas;
                                    renderRazas(razasCargadas);
                                    // Habilitar select y buscador
                                    const razaSelect = document.getElementById('raza_id');
                                    if (razaSelect) { razaSelect.disabled = false; }
                                    if (buscarRazaInput) {
                                        buscarRazaInput.disabled = false;
                                        buscarRazaInput.value = '';
                                        if (!buscarRazaInput.dataset.listenerAdded) {
                                            buscarRazaInput.addEventListener('input', onBuscarRazaInput);
                                            buscarRazaInput.dataset.listenerAdded = 'true';
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error al cargar razas:', error);
                                });
                        }

                        function cargarTamanosPorEspecie(especieId) {
                            fetch(`/tamanos/especie/${especieId}`)
                                .then(response => response.json())
                                .then(tamanos => {
                                    const tamanoSelect = document.getElementById('tamano_id');
                                    tamanoSelect.innerHTML = '<option value="">Selecciona un tamaño</option>';
                                    tamanos.forEach(tamano => {
                                        tamanoSelect.innerHTML += `<option value="${tamano.id}">${tamano.nombre_tamano}</option>`;
                                    });
                                })
                                .catch(error => {
                                    console.error('Error al cargar tamaños:', error);
                                });
                        }

                        function cargarTiposPelo() {
                            fetch('/tipos-pelo')
                                .then(response => response.json())
                                .then(tipos => {
                                    const tipoPeloSelect = document.getElementById('tipo_pelo_id');
                                    tipoPeloSelect.innerHTML = '<option value="">Selecciona tipo de pelo</option>';
                                    tipos.forEach(tipo => {
                                        tipoPeloSelect.innerHTML += `<option value="${tipo.id}">${tipo.nombre_tipo_pelo}</option>`;
                                    });
                                })
                                .catch(error => {
                                    console.error('Error al cargar tipos de pelo:', error);
                                });
                        }

                        // Renderizado y filtrado de razas
                        function renderRazas(listado, selectedId = null) {
                            const select = document.getElementById('raza_id');
                            if (!select) return;
                            select.innerHTML = '<option value="">Selecciona una raza</option>';
                            listado.forEach(raza => {
                                select.innerHTML += `<option value="${raza.id}">${raza.nombre_raza}</option>`;
                            });
                            if (selectedId) {
                                select.value = selectedId;
                            }
                            if (razaResultCount) {
                                razaResultCount.textContent = listado.length ? `${listado.length} razas encontradas` : 'Sin resultados';
                            }
                        }

                        function onBuscarRazaInput(e) {
                            const termino = e.target.value.trim().toLowerCase();
                            const select = document.getElementById('raza_id');
                            const seleccionado = select ? select.value : null;
                            const filtradas = termino ? razasCargadas.filter(r => r.nombre_raza.toLowerCase().includes(termino)) : razasCargadas;
                            renderRazas(filtradas, seleccionado);
                        }

                        function limpiarSelect(selectId) {
                            const select = document.getElementById(selectId);
                            if (select) {
                                select.innerHTML = '<option value="">Selecciona una opción</option>';
                            }
                        }

                        // Funciones para cargar datos dinámicos en formulario de edición
                        function cargarRazasParaEdicion(especieId, mascotaId) {
                            fetch(`/razas/especie/${especieId}`)
                                .then(response => response.json())
                                .then(razas => {
                                    const razaSelect = document.getElementById(`edit_raza_id${mascotaId}`);
                                    if (razaSelect) {
                                        razaSelect.innerHTML = '<option value="">Selecciona una raza</option>';
                                        razas.forEach(raza => {
                                            razaSelect.innerHTML += `<option value="${raza.id}">${raza.nombre_raza}</option>`;
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error al cargar razas:', error);
                                });
                        }

                        function cargarTamanosParaEdicion(especieId, mascotaId) {
                            fetch(`/tamanos/especie/${especieId}`)
                                .then(response => response.json())
                                .then(tamanos => {
                                    const tamanoSelect = document.getElementById(`edit_tamano_id${mascotaId}`);
                                    if (tamanoSelect) {
                                        tamanoSelect.innerHTML = '<option value="">Selecciona un tamaño</option>';
                                        tamanos.forEach(tamano => {
                                            tamanoSelect.innerHTML += `<option value="${tamano.id}">${tamano.nombre_tamano}</option>`;
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error al cargar tamaños:', error);
                                });
                        }

                        function cargarTiposPeloParaEdicion(mascotaId) {
                            fetch('/tipos-pelo')
                                .then(response => response.json())
                                .then(tipos => {
                                    const tipoPeloSelect = document.getElementById(`edit_tipo_pelo_id${mascotaId}`);
                                    if (tipoPeloSelect) {
                                        tipoPeloSelect.innerHTML = '<option value="">Selecciona tipo de pelo</option>';
                                        tipos.forEach(tipo => {
                                            tipoPeloSelect.innerHTML += `<option value="${tipo.id}">${tipo.nombre_tipo_pelo}</option>`;
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error al cargar tipos de pelo:', error);
                                });
                        }

                        function limpiarSelectEdicion(selectId) {
                            const select = document.getElementById(selectId);
                            if (select) {
                                select.innerHTML = '<option value="">Selecciona una opción</option>';
                            }
                        }

                        function cargarVacunasParaEdicion(especieId, mascotaId) {
                            const editVacunasContainer = document.getElementById(`edit-vacunas-container${mascotaId}`);
                            
                            fetch(`/vacunas/especie/${especieId}`)
                                .then(response => response.json())
                                .then(vacunas => {
                                    mostrarVacunasParaEdicion(vacunas, mascotaId);
                                })
                                .catch(error => {
                                    console.error('Error al cargar vacunas:', error);
                                    editVacunasContainer.innerHTML = '<p class="text-danger">Error al cargar las vacunas.</p>';
                                });
                        }

                        function mostrarVacunasParaEdicion(vacunas, mascotaId) {
                            const editVacunasContainer = document.getElementById(`edit-vacunas-container${mascotaId}`);
                            
                            if (vacunas.length === 0) {
                                editVacunasContainer.innerHTML = '<p class="text-muted">No hay vacunas disponibles para esta especie.</p>';
                                return;
                            }

                            let html = '<div class="row">';
                            vacunas.forEach(vacuna => {
                                html += `
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="vacunas[]" 
                                                value="${vacuna.id}" id="edit_vacuna_${mascotaId}_${vacuna.id}">
                                            <label class="form-check-label" for="edit_vacuna_${mascotaId}_${vacuna.id}">
                                                ${vacuna.nombre}
                                            </label>
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            editVacunasContainer.innerHTML = html;
                        }

                        // Función para confirmar eliminación de mascota
                        window.confirmarEliminacion = function(mascotaId, nombreMascota) {
                            if (confirm(`¿Estás seguro de que deseas eliminar a ${nombreMascota}? Esta acción no se puede deshacer.`)) {
                                // Crear un formulario temporal para enviar la petición DELETE
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `/mascotas/${mascotaId}`;
                                
                                // Agregar token CSRF
                                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                const csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = csrfToken;
                                form.appendChild(csrfInput);
                                
                                // Agregar método DELETE
                                const methodInput = document.createElement('input');
                                methodInput.type = 'hidden';
                                methodInput.name = '_method';
                                methodInput.value = 'DELETE';
                                form.appendChild(methodInput);
                                
                                // Agregar al DOM y enviar
                                document.body.appendChild(form);
                                form.submit();
                            }
                        };

                        function cargarVacunasPorEspecie(especieId) {
                            fetch(`/vacunas/especie/${especieId}`)
                                .then(response => response.json())
                                .then(vacunas => {
                                    mostrarVacunasDisponibles(vacunas);
                                })
                                .catch(error => {
                                    console.error('Error al cargar vacunas:', error);
                                    vacunasContainer.innerHTML = '<p class="text-danger">Error al cargar las vacunas.</p>';
                                });
                        }

                        function mostrarVacunasDisponibles(vacunas) {
                            if (vacunas.length === 0) {
                                vacunasContainer.innerHTML = '<p class="text-muted">No hay vacunas disponibles para esta especie.</p>';
                                return;
                            }

                            let html = '<div class="row">';
                            vacunas.forEach(vacuna => {
                                const isSelected = vacunasSeleccionadas.some(v => v.id === vacuna.id);
                                html += `
                                    <div class="col-md-6 mb-2">
                                        <div class="card ${isSelected ? 'border-success' : ''}">
                                            <div class="card-body p-2">
                                                <h6 class="card-title mb-2">${vacuna.nombre}</h6>
                                                <button type="button" class="btn btn-sm ${isSelected ? 'btn-danger' : 'btn-success'}" 
                                                        onclick="${isSelected ? 'quitarVacuna' : 'agregarVacuna'}(${vacuna.id}, '${vacuna.nombre}', '${vacuna.tipo}')">
                                                    ${isSelected ? 'Quitar' : 'Agregar'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            vacunasContainer.innerHTML = html;
                        }

                        window.agregarVacuna = function(id, nombre, tipo) {
                            if (!vacunasSeleccionadas.some(v => v.id === id)) {
                                vacunasSeleccionadas.push({id, nombre, tipo});
                                actualizarVacunasSeleccionadas();
                                // Recargar las vacunas disponibles para actualizar los botones
                                const especieId = especieSelect.value;
                                if (especieId) {
                                    cargarVacunasPorEspecie(especieId);
                                }
                            }
                        };

                        window.quitarVacuna = function(id, nombre, tipo) {
                            vacunasSeleccionadas = vacunasSeleccionadas.filter(v => v.id !== id);
                            actualizarVacunasSeleccionadas();
                            // Recargar las vacunas disponibles para actualizar los botones
                            const especieId = especieSelect.value;
                            if (especieId) {
                                cargarVacunasPorEspecie(especieId);
                            }
                        };

                        function actualizarVacunasSeleccionadas() {
                            if (vacunasSeleccionadas.length === 0) {
                                listaVacunasSeleccionadas.innerHTML = '<p class="text-muted mb-0">Ninguna vacuna seleccionada</p>';
                            } else {
                                let html = '';
                                vacunasSeleccionadas.forEach(vacuna => {
                                    html += `
                                        <span class="badge bg-primary me-2 mb-2">
                                            ${vacuna.nombre}
                                            <button type="button" class="btn-close btn-close-white ms-1" 
                                                    onclick="quitarVacuna(${vacuna.id}, '${vacuna.nombre}', '${vacuna.tipo}')" 
                                                    style="font-size: 0.7em;"></button>
                                            <input type="hidden" name="vacunas[]" value="${vacuna.id}">
                                        </span>
                                    `;
                                });
                                listaVacunasSeleccionadas.innerHTML = html;
                            }
                        }
                    });
                </script>
            @endpush

            <div class="modal fade" id="detalleCitaModal" tabindex="-1" aria-labelledby="detalleCitaLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detalleCitaLabel">Detalle de la cita</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Fecha:</strong> <span id="detalle-fecha"></span></p>
                            <p><strong>Hora:</strong> <span id="detalle-hora"></span></p>
                            <p><strong>Mascota:</strong> <span id="detalle-mascota"></span></p>
                            <p><strong>Profesional:</strong> <span id="detalle-profesional"></span></p>
                            <p><strong>Servicio:</strong> <span id="detalle-servicio"></span></p>
                            <p><strong>Estado:</strong> <span id="detalle-estado"></span></p>
                            <p><strong>Reserva de consumibles:</strong> $<span id="detalle-reserva"></span> COP</p>
                            <div class="detalle-motivo-wrapper">
                                <p><strong>Motivo:</strong> <span id="detalle-motivo"></span></p>
                            </div>
                            <div class="alert alert-warning mt-2">
                                Nota: El costo de la reserva de la cita no será devuelto si cancelas.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var detalleModal = document.getElementById('detalleCitaModal');
                    detalleModal.addEventListener('show.bs.modal', function (event) {
                        var button = event.relatedTarget;
                        if (!button) return;
                        document.getElementById('detalle-fecha').textContent = button.getAttribute('data-fecha') || '';
                        document.getElementById('detalle-hora').textContent = button.getAttribute('data-hora') || '';
                        document.getElementById('detalle-mascota').textContent = button.getAttribute('data-mascota') || '';
                        document.getElementById('detalle-profesional').textContent = button.getAttribute('data-profesional') || '';
                        document.getElementById('detalle-servicio').textContent = button.getAttribute('data-servicio') || '';
                        document.getElementById('detalle-estado').textContent = button.getAttribute('data-estado') || '';
                        document.getElementById('detalle-reserva').textContent = button.getAttribute('data-reserva') || '';
                        var motivo = button.getAttribute('data-motivo') || '';
                        var wrapper = detalleModal.querySelector('.detalle-motivo-wrapper');
                        if (motivo.trim() !== '') {
                            wrapper.style.display = '';
                            document.getElementById('detalle-motivo').textContent = motivo;
                        } else {
                            wrapper.style.display = 'none';
                        }
                    });
                });
            </script>
            @endpush
        @endsection
