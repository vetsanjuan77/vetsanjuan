<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo {{ $factura->numero_factura ?? ('FAC-' . $factura->id) }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        .container { max-width: 900px; margin: 20px auto; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .meta { margin-top: 8px; font-size: 12px; color: #555; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        th { background: #f5f5f5; text-align: left; }
        .totales { margin-top: 20px; display: grid; grid-template-columns: 1fr 200px; }
        .right { text-align: right; }
        .print-btn { margin-top: 12px; }
        .qr-box { display: flex; align-items: center; gap: 12px; }
        .qr-meta { font-size: 11px; color: #444; }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>Recibo / Factura</h1>
            <div class="meta">
                <div><strong>Número:</strong> {{ $factura->numero_factura }}</div>
                <div><strong>Fecha emisión:</strong> {{ optional($factura->fecha_emision)->format('d/m/Y H:i') }}</div>
                <div><strong>Estado:</strong> {{ ucfirst($factura->estado) }}</div>
            </div>
        </div>
        <div>
            <button class="print-btn" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    {{-- QR DIAN / verificación --}}
    <div style="margin-top: 12px;">
        @php
            use Illuminate\Support\Str;
            $qrContent = $factura->qr ?? null;
            $qrIsSvg = $qrContent && Str::startsWith($qrContent, '<svg');
            $qrIsData = $qrContent && Str::startsWith($qrContent, 'data:image');
            $qrIsHttp = $qrContent && Str::startsWith($qrContent, ['http://','https://']);
            $qrSrc = null;
            if ($qrContent) {
                if ($qrIsSvg) {
                    // SVG inline
                } elseif ($qrIsData || $qrIsHttp) {
                    $qrSrc = $qrContent;
                } else {
                    $qrSrc = 'data:image/png;base64,' . $qrContent;
                }
            } else {
                // Fallback: generar QR con URL de verificación interna del recibo
                $verifyUrl = url('/facturas/' . $factura->id . '/recibo');
                $qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' . urlencode($verifyUrl);
            }
        @endphp

        <div class="qr-box">
            @if($qrIsSvg)
                {!! $qrContent !!}
            @elseif(!empty($qrSrc))
                <img src="{{ $qrSrc }}" alt="QR" style="width:140px;height:140px;border:1px solid #eee;" />
            @endif
            <div class="qr-meta">
                <div><strong>NIT Emisor:</strong> {{ config('dian.direct.nit') ?? 'N/A' }}</div>
                @if(!empty($factura->numero_dian))
                    <div><strong>Número DIAN:</strong> {{ $factura->numero_dian }}</div>
                @endif
                @if(!empty($factura->cufe))
                    <div><strong>CUFE:</strong> {{ $factura->cufe }}</div>
                @endif
                @if(!empty($factura->estado_dian))
                    <div><strong>Estado DIAN:</strong> {{ ucfirst($factura->estado_dian) }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid">
        <div>
            <h3 style="margin:0 0 6px 0; font-size:14px;">Cliente</h3>
            <div><strong>Nombre:</strong> {{ optional($factura->cliente)->nombre }} {{ optional($factura->cliente)->apellido }}</div>
            <div><strong>Teléfono:</strong> {{ optional($factura->cliente)->telefono }}</div>
            <div><strong>Email:</strong> {{ optional($factura->cliente)->email }}</div>
            <div><strong>Dirección:</strong> {{ optional($factura->cliente)->direccion }}</div>
        </div>
        <div>
            <h3 style="margin:0 0 6px 0; font-size:14px;">Sucursal</h3>
            <div><strong>ID sucursal:</strong> {{ $factura->sucursal_id }}</div>
            <div><strong>Método pago:</strong> {{ $factura->metodo_pago ?? 'efectivo' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Presentación</th>
                <th class="right">Cantidad</th>
                <th class="right">Precio unitario</th>
                <th class="right">Descuento</th>
                <th class="right">Total línea</th>
            </tr>
        </thead>
        <tbody>
        @foreach($factura->detalles()->with(['producto','presentacion'])->get() as $d)
            <tr>
                <td>{{ optional($d->producto)->nombre ?? 'Producto' }}</td>
                <td>{{ optional($d->presentacion)->nombre ?? '-' }}</td>
                <td class="right">{{ number_format($d->cantidad, 0, ',', '.') }}</td>
                <td class="right">${{ number_format($d->precio_unitario, 2, ',', '.') }}</td>
                <td class="right">${{ number_format($d->descuento, 2, ',', '.') }}</td>
                <td class="right">${{ number_format($d->total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totales">
        <div></div>
        <div>
            <table>
                <tr>
                    <th>Subtotal</th>
                    <td class="right">${{ number_format($factura->subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Impuestos</th>
                    <td class="right">${{ number_format($factura->impuestos, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Descuento</th>
                    <td class="right">-${{ number_format($factura->descuento, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td class="right"><strong>${{ number_format($factura->total, 2, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    @if(!empty($factura->observaciones))
        <div style="margin-top:16px; font-size:12px; color:#555;">
            <strong>Observaciones:</strong>
            <div>{{ $factura->observaciones }}</div>
        </div>
    @endif
</div>
</body>
</html>