<?php
return [
    'category_weights' => [
        'fast_moving' => env('WEIGHT_FAST_MOVING', 1.2),
        'premium'     => env('WEIGHT_PREMIUM', 0.6),
        'slow_moving' => env('WEIGHT_SLOW_MOVING', 0.4),
    ],
    'saw_weights' => [
        'c1_penjualan'  => env('SAW_W1', 0.45),
        'c2_stok'       => env('SAW_W2', 0.25),
        'c3_sentimen'   => env('SAW_W3', 0.15),
        'c4_trend'      => env('SAW_W4', 0.15),
    ],
];