@extends('layouts.navfoot')
@section('title')
    <title>Peluquería y Baño</title>
@endsection
@section('styles')
    <style>
        .main-content {
            padding: 20px;
            min-height: 100%;
        }

        .service-card {
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            border-radius: 15px;
            border: 2px solid #e9ecef;
            cursor: pointer;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .service-card.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
        }

        .service-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: #6c757d;
        }

        .step.active {
            background-color: #007bff;
            color: white;
        }

        .step.completed {
            background-color: #28a745;
            color: white;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
        }

        .option-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .option-card:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .option-card.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .option-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-back:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
@endsection
@section('content')
    <div class="main-content">
        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4 text-center">Peluquería y Baño</h1>
                    
                    <!-- Indicador de pasos -->
                    <div class="step-indicator">
                        <div class="step active" id="step-1">1</div>
                        <div class="step" id="step-2">2</div>
                        <div class="step" id="step-3">3</div>
                        <div class="step" id="step-4">4</div>
                        <div class="step" id="step-5">5</div>
                    </div>

                    <!-- Paso 1: Selección de servicio principal -->
                    <div class="step-content active" id="content-step-1">
                        <h3 class="text-center mb-4">Selecciona el tipo de servicio</h3>
                        <div class="row">
                            <!-- Perro Solo Baño -->
                            <div class="col-md-4 mb-4">
                                <div class="service-card" data-service="perro-bano">
                                    <div class="card-body text-center p-4">
                                        <img src="{{ asset('images/perro solo bano.png') }}" alt="Perro Solo Baño" class="service-image mb-3">
                                        <h5 class="card-title">Perro - Solo Baño</h5>
                                        <p class="card-text">Baño completo con champú especializado, secado y cepillado. Incluye limpieza de oídos y corte de uñas.</p>
                                        <button class="btn btn-primary" onclick="selectService('perro-bano')">Seleccionar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Perro Baño y Corte -->
                            <div class="col-md-4 mb-4">
                                <div class="service-card" data-service="perro-corte">
                                    <div class="card-body text-center p-4">
                                        <img src="{{ asset('images/perro bano y corte.png') }}" alt="Perro Baño y Corte" class="service-image mb-3">
                                        <h5 class="card-title">Perro - Baño y Corte</h5>
                                        <p class="card-text">Servicio completo que incluye baño, corte de pelo personalizado, peinado y acabados profesionales.</p>
                                        <button class="btn btn-primary" onclick="selectService('perro-corte')">Seleccionar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Gato Solo Baño -->
                            <div class="col-md-4 mb-4">
                                <div class="service-card" data-service="gato-bano">
                                    <div class="card-body text-center p-4">
                                        <img src="{{ asset('images/gato bano.png') }}" alt="Gato Solo Baño" class="service-image mb-3">
                                        <h5 class="card-title">Gato - Solo Baño</h5>
                                        <p class="card-text">Baño especializado para gatos con productos suaves, secado cuidadoso y cepillado anti-estrés.</p>
                                        <button class="btn btn-primary" onclick="selectService('gato-bano')">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Selección de tamaño/detalles -->
                    <div class="step-content" id="content-step-2">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button class="btn btn-back" onclick="goToStep(1)">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <h3 class="text-center mb-0" id="step2-title">Selecciona el tamaño</h3>
                            <div></div>
                        </div>

                        <!-- Opciones para Perro Solo Baño -->
                        <div id="perro-bano-options" class="service-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="option-card" data-option="perro-pequeno" onclick="selectOption('perro-pequeno')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/bano perro pequeno.png') }}" alt="Perro Pequeño" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Perro Pequeño</h6>
                                                <p class="mb-0 text-muted">Hasta 10 kg</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="option-card" data-option="perro-mediano" onclick="selectOption('perro-mediano')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/bano perro mediano.png') }}" alt="Perro Mediano" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Perro Mediano</h6>
                                                <p class="mb-0 text-muted">10 - 25 kg</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="option-card" data-option="perro-grande" onclick="selectOption('perro-grande')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/bano perro grande.png') }}" alt="Perro Grande" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Perro Grande</h6>
                                                <p class="mb-0 text-muted">Más de 25 kg</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones para Perro Baño y Corte -->
                        <div id="perro-corte-options" class="service-options" style="display: none;">
                            <div class="row">
                                <!-- Pequeño -->
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="pequeno-pelo-largo" onclick="selectOption('pequeno-pelo-largo')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/pequeno pelo largo.png') }}" alt="Pequeño Pelo Largo" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Pequeño Pelo Largo</h6>
                                                <p class="mb-0 text-muted">Hasta 10 kg, pelo largo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="pequeno-pelo-corto" onclick="selectOption('pequeno-pelo-corto')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/pequeno pelo corto.png') }}" alt="Pequeño Pelo Corto" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Pequeño Pelo Corto</h6>
                                                <p class="mb-0 text-muted">Hasta 10 kg, pelo corto</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mediano -->
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="mediano-pelo-largo" onclick="selectOption('mediano-pelo-largo')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/mediano pelo largo.png') }}" alt="Mediano Pelo Largo" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Mediano Pelo Largo</h6>
                                                <p class="mb-0 text-muted">10-25 kg, pelo largo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="mediano-pelo-corto" onclick="selectOption('mediano-pelo-corto')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/mediano pelo corto.png') }}" alt="Mediano Pelo Corto" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Mediano Pelo Corto</h6>
                                                <p class="mb-0 text-muted">10-25 kg, pelo corto</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grande -->
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="grande-pelo-largo" onclick="selectOption('grande-pelo-largo')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/grande pelo largo.png') }}" alt="Grande Pelo Largo" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Grande Pelo Largo</h6>
                                                <p class="mb-0 text-muted">Más de 25 kg, pelo largo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="grande-pelo-corto" onclick="selectOption('grande-pelo-corto')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/grande pelo corto.png') }}" alt="Grande Pelo Corto" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Grande Pelo Corto</h6>
                                                <p class="mb-0 text-muted">Más de 25 kg, pelo corto</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones para Gato Solo Baño -->
                        <div id="gato-bano-options" class="service-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="gato-pequeno" onclick="selectOption('gato-pequeno')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/bano gato bebe.png') }}" alt="Gato Pequeño" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Gato Pequeño</h6>
                                                <p class="mb-0 text-muted">Hasta 3 kg</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="gato-mediano-pelo-largo" onclick="selectOption('gato-mediano-pelo-largo')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/gato mediano pelo largo.png') }}" alt="Gato Mediano Pelo Largo" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Gato Mediano Pelo Largo</h6>
                                                <p class="mb-0 text-muted">3-5 kg, pelo largo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="gato-mediano-pelo-corto" onclick="selectOption('gato-mediano-pelo-corto')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/gato mediano pelo corto.png') }}" alt="Gato Mediano Pelo Corto" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Gato Mediano Pelo Corto</h6>
                                                <p class="mb-0 text-muted">3-5 kg, pelo corto</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="gato-grande-pelo-largo" onclick="selectOption('gato-grande-pelo-largo')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/gato grande pelo largo.png') }}" alt="Gato Grande Pelo Largo" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Gato Grande Pelo Largo</h6>
                                                <p class="mb-0 text-muted">Más de 5 kg, pelo largo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="option-card" data-option="gato-grande-pelo-corto" onclick="selectOption('gato-grande-pelo-corto')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/gato grande pelo corto.png') }}" alt="Gato Grande Pelo Corto" class="option-image me-3">
                                            <div>
                                                <h6 class="mb-1">Gato Grande Pelo Corto</h6>
                                                <p class="mb-0 text-muted">Más de 5 kg, pelo corto</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Agendar Cita -->
                    <div class="step-content" id="content-step-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button class="btn btn-back" onclick="goToStep(2)">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <h3 class="text-center mb-0">Agendar Cita</h3>
                            <div></div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                                @endif

                                <div class="mb-3">
                                    <label for="mascota_id_peluqueria" class="form-label">Mascota *</label>
                                    <select name="mascota_id" id="mascota_id_peluqueria" class="form-select" required>
                                        <option value="" disabled selected>Seleccione una mascota</option>
                                        @foreach ($mascotas as $mascota)
                                            <option value="{{ $mascota->id }}">{{ $mascota->nombre_mascota }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="fecha_peluqueria" class="form-label">Fecha *</label>
                                    <input type="date" name="fecha" id="fecha_peluqueria" class="form-control"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="peluquero_id_peluqueria" class="form-label">Peluquero *</label>
                                    <select name="peluquero_id" id="peluquero_id_peluqueria" class="form-select" required>
                                        <option value="" disabled selected>Seleccione un peluquero</option>
                                        @foreach ($peluqueros as $peluquero)
                                            <option value="{{ $peluquero->id }}">{{ $peluquero->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="hora_peluqueria" class="form-label">Hora *</label>
                                    <select name="hora" id="hora_peluqueria" class="form-select" required disabled>
                                        <option value="" disabled selected>Seleccione primero peluquero y fecha</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="goToStep(4)">Siguiente</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 4: Datos del Cliente -->
                    <div class="step-content" id="content-step-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button class="btn btn-back" onclick="goToStep(3)">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <h3 class="text-center mb-0">Datos del Cliente</h3>
                            <div></div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="mb-3">
                                    <label for="nombre_cliente" class="form-label">Nombre Completo *</label>
                                    <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control" 
                                           value="{{ $cliente ? $cliente->nombre . ' ' . $cliente->apellido : $user->name }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="correo_cliente" class="form-label">Correo Electrónico *</label>
                                    <input type="email" name="correo_cliente" id="correo_cliente" class="form-control" 
                                           value="{{ $user->email }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono_cliente" class="form-label">Teléfono *</label>
                                    <input type="tel" name="telefono_cliente" id="telefono_cliente" class="form-control" 
                                           value="{{ $cliente ? $cliente->telefono : '' }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">¿Su mascota tiene muchos nudos? *</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tiene_nudos" id="nudos_si" value="si" required>
                                        <label class="form-check-label" for="nudos_si">
                                            Sí, tiene nudos
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tiene_nudos" id="nudos_no" value="no" required>
                                        <label class="form-check-label" for="nudos_no">
                                            No, no tiene nudos
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">¿Hay algo que debamos saber antes de la sesión?</label>
                                    <textarea name="observaciones" id="observaciones" class="form-control" rows="3" 
                                        placeholder="Ej: La mascota es nerviosa, tiene alguna condición médica, comportamiento especial, etc."></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="goToStep(5)">Siguiente</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 5: Resumen y Confirmación -->
                    <div class="step-content" id="content-step-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button class="btn btn-back" onclick="goToStep(4)">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <h3 class="text-center mb-0">Confirmar Reserva</h3>
                            <div></div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <!-- Resumen de selección -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5>Resumen de la cita:</h5>
                                        <div id="service-summary"></div>
                                        <div id="appointment-summary"></div>
                                        <div id="client-summary"></div>
                                        
                                        <hr class="my-3">
                                        <div class="alert alert-warning mb-0">
                                            <strong>Costo de agendamiento:</strong> $10.000 COP<br>
                                            <small>Corresponde a consumibles y no es reembolsable si no se presenta.</small>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('pago.formulario') }}" method="GET">
                                    <input type="hidden" name="mascota_id" id="hidden_mascota_id">
                                    <input type="hidden" name="fecha" id="hidden_fecha">
                                    <input type="hidden" name="hora" id="hidden_hora">
                                    <input type="hidden" name="peluquero_id" id="hidden_peluquero_id">
                                    <input type="hidden" name="tipo_servicio_id" value="1">
                                    <input type="hidden" name="servicio_detalle" id="servicio_detalle">
                                    <input type="hidden" name="nombre_cliente" id="hidden_nombre_cliente">
                                    <input type="hidden" name="correo_cliente" id="hidden_correo_cliente">
                                    <input type="hidden" name="telefono_cliente" id="hidden_telefono_cliente">
                                    <input type="hidden" name="tiene_nudos" id="hidden_tiene_nudos">
                                    <input type="hidden" name="observaciones" id="hidden_observaciones">

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success btn-lg">Confirmar y Pagar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedService = '';
        let selectedOption = '';
        let currentStep = 1;

        function selectService(service) {
            selectedService = service;
            
            // Actualizar indicadores visuales
            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelector(`[data-service="${service}"]`).classList.add('selected');
            
            // Ir al paso 2
            setTimeout(() => {
                goToStep(2);
                showServiceOptions(service);
            }, 300);
        }

        function showServiceOptions(service) {
            // Ocultar todas las opciones
            document.querySelectorAll('.service-options').forEach(option => {
                option.style.display = 'none';
            });
            
            // Mostrar las opciones correspondientes y actualizar título
            const titles = {
                'perro-bano': 'Selecciona el tamaño del perro',
                'perro-corte': 'Selecciona tamaño y tipo de pelo',
                'gato-bano': 'Selecciona el tamaño y tipo de pelo del gato'
            };
            
            document.getElementById('step2-title').textContent = titles[service];
            document.getElementById(`${service}-options`).style.display = 'block';
        }

        function selectOption(option) {
            selectedOption = option;
            
            // Actualizar indicadores visuales
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelector(`[data-option="${option}"]`).classList.add('selected');
            
            // Ir al paso 3
            setTimeout(() => {
                goToStep(3);
            }, 300);
        }

        function goToStep(step) {
            // Validar campos antes de avanzar
            if (step > currentStep) {
                if (currentStep === 3) {
                    // Validar paso 3: mascota, fecha, hora, peluquero
                    const mascota = document.getElementById('mascota_id_peluqueria').value;
                    const fecha = document.getElementById('fecha_peluqueria').value;
                    const hora = document.getElementById('hora_peluqueria').value;
                    const peluquero = document.getElementById('peluquero_id_peluqueria').value;
                    
                    if (!mascota || !fecha || !hora || !peluquero) {
                        alert('Por favor completa todos los campos obligatorios antes de continuar.');
                        return;
                    }
                } else if (currentStep === 4) {
                    // Validar paso 4: nombre, correo, teléfono, pregunta nudos
                    const nombre = document.getElementById('nombre_cliente').value.trim();
                    const correo = document.getElementById('correo_cliente').value.trim();
                    const telefono = document.getElementById('telefono_cliente').value.trim();
                    const nudos = document.querySelector('input[name="tiene_nudos"]:checked');
                    
                    if (!nombre || !correo || !telefono || !nudos) {
                        alert('Por favor completa todos los campos obligatorios antes de continuar.');
                        return;
                    }
                    
                    // Validar formato de email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(correo)) {
                        alert('Por favor ingresa un correo electrónico válido.');
                        return;
                    }
                }
            }

            // Actualizar indicadores de paso
            document.querySelectorAll('.step').forEach((stepEl, index) => {
                stepEl.classList.remove('active', 'completed');
                if (index + 1 < step) {
                    stepEl.classList.add('completed');
                } else if (index + 1 === step) {
                    stepEl.classList.add('active');
                }
            });
            
            // Mostrar contenido del paso
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`content-step-${step}`).classList.add('active');

            // Actualizar paso actual
            currentStep = step;

            // Si vamos al paso 5, actualizar todos los resúmenes
            if (step === 5) {
                updateAllSummaries();
            }
        }

        function updateAllSummaries() {
            updateServiceSummary();
            updateAppointmentSummary();
            updateClientSummary();
            updateHiddenFields();
        }

        function updateServiceSummary() {
            const serviceNames = {
                'perro-bano': 'Perro - Solo Baño',
                'perro-corte': 'Perro - Baño y Corte',
                'gato-bano': 'Gato - Solo Baño'
            };
            
            const optionNames = {
                'perro-pequeno': 'Perro Pequeño (hasta 10 kg)',
                'perro-mediano': 'Perro Mediano (10-25 kg)',
                'perro-grande': 'Perro Grande (más de 25 kg)',
                'pequeno-pelo-largo': 'Pequeño Pelo Largo (5-10 kg)',
                'pequeno-pelo-corto': 'Pequeño Pelo Corto (5-10 kg)',
                'mediano-pelo-largo': 'Mediano Pelo Largo (10-25 kg)',
                'mediano-pelo-corto': 'Mediano Pelo Corto (10-25 kg)',
                'grande-pelo-largo': 'Grande Pelo Largo (más de 25 kg)',
                'grande-pelo-corto': 'Grande Pelo Corto (más de 25 kg)',
                'gato-pequeno': 'Gato Pequeño (hasta 3 kg)',
                'gato-mediano-pelo-largo': 'Gato Mediano Pelo Largo (3-5 kg)',
                'gato-mediano-pelo-corto': 'Gato Mediano Pelo Corto (3-5 kg)',
                'gato-grande-pelo-largo': 'Gato Grande Pelo Largo (más de 5 kg)',
                'gato-grande-pelo-corto': 'Gato Grande Pelo Corto (más de 5 kg)'
            };
            
            const summary = `
                <p><strong>Servicio:</strong> ${serviceNames[selectedService]}</p>
                <p><strong>Detalles:</strong> ${optionNames[selectedOption]}</p>
            `;
            
            document.getElementById('service-summary').innerHTML = summary;
            document.getElementById('servicio_detalle').value = `${selectedService}|${selectedOption}`;
        }

        function updateAppointmentSummary() {
            const mascota = document.getElementById('mascota_id_peluqueria');
            const fecha = document.getElementById('fecha_peluqueria');
            const hora = document.getElementById('hora_peluqueria');
            const peluquero = document.getElementById('peluquero_id_peluqueria');

            const summary = `
                <p><strong>Mascota:</strong> ${mascota.options[mascota.selectedIndex]?.text || 'No seleccionada'}</p>
                <p><strong>Fecha:</strong> ${fecha.value || 'No seleccionada'}</p>
                <p><strong>Hora:</strong> ${hora.value || 'No seleccionada'}</p>
                <p><strong>Peluquero:</strong> ${peluquero.options[peluquero.selectedIndex]?.text || 'No seleccionado'}</p>
            `;
            
            document.getElementById('appointment-summary').innerHTML = summary;
        }

        function updateClientSummary() {
            const nombre = document.getElementById('nombre_cliente').value;
            const correo = document.getElementById('correo_cliente').value;
            const telefono = document.getElementById('telefono_cliente').value;
            const nudos = document.querySelector('input[name="tiene_nudos"]:checked')?.value;
            const observaciones = document.getElementById('observaciones').value;

            let summary = `
                <p><strong>Nombre:</strong> ${nombre || 'No especificado'}</p>
                <p><strong>Correo:</strong> ${correo || 'No especificado'}</p>
                <p><strong>Teléfono:</strong> ${telefono || 'No especificado'}</p>
                <p><strong>¿Tiene nudos?:</strong> ${nudos === 'si' ? 'Sí' : nudos === 'no' ? 'No' : 'No especificado'}</p>
            `;

            if (observaciones) {
                summary += `<p><strong>Observaciones:</strong> ${observaciones}</p>`;
            }
            
            document.getElementById('client-summary').innerHTML = summary;
        }

        function updateHiddenFields() {
            // Copiar valores a campos ocultos
            document.getElementById('hidden_mascota_id').value = document.getElementById('mascota_id_peluqueria').value;
            document.getElementById('hidden_fecha').value = document.getElementById('fecha_peluqueria').value;
            document.getElementById('hidden_hora').value = document.getElementById('hora_peluqueria').value;
            document.getElementById('hidden_peluquero_id').value = document.getElementById('peluquero_id_peluqueria').value;
            document.getElementById('hidden_nombre_cliente').value = document.getElementById('nombre_cliente').value;
            document.getElementById('hidden_correo_cliente').value = document.getElementById('correo_cliente').value;
            document.getElementById('hidden_telefono_cliente').value = document.getElementById('telefono_cliente').value;
            document.getElementById('hidden_tiene_nudos').value = document.querySelector('input[name="tiene_nudos"]:checked')?.value || '';
            document.getElementById('hidden_observaciones').value = document.getElementById('observaciones').value;
        }

        // Función para cargar datos del cliente si ya existe
        function loadClientData() {
            // Esta función se puede expandir para buscar datos existentes del cliente
            // Por ahora, simplemente limpia los campos
            document.getElementById('nombre_cliente').value = '';
            document.getElementById('correo_cliente').value = '';
            document.getElementById('telefono_cliente').value = '';
        }

        // Cargar datos del cliente cuando se accede al paso 4
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar en el paso 1
            goToStep(1);
        });
    </script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mascotaSelect = document.getElementById('mascota_id_peluqueria');
        const fechaInput = document.getElementById('fecha_peluqueria');
        const horaSelect = document.getElementById('hora_peluqueria');
        const peluqueroSelect = document.getElementById('peluquero_id_peluqueria');

        const resumenCard = document.getElementById('resumen-card-pelu');
        const continuarBtn = document.getElementById('btn-continuar-pelu');
        const confirmarBtn = document.getElementById('btn-confirmar-pagar-pelu');

        const rMascota = document.getElementById('resumen-mascota-pelu');
        const rFecha = document.getElementById('resumen-fecha-pelu');
        const rHora = document.getElementById('resumen-hora-pelu');
        const rPelu = document.getElementById('resumen-peluquero-pelu');

        function cargarHorasPeluqueria() {
            const peluId = peluqueroSelect.value;
            const fecha = fechaInput.value;
            horaSelect.innerHTML = '<option value="" disabled selected>Seleccione primero peluquero y fecha</option>';
            horaSelect.disabled = true;
            if (!peluId || !fecha) return;
            fetch(`/citas/slots?rol=peluquero&user_id=${peluId}&fecha=${fecha}`)
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

        peluqueroSelect.addEventListener('change', cargarHorasPeluqueria);
        fechaInput.addEventListener('change', cargarHorasPeluqueria);

        // Resumen y flujo de continuar
        continuarBtn.addEventListener('click', function() {
            const mascotaId = mascotaSelect.value;
            const mascotaText = mascotaSelect.options[mascotaSelect.selectedIndex]?.text;
            const fecha = fechaInput.value;
            const hora = horaSelect.value;
            const peluId = peluqueroSelect.value;
            const peluText = peluqueroSelect.options[peluqueroSelect.selectedIndex]?.text;

            if (!mascotaId || !fecha || !hora || !peluId) {
                alert('Por favor complete mascota, fecha, hora y peluquero.');
                return;
            }

            rMascota.textContent = mascotaText;
            rFecha.textContent = fecha;
            rHora.textContent = hora;
            rPelu.textContent = peluText;

            const params = new URLSearchParams({
                mascota_id: mascotaId,
                fecha: fecha,
                hora: hora,
                peluquero_id: peluId,
                tipo_servicio_id: 2,
            });
            confirmarBtn.href = `{{ url('/pago') }}?${params.toString()}`;

            resumenCard.classList.remove('d-none');
            resumenCard.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
@endsection