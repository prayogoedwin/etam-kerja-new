<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    // 'characters' => ['2', '3', '4', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'm', 'n', 'p', 'q', 'r', 't', 'u', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'M', 'N', 'P', 'Q', 'R', 'T', 'U', 'X', 'Y', 'Z'],
    'characters' => ['2', '3', '4', '6', '7', '8', '9', 
                     'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 
                     'J', 'M', 'N', 'P', 'Q', 'R', 'T', 'U', 
                     'X', 'Y', 'Z'], // Hanya huruf besar dan angka, kecuali '0' dan 'O'
    'default' => [
        'length' => 4,
        'width' => 240, // Lebar gambar CAPTCHA (diperbesar 2x)
        'height' => 72, // Tinggi gambar CAPTCHA (diperbesar 2x)
        'quality' => 90,
        'math' => false,
        'expire' => 60,
        'encrypt' => false,
        'bgColor' => '#ffffff', // Latar belakang putih
        'fontColors' => ['#000000'], // Warna font hitam
    ],
    'math' => [
        'length' => 4,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
    ],

    'flat' => [
        'length' => 4,
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        'lines' => 0,
        'bgImage' => false,
        'bgColor' => '#ecf2f4',
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#f57c00', '#795548'],
     
        'contrast' => -5,
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 12,
        'sharpen' => 10,
        'blur' => 2,
        'invert' => true,
        'contrast' => -5,
    ]
];
