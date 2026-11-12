<?php

use App\Services\Tte\Handlers\BlkPesertaHandler;
use App\Services\Tte\Handlers\HiPpAjuanHandler;

return [
    'modules' => [
        'blk' => BlkPesertaHandler::class,
        'hi_pp' => HiPpAjuanHandler::class,
        // 'suket_k3' => App\Services\Tte\Handlers\SuketK3Handler::class,
    ],

    'ds' => [
        'url' => rtrim((string) env('TTE_DS_URL', 'https://ds.kaltimprov.go.id/api/v2'), '/'),
        'token' => env('TTE_DS_TOKEN'),
        'template' => env('TTE_DS_TEMPLATE'),
        'demo' => (bool) env('TTE_DS_DEMO', true),
        'simulate_disconnect' => (bool) env('TTE_DS_SIMULATE_DISCONNECT', false),
        'timeout' => (int) env('TTE_DS_TIMEOUT', 30),
    ],
];
