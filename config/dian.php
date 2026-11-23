<?php

return [
    // Integración directa con DIAN (sin proveedor tecnológico)
    'direct' => [
        'base_url' => env('DIAN_DIRECT_BASE_URL', 'https://vpfe-hab.dian.gov.co'),
        'endpoints' => [
            // Personaliza rutas reales según anexo técnico / API pública
            'send_fe' => env('DIAN_DIRECT_SEND_FE_PATH', '/api/v1/bill/send'),
            'send_pos' => env('DIAN_DIRECT_SEND_POS_PATH', '/api/v1/document/send-pos'),
            'status_zip' => env('DIAN_DIRECT_STATUS_ZIP_PATH', '/api/v1/status/zip'),
        ],

        // Datos del emisor / software registrado
        'nit' => env('DIAN_EMISOR_NIT'),
        'software_id' => env('DIAN_SOFTWARE_ID'),
        'software_pin' => env('DIAN_SOFTWARE_PIN'),

        // Certificado digital (PFX/P12) para firma XAdES-BES
        'cert_pfx_path' => env('DIAN_CERT_PFX_PATH'),
        'cert_pfx_password' => env('DIAN_CERT_PFX_PASSWORD'),

        // Ambiente: habilitacion | produccion
        'environment' => env('DIAN_AMBIENTE', 'habilitacion'),

        // Identificadores de set de pruebas (habilitación)
        'pos_test_set_id' => env('DIAN_POS_TEST_SET_ID'),
        'fe_test_set_id' => env('DIAN_FE_TEST_SET_ID'),

        // Configuración HTTP
        'timeout' => env('DIAN_HTTP_TIMEOUT', 20),
    ],

    // Configuración previa de proveedor tecnológico (se conserva por compatibilidad)
    'provider' => [
        'base_url' => env('DIAN_PROVIDER_BASE_URL', ''),
        'api_key' => env('DIAN_PROVIDER_API_KEY'),
        'api_secret' => env('DIAN_PROVIDER_API_SECRET'),
        'software_id' => env('DIAN_SOFTWARE_ID'),
        'software_pin' => env('DIAN_SOFTWARE_PIN'),
        'environment' => env('DIAN_AMBIENTE', 'habilitacion'),
        'pos_test_set_id' => env('DIAN_POS_TEST_SET_ID'),
        'fe_test_set_id' => env('DIAN_FE_TEST_SET_ID'),
        'timeout' => env('DIAN_HTTP_TIMEOUT', 20),
    ],
];