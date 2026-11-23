<?php

namespace App\Services\Dian;

use App\Models\Factura;
use App\Services\Dian\Ubl\UblInvoiceBuilder;
use App\Services\Dian\Ubl\UblPosBuilder;
use App\Services\Dian\Xades\XadesSigner;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class DianDirectClient
{
    protected string $baseUrl;
    protected array $endpoints;
    protected string $environment;
    protected int $timeout;
    protected ?string $softwareId;
    protected ?string $softwarePin;
    protected ?string $posTestSetId;
    protected ?string $feTestSetId;

    public function __construct(
        protected UblInvoiceBuilder $feBuilder = new UblInvoiceBuilder(),
        protected UblPosBuilder $posBuilder = new UblPosBuilder(),
        protected XadesSigner $signer = new XadesSigner(),
    ) {
        $cfg = config('dian.direct');
        $this->baseUrl = rtrim((string) ($cfg['base_url'] ?? ''), '/');
        $this->endpoints = (array) ($cfg['endpoints'] ?? []);
        $this->environment = (string) ($cfg['environment'] ?? 'habilitacion');
        $this->timeout = (int) ($cfg['timeout'] ?? 20);
        $this->softwareId = $cfg['software_id'] ?? null;
        $this->softwarePin = $cfg['software_pin'] ?? null;
        $this->posTestSetId = $cfg['pos_test_set_id'] ?? null;
        $this->feTestSetId = $cfg['fe_test_set_id'] ?? null;
    }

    protected function http()
    {
        return Http::timeout($this->timeout)->acceptJson();
    }

    public function sendFeInvoice(Factura $factura): array
    {
        $xml = $this->feBuilder->build($factura);
        $signedXml = $this->signer->sign($xml);
        $zipBytes = $this->toZip($signedXml, $this->xmlFileName($factura, 'fe'));

        $endpoint = $this->endpoint('send_fe');
        $response = $this->http()->asMultipart()->post($endpoint, [
            [
                'name' => 'file',
                'contents' => $zipBytes,
                'filename' => $this->zipFileName($factura, 'fe'),
            ],
            [
                'name' => 'testSetId',
                'contents' => (string) ($this->feTestSetId ?? ''),
            ],
            [
                'name' => 'softwareId',
                'contents' => (string) ($this->softwareId ?? ''),
            ],
            [
                'name' => 'environment',
                'contents' => (string) $this->environment,
            ],
        ]);

        return $this->normalizeResponse($response->json());
    }

    public function sendPosDocument(Factura $factura): array
    {
        $xml = $this->posBuilder->build($factura);
        $signedXml = $this->signer->sign($xml);
        $zipBytes = $this->toZip($signedXml, $this->xmlFileName($factura, 'pos'));

        $endpoint = $this->endpoint('send_pos');
        $response = $this->http()->asMultipart()->post($endpoint, [
            [
                'name' => 'file',
                'contents' => $zipBytes,
                'filename' => $this->zipFileName($factura, 'pos'),
            ],
            [
                'name' => 'testSetId',
                'contents' => (string) ($this->posTestSetId ?? ''),
            ],
            [
                'name' => 'softwareId',
                'contents' => (string) ($this->softwareId ?? ''),
            ],
            [
                'name' => 'environment',
                'contents' => (string) $this->environment,
            ],
        ]);

        return $this->normalizeResponse($response->json());
    }

    public function statusZip(string $zipKey): array
    {
        $endpoint = $this->endpoint('status_zip');
        $response = $this->http()->post($endpoint, [
            'zipKey' => $zipKey,
        ]);
        return $response->json() ?? [];
    }

    protected function endpoint(string $key): string
    {
        $path = (string) ($this->endpoints[$key] ?? '');
        return $this->baseUrl . $path;
    }

    protected function xmlFileName(Factura $factura, string $type): string
    {
        $prefix = $type === 'pos' ? 'POS' : 'FE';
        return $prefix . '_' . ($factura->numero_factura ?: $factura->id) . '.xml';
    }

    protected function zipFileName(Factura $factura, string $type): string
    {
        return str_replace('.xml', '.zip', $this->xmlFileName($factura, $type));
    }

    protected function toZip(string $xml, string $xmlFileName): string
    {
        $tmpZip = tempnam(sys_get_temp_dir(), 'dian_zip_');
        $zip = new ZipArchive();
        $zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString($xmlFileName, $xml);
        $zip->close();
        $bytes = file_get_contents($tmpZip);
        @unlink($tmpZip);
        return $bytes ?: '';
    }

    protected function normalizeResponse(?array $response): array
    {
        $response = $response ?? [];
        $status = $response['StatusDescription'] ?? $response['status'] ?? null;
        $isValid = (bool) ($response['IsValid'] ?? $response['success'] ?? false);

        return [
            'status' => $isValid ? ($status ?: 'aceptado') : ($status ?: 'rechazado'),
            'cufe' => $response['cufe'] ?? $response['Cufe'] ?? null,
            'zip_key' => $response['ZipKey'] ?? $response['zipKey'] ?? null,
            'qr' => $response['qr'] ?? null,
            'document_id' => $response['documentId'] ?? $response['id'] ?? null,
            'numero_dian' => $response['number'] ?? null,
            'tipo_documento' => $response['documentType'] ?? null,
            'errors' => $response['ErrorsList'] ?? $response['errors'] ?? null,
            'raw' => $response,
        ];
    }
}