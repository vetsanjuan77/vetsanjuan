<?php

namespace App\Services\Dian;

use Illuminate\Support\Facades\Http;
use App\Models\Factura;
use App\Models\DetalleFactura;

class DianProviderClient
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ?string $apiSecret;
    protected string $environment;
    protected int $timeout;
    protected ?string $softwareId;
    protected ?string $softwarePin;
    protected ?string $posTestSetId;
    protected ?string $feTestSetId;

    public function __construct()
    {
        $cfg = config('dian.provider');
        $this->baseUrl = rtrim((string) ($cfg['base_url'] ?? ''), '/');
        $this->apiKey = $cfg['api_key'] ?? null;
        $this->apiSecret = $cfg['api_secret'] ?? null;
        $this->environment = (string) ($cfg['environment'] ?? 'habilitacion');
        $this->timeout = (int) ($cfg['timeout'] ?? 20);
        $this->softwareId = $cfg['software_id'] ?? null;
        $this->softwarePin = $cfg['software_pin'] ?? null;
        $this->posTestSetId = $cfg['pos_test_set_id'] ?? null;
        $this->feTestSetId = $cfg['fe_test_set_id'] ?? null;
    }

    protected function http()
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->apiKey) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        return Http::withHeaders($headers)->timeout($this->timeout);
    }

    /**
     * Envía una factura electrónica (FE) al proveedor tecnológico.
     */
    public function sendInvoice(Factura $factura): array
    {
        $payload = $this->buildInvoicePayload($factura, 'fe');
        $endpoint = $this->baseUrl . '/fe/invoices';
        $response = $this->http()->post($endpoint, $payload);
        return $this->normalizeResponse($response->json());
    }

    /**
     * Reporta un documento equivalente electrónico POS al proveedor tecnológico.
     */
    public function reportPos(Factura $factura): array
    {
        $payload = $this->buildInvoicePayload($factura, 'pos');
        $endpoint = $this->baseUrl . '/pos/documents';
        $response = $this->http()->post($endpoint, $payload);
        return $this->normalizeResponse($response->json());
    }

    /**
     * Consulta estado en el proveedor/DIAN (si el proveedor expone este endpoint).
     */
    public function getStatus(string $documentId, string $type = 'pos'): array
    {
        $endpoint = $this->baseUrl . '/' . $type . '/documents/' . $documentId . '/status';
        $response = $this->http()->get($endpoint);
        return $response->json() ?? [];
    }

    /**
     * Construye el payload genérico para FE/POS a partir de la factura y sus detalles.
     * Adaptar según el proveedor (nombres de campos/rutas pueden variar).
     */
    protected function buildInvoicePayload(Factura $factura, string $type = 'fe'): array
    {
        $factura->loadMissing(['cliente', 'detalles.producto', 'detalles.presentacion']);

        $cliente = $factura->cliente;
        $sucursalId = $factura->sucursal_id;

        $lines = [];
        foreach ($factura->detalles as $det) {
            $productoNombre = optional($det->producto)->nombre;
            $presentacionNombre = optional($det->presentacion)->nombre;

            $lines[] = [
                'sku' => $det->presentacion_id ?? $det->producto_id,
                'description' => trim(($presentacionNombre ?: $productoNombre) ?? 'Item'),
                'quantity' => (float) $det->cantidad,
                'unit_price' => (float) $det->precio_unitario,
                'discount' => (float) ($det->descuento ?? 0),
                'subtotal' => (float) ($det->subtotal ?? ($det->cantidad * $det->precio_unitario)),
                'total' => (float) ($det->total ?? ($det->cantidad * $det->precio_unitario - ($det->descuento ?? 0))),
                // Campos sugeridos para DIAN (el proveedor los mapea a UBL):
                'measurement_unit' => 'NIU',
                'tax_rate' => 0.0, // Ajustar si manejan IVA por línea
            ];
        }

        $header = [
            'type' => $type, // fe | pos
            'environment' => $this->environment, // habilitacion | produccion
            'branch_id' => $sucursalId,
            'software_id' => $this->softwareId,
            'software_pin' => $this->softwarePin,
            'test_set_id' => $type === 'pos' ? $this->posTestSetId : $this->feTestSetId,
            'issue_date' => optional($factura->fecha_emision)->format('Y-m-d\TH:i:sP') ?? now()->format('Y-m-d\TH:i:sP'),
            'payment_means' => $factura->metodo_pago,
            'internal_number' => $factura->numero_factura ?? null,
        ];

        $buyer = [
            'name' => trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: 'Consumidor Final',
            'email' => $cliente->email ?? null,
            'phone' => $cliente->telefono ?? null,
            'address' => $cliente->direccion ?? null,
            // Si su modelo posee identificación tributaria, agréguela aquí:
            'id_type' => 'CC', // Cambiar según real
            'id_number' => method_exists($cliente, 'getAttribute') ? ($cliente->getAttribute('documento') ?? (string) $cliente->id) : (string) $cliente->id,
        ];

        $totals = [
            'subtotal' => (float) ($factura->subtotal ?? 0),
            'taxes' => (float) ($factura->impuestos ?? 0),
            'discount' => (float) ($factura->descuento ?? 0),
            'total' => (float) ($factura->total ?? 0),
            'currency' => 'COP',
        ];

        return [
            'header' => $header,
            'buyer' => $buyer,
            'lines' => $lines,
            'totals' => $totals,
            // Campos libres útiles para trazabilidad
            'meta' => [
                'factura_id' => $factura->id,
                'sucursal_id' => $sucursalId,
                'user_id' => $factura->getAttribute('user_id'),
            ],
        ];
    }

    /**
     * Normaliza la respuesta del proveedor para almacenamiento homogéneo.
     */
    protected function normalizeResponse(?array $response): array
    {
        $response = $response ?? [];
        return [
            'status' => $response['status'] ?? ($response['success'] ? 'aceptado' : 'rechazado' ?? 'desconocido'),
            'cufe' => $response['cufe'] ?? $response['cufe_code'] ?? null,
            'zip_key' => $response['zipKey'] ?? $response['zip_key'] ?? null,
            'qr' => $response['qr'] ?? null,
            'document_id' => $response['documentId'] ?? $response['id'] ?? null,
            'numero_dian' => $response['number'] ?? null,
            'tipo_documento' => $response['documentType'] ?? null,
            'errors' => $response['errors'] ?? $response['error'] ?? null,
            'raw' => $response,
        ];
    }
}