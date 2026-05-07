<?php

namespace App\DTOs;

class PredictionResult
{
    public function __construct(
        public int $motorId,
        public string $namaMotor,
        public float $salesScore,
        public float $sentimentScore,
        public float $stockScore,
        public float $finalScore,
        public float $percent,
        public float $trendScore = 0.3,
        public int $stokSisa,
        public int $rekomendasiJumlah,
        public bool $isNew,
        public string $category,
        public ?string $alasan = null,
        public int $jumlahPenjualan = 0,
        public string $status = 'safe',
        public int $gapAdjustment = 0,
        
    ) {}
}