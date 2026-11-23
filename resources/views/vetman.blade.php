@extends('layouts.navfoot')

@section('title')
<title>Vetman – Nuestro Superhéroe</title>
@endsection

@section('styles')
<style>
    .hero-vetman {
        /* Fix: remove extra ')' and add solid color fallback */
        background: linear-gradient(rgba(0, 54, 115, 0.8), rgba(0, 54, 115, 0.8)), url('{{ asset('images/vet.png') }}');
        background-color: #003673;
        background-size: cover;
        background-position: center;
        padding: 100px 0;
        color: white;
        text-align: center;
    }
    .hero-vetman h1 { font-size: 3rem; font-weight: 800; }
    .hero-vetman p { font-size: 1.2rem; max-width: 800px; margin: 0 auto; }

    .story-section { padding: 3rem 0; }
    .donation-card { border: none; border-radius: 15px; }
    .donation-input { max-width: 240px; }
</style>
@endsection

@section('content')
<div class="hero-vetman">
    <div class="container">
        <h1>Vetman, el superhéroe de los animales</h1>
        <p>El guardián de los peludos de la calle: rescata, cuida y encuentra hogares para animales abandonados.</p>
    </div>
</div>

<div class="container story-section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-3" style="color:#003673; font-weight:700;">Nuestra historia</h2>
            <p>
                Vetman nació del compromiso de nuestra veterinaria con los animales sin voz. Cada día, apoya rescates,
                tratamientos y procesos de adopción para perros y gatos que han sido abandonados. Con tu ayuda,
                podemos darles una segunda oportunidad.
            </p>
            <p>
                Tus donaciones se destinan directamente a alimentación, atención médica, vacunas, esterilizaciones y
                rehabilitación. Cada aporte cuenta y nos acerca a un mundo más justo para ellos.
            </p>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-lg-6">
            <div class="card shadow donation-card">
                <div class="card-body p-4">
                    <h3 class="mb-3">Dona a la causa</h3>
                    <p class="text-muted">Ingresa el monto que deseas donar y realiza tu aporte mediante PayPal.</p>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="input-group donation-input">
                            <span class="input-group-text">USD</span>
                            <input type="number" id="donationAmount" class="form-control" placeholder="10.00" min="1" step="0.01" value="10.00">
                        </div>
                        <button id="updateAmount" class="btn btn-outline-primary">Actualizar monto</button>
                    </div>

                    @php $paypalClientId = config('services.paypal.client_id'); @endphp
                    @if($paypalClientId)
                        <div id="paypal-button-container"></div>
                        <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency=USD"></script>
                        <script>
                            (function() {
                                const amountInput = document.getElementById('donationAmount');
                                const updateBtn = document.getElementById('updateAmount');

                                function renderButtons() {
                                    // Limpiar contenedor antes de renderizar nuevamente
                                    const container = document.getElementById('paypal-button-container');
                                    container.innerHTML = '';
                                    paypal.Buttons({
                                        style: { layout: 'vertical', color: 'gold', shape: 'rect', label: 'donate' },
                                        createOrder: (data, actions) => {
                                            const value = parseFloat(amountInput.value);
                                            if (isNaN(value) || value < 1) {
                                                alert('Por favor ingresa un monto válido (mínimo 1 USD).');
                                                throw new Error('Invalid amount');
                                            }
                                            return actions.order.create({
                                                purchase_units: [{ amount: { value: value.toFixed(2) } }]
                                            });
                                        },
                                        onApprove: (data, actions) => {
                                            return actions.order.capture().then(details => {
                                                const name = details.payer && details.payer.name ? (details.payer.name.given_name || 'Donante') : 'Donante';
                                                alert(`¡Gracias, ${name}! Tu donación ha sido recibida.`);
                                            });
                                        },
                                        onError: (err) => {
                                            console.error('PayPal error', err);
                                            alert('Ocurrió un error al procesar la donación. Inténtalo nuevamente.');
                                        }
                                    }).render('#paypal-button-container');
                                }

                                // Render inicial y permitir actualizar el monto
                                renderButtons();
                                updateBtn.addEventListener('click', renderButtons);
                            })();
                        </script>
                    @else
                        <div class="alert alert-warning" role="alert">
                            Configura <code>PAYPAL_CLIENT_ID</code> en tu archivo <code>.env</code> para activar el botón de donaciones.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection