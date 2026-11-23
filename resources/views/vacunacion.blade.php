@extends('layouts.navfoot')
@section('title')
    <title>Vacunación</title>
@endsection
@section('styles')
    <style>
        .main-content {
            padding: 20px;
            min-height: 100%;
        }

        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .banner-img {
            width: 100%;
            height: 400px;
            object-fit: contain;
        }
        
        .pet-images {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .pet-images img {
            width: 48%;
            height: 400px;
            object-fit: contain;
        }
    </style>
@endsection
@section('content')
    <div class="main-content">
        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Vacunación</h1>
                    
                    <!-- Imagen de vacunas a lo largo de la página -->
                    <img src="{{ asset('images/vacunas.png') }}" alt="Vacunas" class="banner-img">
                    
                    <!-- Imágenes de perros y gatos una al lado de la otra -->
                    <div class="pet-images">
                        <img src="{{ asset('images/perros.png') }}" alt="Perros">
                        <img src="{{ asset('images/gatos.png') }}" alt="Gatos">
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <h2>Agendar Cita</h2>

                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                            @endif

                            <form id="form-vacunacion" action="#" method="GET">
                                @csrf

                                <div class="mb-3">
                                    <label for="mascota_id_vacunacion" class="form-label">Mascota</label>
                                    <select name="mascota_id" id="mascota_id_vacunacion" class="form-select" required>
                                        <option value="" disabled selected>Seleccione una mascota</option>
                                        @foreach ($mascotas as $mascota)
                                            <option value="{{ $mascota->id }}" data-especie="{{ $mascota->especie->nombre }}">{{ $mascota->nombre_mascota }} ({{ $mascota->especie->nombre }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tipo_vacuna" class="form-label">Tipo de Vacuna</label>
                                    <select name="tipo_vacuna" id="tipo_vacuna" class="form-select" required>
                                        <option value="" disabled selected>Seleccione primero una mascota</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="fecha_vacunacion" class="form-label">Fecha</label>
                                    <input type="date" name="fecha" id="fecha_vacunacion" class="form-control"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="veterinario_id_vacunacion" class="form-label">Veterinario</label>
                                    <select name="veterinario_id" id="veterinario_id_vacunacion" class="form-select"
                                        required>
                                        <option value="" disabled selected>Seleccione un veterinario</option>
                                        @foreach ($veterinarios as $vet)
                                            <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="hora_vacunacion" class="form-label">Hora</label>
                                    <select name="hora" id="hora_vacunacion" class="form-select" required disabled>
                                        <option value="" disabled selected>Seleccione primero veterinario y fecha</option>
                                    </select>
                                </div>

                                <input type="hidden" name="tipo_servicio_id" value="3">

                                <div class="d-flex gap-2">
                                    <button type="button" id="btn-continuar-vac" class="btn btn-primary">Continuar</button>
                                    <button type="reset" class="btn btn-secondary">Limpiar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Resumen de agendamiento y pago -->
                    <div class="card d-none" id="resumen-card-vac">
                        <div class="card-body">
                            <h3 class="mb-3">Resumen de la cita</h3>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Servicio</span><span class="fw-semibold">Cita de Vacunación</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Mascota</span><span id="resumen-mascota-vac" class="fw-semibold">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Tipo de vacuna</span><span id="resumen-tipo-vacuna" class="fw-semibold">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Fecha</span><span id="resumen-fecha-vac" class="fw-semibold">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Hora</span><span id="resumen-hora-vac" class="fw-semibold">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Veterinario</span><span id="resumen-veterinario-vac" class="fw-semibold">-</span></li>
                            </ul>
                            <div class="alert alert-info mb-3">
                                Reserva de consumibles: <strong>$10.000 COP</strong>. Es un consumible no reembolsable si no llega a la cita.
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary" onclick="document.getElementById('resumen-card-vac').classList.add('d-none')">Volver</button>
                                <a id="btn-confirmar-pagar-vac" href="#" class="btn btn-success">Confirmar y pagar</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mascotaSelect = document.getElementById('mascota_id_vacunacion');
            const tipoVacunaSelect = document.getElementById('tipo_vacuna');
            const fechaInput = document.getElementById('fecha_vacunacion');
            const horaSelect = document.getElementById('hora_vacunacion');
            const vetSelect = document.getElementById('veterinario_id_vacunacion');

            const resumenCard = document.getElementById('resumen-card-vac');
            const continuarBtn = document.getElementById('btn-continuar-vac');
            const confirmarBtn = document.getElementById('btn-confirmar-pagar-vac');

            const rMascota = document.getElementById('resumen-mascota-vac');
            const rTipoVacuna = document.getElementById('resumen-tipo-vacuna');
            const rFecha = document.getElementById('resumen-fecha-vac');
            const rHora = document.getElementById('resumen-hora-vac');
            const rVet = document.getElementById('resumen-veterinario-vac');
            
            function cargarHorasVacunacion() {
                const vetId = vetSelect.value;
                const fecha = fechaInput.value;
                horaSelect.innerHTML = '<option value="" disabled selected>Seleccione primero veterinario y fecha</option>';
                horaSelect.disabled = true;
                if (!vetId || !fecha) return;
                fetch(`/citas/slots?rol=veterinario&user_id=${vetId}&fecha=${fecha}`)
                    .then(resp => resp.json())
                    .then(slots => {
                        horaSelect.innerHTML = '';
                        if (!slots || slots.length === 0) {
                            const opt = document.createElement('option');
                            opt.value = '';
                            opt.textContent = 'No hay horas disponibles';
                            opt.disabled = true;
                            opt.selected = true;
                            horaSelect.appendChild(opt);
                            return;
                        }
                        const def = document.createElement('option');
                        def.value = '';
                        def.textContent = 'Seleccione una hora';
                        def.disabled = true;
                        def.selected = true;
                        horaSelect.appendChild(def);
                        slots.forEach(h => {
                            const opt = document.createElement('option');
                            opt.value = h;
                            opt.textContent = h;
                            horaSelect.appendChild(opt);
                        });
                        horaSelect.disabled = false;
                    })
                    .catch(() => {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'Error al cargar horas';
                        opt.disabled = true;
                        opt.selected = true;
                        horaSelect.appendChild(opt);
                    });
            }

            vetSelect.addEventListener('change', cargarHorasVacunacion);
            fechaInput.addEventListener('change', cargarHorasVacunacion);

            // Función para cargar vacunas desde la API según la especie
            function cargarVacunasPorEspecie(especieId) {
                // Mostrar mensaje de carga
                tipoVacunaSelect.innerHTML = '<option value="" disabled selected>Cargando vacunas...</option>';
                
                fetch(`/vacunas/especie/${especieId}`)
                    .then(response => response.json())
                    .then(vacunas => {
                        actualizarOpcionesVacunas(vacunas);
                    })
                    .catch(error => {
                        console.error('Error al cargar vacunas:', error);
                        tipoVacunaSelect.innerHTML = '<option value="" disabled selected>Error al cargar vacunas</option>';
                    });
            }
            
            // Función para actualizar las opciones de vacunas
            function actualizarOpcionesVacunas(vacunas) {
                // Limpiar opciones actuales
                tipoVacunaSelect.innerHTML = '';
                
                // Agregar opción predeterminada
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.disabled = true;
                defaultOption.selected = true;
                defaultOption.textContent = vacunas.length > 0 ? 'Seleccione tipo de vacuna' : 'No hay vacunas disponibles';
                tipoVacunaSelect.appendChild(defaultOption);
                
                // Agregar las opciones de vacunas desde la base de datos
                vacunas.forEach(vacuna => {
                    const option = document.createElement('option');
                    option.value = vacuna.tipo;
                    option.textContent = vacuna.nombre;
                    tipoVacunaSelect.appendChild(option);
                });
            }
            
            // Evento para cuando cambia la mascota seleccionada
            mascotaSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const mascotaId = this.value;
                
                if (mascotaId) {
                    // Obtener la especie de la mascota seleccionada
                    // Necesitamos hacer una llamada para obtener los datos de la mascota
                    fetch(`/mascotas/${mascotaId}/especie`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.especie_id) {
                                cargarVacunasPorEspecie(data.especie_id);
                            }
                        })
                        .catch(error => {
                            console.error('Error al obtener especie de mascota:', error);
                            // Fallback: usar el atributo data-especie si existe
                            const especie = selectedOption.getAttribute('data-especie');
                            if (especie) {
                                // Buscar el ID de la especie basado en el nombre
                                const especieNombre = especie.toLowerCase();
                                let especieId = null;
                                
                                // Mapeo básico de nombres a IDs (esto debería venir de la base de datos)
                                if (especieNombre === 'perro') {
                                    especieId = 1; // Asumiendo que Perro tiene ID 1
                                } else if (especieNombre === 'gato') {
                                    especieId = 2; // Asumiendo que Gato tiene ID 2
                                }
                                
                                if (especieId) {
                                    cargarVacunasPorEspecie(especieId);
                                }
                            } else {
                                tipoVacunaSelect.innerHTML = '<option value="" disabled selected>Error al cargar vacunas</option>';
                            }
                        });
                } else {
                    tipoVacunaSelect.innerHTML = '<option value="" disabled selected>Seleccione primero una mascota</option>';
                }
            });

            continuarBtn.addEventListener('click', function() {
                const mascotaId = mascotaSelect.value;
                const mascotaText = mascotaSelect.options[mascotaSelect.selectedIndex]?.text;
                const tipoVacuna = tipoVacunaSelect.value;
                const tipoVacunaText = tipoVacunaSelect.options[tipoVacunaSelect.selectedIndex]?.text;
                const fecha = fechaInput.value;
                const hora = horaSelect.value;
                const vetId = vetSelect.value;
                const vetText = vetSelect.options[vetSelect.selectedIndex]?.text;

                if (!mascotaId || !tipoVacuna || !fecha || !hora || !vetId) {
                    alert('Por favor completa mascota, tipo de vacuna, fecha, hora y veterinario.');
                    return;
                }

                rMascota.textContent = mascotaText;
                rTipoVacuna.textContent = tipoVacunaText;
                rFecha.textContent = fecha;
                rHora.textContent = hora;
                rVet.textContent = vetText;

                const params = new URLSearchParams({
                    mascota_id: mascotaId,
                    tipo_vacuna: tipoVacuna,
                    fecha: fecha,
                    hora: hora,
                    veterinario_id: vetId,
                    tipo_servicio_id: 3,
                });
                confirmarBtn.href = `{{ url('/pago') }}?${params.toString()}`;

                resumenCard.classList.remove('d-none');
                resumenCard.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
@endsection