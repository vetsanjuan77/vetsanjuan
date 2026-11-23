<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected string $accessToken;

    public function __construct()
    {
        $this->accessToken = (string) config('services.mercadopago.access_token');
    }

    public function createPreference(array $items, array $backUrls, ?array $payer = null, ?string $externalReference = null, ?string $notificationUrl = null): array
    {
        $payload = [
            'items' => array_map(function ($it) {
                return [
                    'title' => (string) ($it['title'] ?? $it['name'] ?? 'Item'),
                    'quantity' => (int) ($it['quantity'] ?? 1),
                    'unit_price' => (float) ($it['unit_price'] ?? $it['price'] ?? 0),
                    'currency_id' => (string) ($it['currency_id'] ?? 'COP'),
                ];
            }, $items),
            'back_urls' => [
                'success' => (string) ($backUrls['success'] ?? ''),
                'failure' => (string) ($backUrls['failure'] ?? ''),
                'pending' => (string) ($backUrls['pending'] ?? ''),
            ],
            'auto_return' => 'approved',
        ];

        if ($externalReference) {
            $payload['external_reference'] = $externalReference;
        }
        if ($notificationUrl) {
            $payload['notification_url'] = $notificationUrl;
        }
        if ($payer) {
            $payload['payer'] = $payer;
        }

        $res = Http::withToken($this->accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$res->successful()) {
            Log::error('MercadoPago preference creation failed', [
                'status' => $res->status(),
                'body' => $res->body(),
            ]);
            throw new \RuntimeException('No se pudo crear la preferencia de Mercado Pago.');
        }

        return $res->json();
    }
}