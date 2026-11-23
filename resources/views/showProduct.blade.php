@extends('layouts.navfoot')

@section('title')
<title>{{ $producto->nombre }}</title>
@endsection

@section('styles')
<style>
    .product-gallery {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .product-gallery img {
        max-width: 100%;
        border-radius: 0.5rem;
        object-fit: cover;
    }

    .main-image {
        width: 100%;
        max-height: 400px;
        object-fit: contain;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        cursor: pointer;
        object-fit: cover;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }

    .thumbnail:hover {
        transform: scale(1.05);
    }

    .thumbnail.active {
        border-color: #0d6efd;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: #080286;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .precio-container {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin: 1rem 0;
    }

    .btn-agregar {
        width: 100%;
        padding: 0.8rem;
        margin-top: 1rem;
        font-size: 1.1rem;
    }
</style>
@endsection

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop') }}">Tienda</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $producto->nombre }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            <div class="product-gallery">
                <div class="col-md-6">
                    <!-- Imagen principal que se ajusta al ancho completo -->
                    <div class="main-image-container mb-3 w-100">
                        @if($producto->imagen)
                        <img id="mainImage" src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="main-image img-fluid rounded w-100">
                        @elseif($producto->presentaciones->isNotEmpty() && $producto->presentaciones->first()->imagen)
                        <img id="mainImage" src="{{ asset('storage/' . $producto->presentaciones->first()->imagen) }}" alt="{{ $producto->nombre }}" class="main-image img-fluid rounded w-100">
                        @else
                        <img id="mainImage" src="{{ asset('images/product-placeholder.svg') }}" alt="Sin imagen" class="main-image img-fluid rounded w-100">
                        @endif
                    </div>

                    <!-- Miniaturas (thumbnails) debajo de la imagen principal -->
                    <div class="thumbnails-container d-flex flex-wrap gap-2 mb-4 justify-content-center w-100">
                        <!-- Miniatura de la imagen principal -->
                        @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="thumbnail active" onclick="changeMainImage(this)">
                        @endif

                        <!-- Miniaturas de las presentaciones -->
                        @foreach($producto->presentaciones as $presentacion)
                        @if($presentacion->imagen)
                        <img src="{{ asset('storage/' . $presentacion->imagen) }}" alt="{{ $presentacion->nombre }}" class="thumbnail" onclick="changeMainImage(this)">
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="mb-3">{{ $producto->nombre }}</h1>
            <p class="text-muted mb-2">{{ $producto->marca->nombre }}</p>

            <div class="description-container mb-4">
                @if (strlen($producto->description) > 200)
                <p class="description-preview">{{ substr($producto->description, 0, 200) }}...
                    <a href="#" class="text-primary" onclick="toggleDescription(event)">Ver más</a>
                </p>
                <p class="description-full d-none">{{ $producto->description }}
                    <a href="#" class="text-primary" onclick="toggleDescription(event)">Ver menos</a>
                </p>
                @else
                <p>{{ $producto->description }}</p>
                @endif
            </div>

            <div class="precio-container">
                <h2 class="mb-3" id="precioProducto">
                    @if ($producto->presentaciones->isNotEmpty())
                        ${{ number_format($producto->presentaciones->first()->precio_unitario, 2) }}
                    @else
                        ${{ number_format($producto->precio, 2) }}
                    @endif
                </h2>
                <form action="{{ route('carrito.agregar', ['id' => $producto->id]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" value="1"
                            min="1">
                    </div>
                    @if ($producto->presentaciones->isNotEmpty())
                    <div class="mb-3">
                        <label for="presentacion_id" class="form-label">Presentación:</label>
                        <select name="presentacion_id" id="presentacion_id" class="form-select">
                            @foreach ($producto->presentaciones as $presentacion)
                            <option value="{{ $presentacion->id }}" 
                                    data-imagen="{{ $presentacion->imagen }}" 
                                    data-precio="{{ $presentacion->precio_unitario }}">
                                {{ $presentacion->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <button type="submit" class="btn btn-primary btn-agregar">
                        <i class="bi bi-cart-plus"></i> Agregar al carrito
                    </button>
                </form>
            </div>

            <a href="{{ route('shop') }}" class="btn btn-outline-secondary mt-3">
                <i class="bi bi-arrow-left"></i> Volver a la tienda
            </a>
        </div>
    </div>

    @if (isset($productosRelacionados) && $productosRelacionados->count() > 0)
    <div class="mt-5">
        <h3 class="mb-4">Productos relacionados</h3>
        <div class="row">
            @foreach ($productosRelacionados as $relacionado)
            <div class="col-md-3 mb-4">
                <div class="card h-100 producto-card">
                    @if ($relacionado->imagen)
                    @if (is_array($relacionado->imagen))
                    <img src="{{ asset('storage/' . $relacionado->imagen[0]) }}" class="card-img-top"
                        alt="{{ $relacionado->nombre }}">
                    @else
                    <img src="{{ asset('storage/' . $relacionado->imagen) }}" class="card-img-top"
                        alt="{{ $relacionado->nombre }}">
                    @endif
                    @else
                    <img src="{{ asset('images/placeholder.svg') }}" class="card-img-top"
                        alt="Sin imagen">
                    @endif
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $relacionado->nombre }}</h5>
                        <p class="card-text text-primary fw-bold">
                            @if ($relacionado->presentaciones->isNotEmpty())
                            ${{ number_format($relacionado->presentaciones->first()->precio_unitario, 2) }}
                            @else
                            ${{ number_format($relacionado->precio, 2) }}
                            @endif
                        </p>
                        <a href="{{ route('shop.producto', $relacionado->id) }}"
                            class="btn btn-sm btn-primary">Ver producto</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mainImage = document.getElementById('mainImage');
        const thumbnails = document.querySelectorAll('.thumbnail');

        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', () => {
                mainImage.src = thumb.src;
                thumbnails.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });

        const select = document.getElementById('presentacion');
        const precioSpan = document.getElementById('precioProducto');

        if (select && precioSpan) {
            select.addEventListener('change', () => {
                const precio = select.options[select.selectedIndex].dataset.precio;
                precioSpan.textContent = new Intl.NumberFormat('es-CO', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(precio);
            });
        }

        // Funciones para el control de cantidad
        window.incrementarCantidad = function() {
            const input = document.getElementById('cantidad');
            input.value = parseInt(input.value) + 1;
        }

        window.decrementarCantidad = function() {
            const input = document.getElementById('cantidad');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
    });
</script>
<script>
    function toggleDescription(event) {
        event.preventDefault();
        const preview = event.target.closest('.description-container').querySelector('.description-preview');
        const full = event.target.closest('.description-container').querySelector('.description-full');
        preview.classList.toggle('d-none');
        full.classList.toggle('d-none');
    }
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Manejar cambio de presentación
        const presentacionSelect = document.getElementById('presentacion_id');
        const precioProducto = document.getElementById('precioProducto');
        
        if (presentacionSelect) {
            presentacionSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const imagen = selectedOption.getAttribute('data-imagen');
                const precio = selectedOption.getAttribute('data-precio');

                // Actualizar precio
                if (precio && precioProducto) {
                    precioProducto.textContent = '$' + new Intl.NumberFormat('es-CO', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(precio);
                }

                // Actualizar imagen
                if (imagen) {
                    const mainImage = document.getElementById('mainImage');
                    mainImage.src = '{{ asset("storage/") }}/' + imagen;

                    // Actualizar thumbnail activo
                    document.querySelectorAll('.thumbnail').forEach(thumb => {
                        if (thumb.src.includes(imagen)) {
                            thumb.classList.add('active');
                        } else {
                            thumb.classList.remove('active');
                        }
                    });
                }
            });
        }
    });

    // Función para cambiar la imagen principal
    function changeMainImage(thumbnail) {
        const mainImage = document.getElementById('mainImage');
        mainImage.src = thumbnail.src;
        
        // Actualizar clase active
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');
    }
</script>
@endpush