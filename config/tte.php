<?php

use App\Services\Tte\Handlers\BlkPesertaHandler;

return [
    'modules' => [
        'blk' => BlkPesertaHandler::class,
        // 'hi_pp' => App\Services\Tte\Handlers\HiPpHandler::class,
        // 'suket_k3' => App\Services\Tte\Handlers\SuketK3Handler::class,
    ],

    'bsre' => [
        'demo' => (bool) env('TTE_BSRE_DEMO', true),
        'demo_passphrase' => env('TTE_DEMO_PASSPHRASE', 'rahasia-tte'),
        'simulate_disconnect' => (bool) env('TTE_BSRE_SIMULATE_DISCONNECT', false),
        'url' => env('TTE_BSRE_URL'),
        'timeout' => (int) env('TTE_BSRE_TIMEOUT', 30),
    ],
];
