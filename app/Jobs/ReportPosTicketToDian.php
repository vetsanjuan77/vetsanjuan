<?php

namespace App\Jobs;

use App\Models\Factura;
use App\Services\Dian\DianDirectClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReportPosTicketToDian implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public int $facturaId)
    {
    }

    public function handle(DianDirectClient $client): void
    {
        $factura = Factura::with(['detalles', 'cliente'])->findOrFail($this->facturaId);

        try {
            $resp = $client->sendPosDocument($factura);

            $factura->estado_dian = $resp['status'] ?? 'enviado';
            $factura->cufe = $resp['cufe'] ?? null;
            $factura->zip_key = $resp['zip_key'] ?? null;
            $factura->qr = $resp['qr'] ?? null;
            $factura->numero_dian = $resp['numero_dian'] ?? null;
            $factura->tipo_documento = 'pos';
            $factura->respuesta_dian = $resp['raw'] ?? $resp;
            $factura->save();
        } catch (\Throwable $e) {
            Log::error('Error reportando POS electrónico a DIAN: ' . $e->getMessage(), [
                'factura_id' => $this->facturaId,
                'trace' => $e->getTraceAsString(),
            ]);

            $factura->estado_dian = 'error';
            $factura->respuesta_dian = [
                'exception' => $e->getMessage(),
            ];
            $factura->save();

            $this->fail($e);
        }
    }
}