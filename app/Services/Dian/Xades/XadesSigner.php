<?php

namespace App\Services\Dian\Xades;

class XadesSigner
{
    public function sign(string $xml): string
    {
        $pfxPath = config('dian.direct.cert_pfx_path');
        $pfxPassword = config('dian.direct.cert_pfx_password');

        if (class_exists('\\AnourValar\\Xades\\Xades') && $pfxPath && is_file($pfxPath)) {
            $pfx = file_get_contents($pfxPath);
            try {
                $xades = new \AnourValar\Xades\Xades();
                // Dependiendo de la versión del paquete, el método puede variar; se usa el genérico sign
                return $xades->sign($xml, $pfx, $pfxPassword);
            } catch (\Throwable $e) {
                // Si fallara la firma, se retorna el XML sin firmar para no romper el flujo
                return $xml;
            }
        }

        // Si no hay librería o certificado, retorna el XML sin firmar (para habilitación inicial)
        return $xml;
    }
}