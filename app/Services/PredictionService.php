<?php

namespace App\Services;

use App\DTOs\PredictionResult;
use App\Models\Motor;
use Illuminate\Support\Collection;


class PredictionService
{
    // =========================================================================
    // 🔥 CORE ENGINE PREDICTION (SAW + HEURISTIC + GAP FILLER) 🔥
    // =========================================================================
    public function predict(
        int $totalTarget,
        array $motorBaruIds = [],
        ?string $periodePenjualan = null,
        ?string $periodeStok = null,
        ?string $periodeOpini = null,
        ?string $periodeTrend = null
    ): Collection {

        $motors = $this->fetchMotors($periodePenjualan, $periodeStok, $periodeOpini, $periodeTrend);
        $results = $motors->map(fn($motor) => $this->scoreMotor($motor, $motorBaruIds));
        $results = $this->distributeProportional($results, $totalTarget);
        $results = $this->applyBusinessConstraints($results);
        $results = $this->fillTargetGap($results, $totalTarget);
        $results = $this->recalculateRealPercentage($results, $totalTarget);

        return $results->sortByDesc('finalScore')->values();
    }

    // =========================================================================
    // 1. FETCH DATA (EAGER LOADING)
    // =========================================================================
    private function fetchMotors(?string $periodePenjualan, ?string $periodeStok, ?string $periodeOpini, ?string $periodeTrend): Collection
    {
        return Motor::with([
            'penjualanAnalisis' => function ($query) use ($periodePenjualan) {
                if ($periodePenjualan) $query->where('dataset_name', $periodePenjualan);
            },
            'stok' => function ($query) use ($periodeStok) {
                if ($periodeStok) $query->where('snapshot_name', $periodeStok);
            },
            'opini' => function ($query) use ($periodeOpini) {
                if ($periodeOpini) $query->where('dataset_name', $periodeOpini);
            },
            'trend' => function ($query) use ($periodeTrend) {
                if ($periodeTrend) $query->where('periode', $periodeTrend);
            },
        ])->get();
    }

    // =========================================================================
    // 2. SAW SCORING ALGORITHM (BULLETPROOF + HYPE INJECTION)
    // =========================================================================
    private function scoreMotor(Motor $motor, array $motorBaruIds): PredictionResult
    {
        $isNew = in_array((int)$motor->id, array_map('intval', $motorBaruIds));

        // Ekstraksi Aman Anti-Error 500
        $penjualanData = $motor->getRelationValue('penjualanAnalisis');
        $penjualan = $penjualanData instanceof Collection ? $penjualanData->first() : $penjualanData;
        
        $stokData = $motor->getRelationValue('stok');
        $stok = $stokData instanceof Collection ? $stokData->first() : $stokData;

        $opiniData = $motor->getRelationValue('opini');
        if ($opiniData instanceof Collection) {
            $opini = $opiniData;
        } else {
            $opini = $opiniData ? collect([$opiniData]) : collect();
        }

        $trendData = $motor->getRelationValue('trend');
        $trendModel = $trendData instanceof Collection ? $trendData->first() : $trendData;

        $percent = (float) ($penjualan?->percent ?? 0);
        $stokSisa = (int) ($stok?->stok_sisa ?? 0);
        $trendScore = (float) ($trendModel?->skor_trend ?? 0.3);

        // 🔥 HYPE INJECTION (Biar Motor Baru kaga jeblok gara-gara kaga punya data)
        if ($isNew) {
            $salesScore = 0.85; 
            $stockScore = 0.90; 
            $sentimentScore = 0.85; 
            $jumlahPenjualan = 0; // Kaga kepake, buat dummy aja
        } else {
            $salesScore = $percent / 100;
            $jumlahPenjualan = max(1, (int) ($penjualan?->jumlah ?? 0));
            $stockScore = $this->calcStockScore($stokSisa, $jumlahPenjualan);
            $sentimentScore = $opini->isEmpty() ? 0.5 : ($opini->avg('score') + 3) / 6;
        }

        // 💥 RUMUS FINAL SAW KOMPREHENSIF 💥
    $finalScore = ((float)env('SAW_W1', 0.45) * $salesScore)
            + ((float)env('SAW_W2', 0.25) * $stockScore)
            + ((float)env('SAW_W3', 0.15) * $sentimentScore)
            + ((float)env('SAW_W4', 0.15) * $trendScore);
        // 🔥 HUKUMAN KASTA BANTEN 🔥
        $category = $motor->category ?? 'fast_moving';
$weightMap = [
    'fast_moving' => (float)env('WEIGHT_FAST_MOVING', 1.2),
    'premium'     => (float)env('WEIGHT_PREMIUM', 0.6),
    'slow_moving' => (float)env('WEIGHT_SLOW_MOVING', 0.4),
];        
        $finalScore = $finalScore * ($weightMap[$category] ?? 1.0);

        return new PredictionResult(
            motorId:           $motor->id,
            namaMotor:         $motor->nama,
            salesScore:        $salesScore,
            sentimentScore:    $sentimentScore,
            stockScore:        $stockScore,
            finalScore:        round($finalScore, 4),
            percent:           $percent,
            stokSisa:          $stokSisa,
            rekomendasiJumlah: 0,
            isNew:             $isNew,
            category:          $category,
            jumlahPenjualan:   $jumlahPenjualan ?? 0,
            trendScore:        $trendScore
        );
    }

    private function calcStockScore(int $stok, int $sales): float
    {
        if ($stok == 0) return 1.0;
        $ratio = $stok / $sales;
        if ($ratio <= 0.5) return 0.8;
        if ($ratio <= 1.0) return 0.5;
        if ($ratio <= 2.0) return 0.2;
        return 0.0;
    }

    // =========================================================================
    // 3. PROPORSIONAL BASIC
    // =========================================================================
    private function distributeProportional(Collection $results, int $totalTarget): Collection
{
    $totalScore = $results->sum('finalScore');
    if ($totalScore <= 0) return $results;

    return $results->map(function ($r) use ($totalScore, $totalTarget) {
        $raw = (int) round(($r->finalScore / $totalScore) * $totalTarget);

        // Pre-cap sebelum business rule jalan
        // supaya slow/premium tidak makan jatah fast_moving
        if ($r->category === 'slow_moving') {
            $raw = min($raw, (int) env('RULE_SLOW_OVERSTOCK_CAP', 3));
        }

        if ($r->category === 'premium') {
            $raw = min($raw, (int) env('RULE_PREMIUM_OVERSTOCK_CAP', 1));
        }

        $r->rekomendasiJumlah = $raw;
        return $r;
    });
}

    // =========================================================================
    // 4. BUSINESS RULES (SINKRON DENGAN BUKU SUCI LOGIKA KEPALA CABANG)
    // =========================================================================
    private function applyBusinessConstraints(Collection $results): Collection
{
    return $results->map(function ($r) {

        // SLOW MOVING
        if ($r->category === 'slow_moving') {
            if ($r->stokSisa >= (int)env('RULE_SLOW_OVERSTOCK_THRESHOLD', 5)) {
                $r->rekomendasiJumlah = min($r->rekomendasiJumlah, (int)env('RULE_SLOW_OVERSTOCK_CAP', 0));
            }
            if ($r->stokSisa <= (int)env('RULE_SLOW_DANGER_THRESHOLD', 0)) {
                $r->rekomendasiJumlah += (int)env('RULE_SLOW_DANGER_BOOST', 0);
            }
        }

        // PREMIUM
        if ($r->category === 'premium') {
            if ($r->stokSisa >= (int)env('RULE_PREMIUM_OVERSTOCK_THRESHOLD', 2)) {
                $r->rekomendasiJumlah = min($r->rekomendasiJumlah, (int)env('RULE_PREMIUM_OVERSTOCK_CAP', 0));
            }
            if ($r->stokSisa <= (int)env('RULE_PREMIUM_DANGER_THRESHOLD', 0)) {
                $r->rekomendasiJumlah += (int)env('RULE_PREMIUM_DANGER_BOOST', 0);
            }
        }

        // FAST MOVING
        if ($r->category === 'fast_moving') {
            if ($r->stokSisa >= (int)env('RULE_FAST_OVERSTOCK_THRESHOLD', 15)) {
                $r->rekomendasiJumlah = min($r->rekomendasiJumlah, (int)env('RULE_FAST_OVERSTOCK_CAP', 10));
            }
            if ($r->stokSisa <= (int)env('RULE_FAST_DANGER_THRESHOLD', 5)) {
                $r->rekomendasiJumlah += (int)env('RULE_FAST_DANGER_BOOST', 5);
            }
        }

        // MOTOR BARU — override semua
        if ($r->isNew) {
            $minDisplay = ($r->category === 'fast_moving')
                ? (int)env('RULE_NEW_FAST_MIN_DISPLAY', 15)
                : (int)env('RULE_NEW_OTHER_MIN_DISPLAY', 5);
            $r->rekomendasiJumlah = max($r->rekomendasiJumlah, $minDisplay);
        }

        return $r;
    });
}

    // =========================================================================
    // 5. 🔥 DYNAMIC GAP FILLER (ANTI MIO M3 OVERSTOCK AI) 🔥
    // =========================================================================
    // =========================================================================
    // 5. 🔥 DYNAMIC BALANCE RESOLVER (ANTI KURANG, ANTI JEBOL, + EXPLAINABILITY) 🔥
    // =========================================================================
    // =========================================================================
    // 5. 🔥 DYNAMIC BALANCE RESOLVER (LOGIKA ZERO-CHECKBOX = DISABLE) 🔥
    // =========================================================================
   // =========================================================================
    // 5. 🔥 DYNAMIC BALANCE RESOLVER (LOGIKA ZERO-CHECKBOX = DISABLE) 🔥
    // =========================================================================
    private function fillTargetGap(Collection $results, int $totalTarget): Collection
    {
        $currentTotal = $results->sum('rekomendasiJumlah');
        $variance = $totalTarget - $currentTotal;

        if ($variance === 0) return $results;

        // 🔥 KUMPULKAN SIAPA SAJA YANG DICENTANG DI UI (BULLETPROOF BOOLEAN PARSER)
        $allowedCategories = [];
        if (filter_var(env('GAP_FILLER_FAST', true), FILTER_VALIDATE_BOOLEAN)) {
            $allowedCategories[] = 'fast_moving';
        }
        if (filter_var(env('GAP_FILLER_PREMIUM', false), FILTER_VALIDATE_BOOLEAN)) {
            $allowedCategories[] = 'premium';
        }
        if (filter_var(env('GAP_FILLER_SLOW', false), FILTER_VALIDATE_BOOLEAN)) {
            $allowedCategories[] = 'slow_moving';
        }

        // 🔥 LOGIKA DEWA: JIKA TIDAK ADA YANG DICENTANG SAMA SEKALI = MATIKAN FITUR!
        if (empty($allowedCategories)) {
            return $results;
        }

        // 🟢 JIKA KURANG DARI TARGET (GAP FILLER - Nambah Unit)
        if ($variance > 0) {
            $gap = $variance;
            
            // PRIORITAS 1: Cari dari kategori RAJA yang stoknya AMAN (<= 12)
            $eligible = $results->filter(function ($r) use ($allowedCategories) {
                return in_array($r->category, $allowedCategories) && $r->stokSisa <= 12;
            })->sortByDesc('finalScore')->values();

            // PRIORITAS 2: Kalau kepaksa, cari yang stoknya maksimal 25 (Biar berani nyetok pas target raksasa)
            if ($eligible->isEmpty()) {
                $eligible = $results->filter(function ($r) use ($allowedCategories) {
                    return in_array($r->category, $allowedCategories) && $r->stokSisa <= 25;
                })->sortByDesc('finalScore')->values();
            }

            // PRIORITAS 3: Sikat aja semua motor kategori RAJA tanpa batas stok (Fallback terakhir biar target 3000 tembus!)
            if ($eligible->isEmpty()) {
                $eligible = $results->filter(function ($r) use ($allowedCategories) {
                    return in_array($r->category, $allowedCategories);
                })->sortByDesc('finalScore')->values();
            }

            // Mulai bagi-bagi kuota Round-Robin ke Raja-raja yang terpilih
            if ($eligible->isNotEmpty()) {
                $i = 0; $count = $eligible->count();
                while ($gap > 0) {
                    $eligible[$i % $count]->rekomendasiJumlah += 1;
                    $eligible[$i % $count]->gapAdjustment += 1; 
                    $gap--; $i++;
                }
            }
        } 
        // 🔴 JIKA JEBOL / MELEBIHI TARGET (EXCESS TRIMMER - Motong Unit)
        else {
            $excess = abs($variance);
            
            // Pemotongan HANYA berlaku untuk kategori yang DICENTANG
            $eligibleToTrim = $results->filter(function ($r) use ($allowedCategories) {
                return !$r->isNew && $r->rekomendasiJumlah > 1 && in_array($r->category, $allowedCategories);
            })->sortBy('finalScore')->values();

            if ($eligibleToTrim->isNotEmpty()) {
                $i = 0; $count = $eligibleToTrim->count(); $safeguard = 0; 
                while ($excess > 0 && $safeguard < 1000) {
                    if ($eligibleToTrim[$i % $count]->rekomendasiJumlah > 1) {
                        $eligibleToTrim[$i % $count]->rekomendasiJumlah -= 1;
                        $eligibleToTrim[$i % $count]->gapAdjustment -= 1; 
                        $excess--;
                    }
                    $i++; $safeguard++;
                }
            }
        }

        return $results;
    }

    // =========================================================================
    // 6. RECALCULATE PERCENTAGE
    // =========================================================================
    private function recalculateRealPercentage(Collection $results, int $totalTarget): Collection
    {
        return $results->map(function ($r) use ($totalTarget) {
            $r->percent = $totalTarget > 0 ? round(($r->rekomendasiJumlah / $totalTarget) * 100, 1) : 0;
            return $r;
        });
    }
}