@extends('layouts.navfoot')
@section('title')
    <title>Veterinaria</title>
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
    </style>
@endsection
@section('content')
    <div class="main-content">
        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Veterinaria</h1>
                    
                    <div class="card">
                        <div class="card-body">
                            <h2>Agendar Cita</h2>

                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                            @endif

                            <form id="form-veterinaria" action="#" method="GET">
                                @csrf

                                <div class="mb-3">
                                    <label for="mascota_id_veterinaria" class="form-label">Mascota</label>
                                    <select name="mascota_id" id="mascota_id_veterinaria" class="form-select" required>
                                        <option value="" disabled selected>Seleccione una mascota</option>
                                        @foreach ($mascotas as $mascota)
                                            <option value="{{ $mascota->id }}">{{ $mascota->nombre_mascota }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="fecha_veterinaria" class="form-label">Fecha</label>
                                    <input type="date" name="fecha" id="fecha_veterinaria" class="form-control"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="veterinario_id_veterinaria" class="form-label">Veterinario</label>
                                    <select name="veterinario_id" id="veterinario_id_veterinaria" class="form-select"
                                        required>
                                        <option value="" disabled selected>Seleccione un veterinario</option>
                                        @foreach ($veterinarios as $vet)
                                            <option value="{{ $vet->id }}">{{ $vet->nombre ?? $vet->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="hora_veterinaria" class="form-label">Hora</label>
                                    <select name="hora" id="hora_veterinaria" class="form-select" required disabled>
                                        <option value="" disabled selected>Seleccione primero veterinario y fecha</option>
                                    </select>
                                </div>

                                <input type="hidden" name="tipo_servicio_id" value="2">

                                <div class="d-flex gap-2">
                                    <button type="button" id="btn-continuar-vet" class="btn btn-primary">Continuar</button>
                                    <button type="reset" class="btn btn-secondary">Limpiar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Resumen de agendamiento y pago -->
                    <div class="card d-none" id="resumen-card-vet">
                        <div class="card-body">
                            <h3 class="mb-3">Resumen de la cita</h3>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Servicio</span><span class="fw-semibold">Consulta Veterinaria</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Mascota</span><span id="resumen-mascota-vet" class="fw-semibold">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Fecha</span><span id="resumen-fecha-vet" class="fw-semibold">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Hora</span><span id="resumen-hora-vet" class="fw-semibold">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Veterinario</span><span id="resumen-veterinario-vet" class="fw-semibold">-</span></li>
                            </ul>
                            <div class="alert alert-info mb-3">
                                Reserva de consumibles: <strong>$10.000 COP</strong>. Es un consumible no reembolsable si no llega a la cita.
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary" onclick="document.getElementById('resumen-card-vet').classList.add('d-none')">Volver</button>
                                <a id="btn-confirmar-pagar-vet" href="#" class="btn btn-success">Confirmar y pagar</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-veterinaria');
            const continuarBtn = document.getElementById('btn-continuar-vet');
            const resumenCard = document.getElementById('resumen-card-vet');
            const confirmarBtn = document.getElementById('btn-confirmar-pagar-vet');

            const mascotaSelect = document.getElementById('mascota_id_veterinaria');
            const fechaInput = document.getElementById('fecha_veterinaria');
            const horaSelect = document.getElementById('hora_veterinaria');
            const vetSelect = document.getElementById('veterinario_id_veterinaria');

            const rMascota = document.getElementById('resumen-mascota-vet');
            const rFecha = document.getElementById('resumen-fecha-vet');
            const rHora = document.getElementById('resumen-hora-vet');
            const rVet = document.getElementById('resumen-veterinario-vet');

            function cargarHorasVeterinaria() {
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

            vetSelect.addEventListener('change', cargarHorasVeterinaria);
            fechaInput.addEventListener('change', cargarHorasVeterinaria);

            continuarBtn.addEventListener('click', function() {
                const mascotaId = mascotaSelect.value;
                const mascotaText = mascotaSelect.options[mascotaSelect.selectedIndex]?.text;
                const fecha = fechaInput.value;
                const hora = horaSelect.value;
                const vetId = vetSelect.value;
                const vetText = vetSelect.options[vetSelect.selectedIndex]?.text;

                if (!mascotaId || !fecha || !hora || !vetId) {
                    alert('Por favor completa mascota, fecha, hora y veterinario.');
                    return;
                }

                rMascota.textContent = mascotaText;
                rFecha.textContent = fecha;
                rHora.textContent = hora;
                rVet.textContent = vetText;

                const params = new URLSearchParams({
                    mascota_id: mascotaId,
                    fecha: fecha,
                    hora: hora,
                    veterinario_id: vetId,
                    tipo_servicio_id: 2,
                });
                confirmarBtn.href = `{{ url('/pago') }}?${params.toString()}`;

                resumenCard.classList.remove('d-none');
                resumenCard.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
@endsection