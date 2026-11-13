<?php

use App\Services\Tte\Handlers\BlkPesertaHandler;
use App\Services\Tte\Handlers\HiPpAjuanHandler;
use App\Services\Tte\Handlers\SuketK3Handler;

return [
    'modules' => [
        'blk' => BlkPesertaHandler::class,
        'hi_pp' => HiPpAjuanHandler::class,
        'suket_k3' => SuketK3Handler::class,
    ],

    'ds' => [
        'url' => rtrim((string) env('TTE_DS_URL', 'https://ds.kaltimprov.go.id/api/v2'), '/'),
        'token' => env('TTE_DS_TOKEN'),
        'template' => env('TTE_DS_TEMPLATE'),
        'demo' => (bool) env('TTE_DS_DEMO', true),
        'simulate_disconnect' => (bool) env('TTE_DS_SIMULATE_DISCONNECT', false),
        'timeout' => (int) env('TTE_DS_TIMEOUT', 30),
        'timeout_unduh' => (int) env('TTE_DS_TIMEOUT_UNDUH', 90),
        'callback_wait' => (int) env('TTE_DS_CALLBACK_WAIT', 8),
    ],
];
