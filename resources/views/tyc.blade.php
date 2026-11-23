@extends('layouts.navfoot')

@section('title')
<title>Términos y Condiciones</title>
@endsection

@section('styles')
<style>
    .tyc-hero { background-color: #003673; color: white; padding: 80px 0; text-align: center; }
    .tyc-hero h1 { font-size: 2.5rem; font-weight: 800; }
    .tyc-section { padding: 2rem 0; }
    .tyc-section h2 { color: #003673; font-weight: 700; }
</style>
@endsection

@section('content')
<div class="tyc-hero">
    <div class="container">
        <h1>Términos y Condiciones</h1>
        <p class="mt-2">Consulta las reglas de uso de nuestro sitio y servicios.</p>
    </div>
</div>

<div class="container tyc-section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2>1. Aceptación</h2>
            <p>Al acceder y utilizar este sitio, aceptas estos Términos y Condiciones. Si no estás de acuerdo, por favor deja de usar el sitio.</p>

            <h2>2. Compras y Pagos</h2>
            <p>Las compras realizadas están sujetas a disponibilidad, precios y condiciones vigentes. Métodos de pago aceptados se detallan en el proceso de checkout.</p>

            <h2>3. Privacidad</h2>
            <p>El tratamiento de datos personales se realiza conforme a nuestra Política de Privacidad. Tus datos no se comparten sin consentimiento.</p>

            <h2>4. Servicios Veterinarios</h2>
            <p>Las citas y procedimientos se agendan según disponibilidad y evaluación profesional. Recomendamos seguir las indicaciones del personal médico.</p>

            <h2>5. Propiedad Intelectual</h2>
            <p>Todos los contenidos (marcas, textos, imágenes) pertenecen a sus respectivos titulares y están protegidos por la ley.</p>

            <h2>6. Contacto</h2>
            <p>Si tienes dudas sobre estos términos, contáctanos a través de la página de Contacto.</p>
        </div>
    </div>
</div>
@endsection