<?php

namespace App\Services;

use App\DTOs\PredictionResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TrendAnalisis;

class GeminiService
{
    protected array $apiKeys = [];
    protected string $endpoint;

    public function __construct()
    {
        // Ambil semua key dan jadikan array
        $rawKeys = config('services.gemini.api_key', env('GEMINI_API_KEY')) ?? '';
        $this->apiKeys = array_filter(array_map('trim', explode(',', $rawKeys)));

        $this->endpoint = config('services.gemini.endpoint',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent'
        );
    }

    public static function analyzeBatch(array $opinions, string $sysPrompt): array
    {
        $instance = new self();
        $jumlah = count($opinions);
        
        $numbered = collect($opinions)
            ->map(fn ($op, $i) => "[" . ($i + 1) . "] " . trim($op))
            ->implode("\n");

        // 🔥 GABUNGKAN INSTRUKSI JUMLAH DENGAN PROMPT DARI DATABASE 🔥
        $finalSysPrompt = "TUGAS UTAMA: WAJIB kembalikan TEPAT " . $jumlah . " item dalam JSON array string.\n\n" . $sysPrompt;
        $userPrompt = "Sentimen untuk $jumlah opini:\n" . $numbered;

        // Looping API Key
        foreach ($instance->apiKeys as $index => $key) {
            $attempt = 0;
            $maxAttempts = 2; // Per key kita coba 2x

            while ($attempt < $maxAttempts) {
                try {
                    Log::info("Mencoba menggunakan API Key ke-" . ($index + 1));
                    
                    // Kirim ke Google
                    $response = $instance->callApiWithKey($key, $finalSysPrompt, $userPrompt);
                    
                    // Bersihin JSON
                    $clean = str_replace(['```json', '```'], '', $response);
                    preg_match('/\[[\s\S]*\]/', $clean, $matches);
                    $jsonPart = $matches[0] ?? '[]';
                    $parsed = json_decode($jsonPart, true);

                    if (is_array($parsed) && count($parsed) === $jumlah) {
                        return array_map(fn($v) => in_array(strtolower($v), ['positif', 'negatif']) ? strtolower($v) : 'netral', $parsed);
                    }

                    throw new \Exception("AI halu atau JSON cacat.");

                } catch (\Exception $e) {
                    $attempt++;
                    $errorCode = $e->getCode();

                    if ($errorCode == 429) {
                        Log::warning("API Key ke-" . ($index + 1) . " LIMIT HABIS (429). Ganti ke key berikutnya...");
                        if ($index == count($instance->apiKeys) - 1) {
                            throw new \Exception("SEMUA API KEY KENA LIMIT 429! Sistem gagal memproses.");
                        }
                        sleep(5);
                        break; 
                    }

                    Log::warning("Gagal di Key ke-" . ($index + 1) . " (Attempt $attempt): " . $e->getMessage());
                    
                    if ($attempt >= $maxAttempts && $index == count($instance->apiKeys) - 1) {
                        throw new \Exception("Semua API Key sudah dicoba dan gagal.");
                    }

                    sleep(2); 
                }
            }
        }

        return [];
    }

    // Fungsi helper buat nembak API dengan key spesifik
    private function callApiWithKey(string $key, string $sysPrompt, string $userPrompt): string
    {
        $url = "{$this->endpoint}?key={$key}";

        $response = Http::timeout(60)->post($url, [
            'system_instruction' => ['parts' => [['text' => $sysPrompt]]],
            'contents' => [['parts' => [['text' => $userPrompt]]]],
            'generationConfig' => [
                'temperature' => 0.1,
                'responseMimeType' => 'application/json',
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception("API Error: " . $response->status(), $response->status());
        }

        $json = $response->json();
        return $json['candidates'][0]['content']['parts'][0]['text'] ?? "";
    }


    // =========================================================================
    // 🔥 FUNGSI 2: TREN BANTEN (JSON KEY-VALUE) 🔥
    // =========================================================================
    public static function getTrendScores(array $motorNames): array
    {
        $instance = new self();
        $listMotor = implode(", ", $motorNames);

        $sysPrompt = "Kamu analis otomotif wilayah Banten. Kembalikan HANYA JSON Object (Key = Nama Motor, Value = Float 0.1 sampai 1.0).";
        
        $userPrompt = "Nilai tren motor ini di Banten (1.0=Sangat Laku, 0.4=Stabil, 0.1=Dead Stock):\n$listMotor";

        $rawResponse = $instance->callApi($sysPrompt, $userPrompt);

        $clean = str_replace(['```json', '```'], '', $rawResponse);
        preg_match('/\{[\s\S]*\}/', $clean, $matches);
        $jsonPart = $matches[0] ?? '{}';

        $parsed = json_decode($jsonPart, true);

        if (!is_array($parsed)) {
            Log::error("Gemini gagal parse tren Banten. Raw: " . $rawResponse);
            return []; 
        }

        return $parsed;
    }

    
    // =========================================================================
    // CORE API CALLER (DENGAN FIX AUTO-RETRY & THROW EXCEPTION)
    // =========================================================================
    private function callApi(string $systemInstruction, string $prompt): string
    {
        // 🔥 FIX-NYA DI SINI CAPT! Kita ambil satu key secara acak dari array apiKeys lu 🔥
        if (empty($this->apiKeys)) {
            throw new \Exception("API Key tidak ditemukan di .env!");
        }
        $key = $this->apiKeys[array_rand($this->apiKeys)];
        $url = "{$this->endpoint}?key={$key}";

        try {
            $response = Http::timeout(60)
                ->retry(3, 5000, function ($exception, $request) {
                    if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                        $status = $exception->response->status();
                        if (in_array($status, [500, 502, 503, 504])) {
                            Log::warning("Gemini sibuk (Error $status). Sistem mencoba ulang...");
                            return true;
                        }
                    }
                    if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                        return true;
                    }
                    return false;
                })
                ->post($url, [
                    'system_instruction' => ['parts' => [['text' => $systemInstruction]]],
                    'contents'         => ['parts' => [['text' => $prompt]]],
                    'generationConfig' => [
                        'temperature'      => 0.1, 
                        'maxOutputTokens'  => 1500,
                        'responseMimeType' => 'application/json',
                    ]
                ])->throw(); 

            $json = $response->json();
            return $json['candidates'][0]['content']['parts'][0]['text'] ?? "";

        } catch (\Illuminate\Http\Client\RequestException $e) {
            $status = $e->response->status();
            $pesanError = match ($status) {
                400 => "Error 400: Bad Request. Format prompt atau JSON lu ada yang salah.",
                401 => "Error 401: Unauthorized. API Key Gemini lu invalid atau belum di-set di .env!",
                429 => "Error 429: Too Many Requests. Kuota Token API lu abis atau lu nembak terlalu cepet!",
                500 => "Error 500: Internal Server Error. Server Google Gemini lagi mabok/down.",
                502 => "Error 502: Bad Gateway. Jaringan Google lagi bermasalah.",
                503 => "Error 503: High Demand. Server Gemini kepenuhan, tunggu beberapa saat lalu coba lagi.",
                504 => "Error 504: Gateway Timeout. Request kelamaan gak dijawab sama Google.",
                default => "Error $status: Gagal terhubung ke API Gemini dengan alasan tidak diketahui."
            };
            
            Log::error("API Gemini Failed: " . $pesanError);
            throw new \Exception($pesanError); 

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Koneksi Putus: " . $e->getMessage());
            throw new \Exception("Koneksi Putus! Cek internet server lu atau API Gemini sedang down total.");
        } catch (\Exception $e) {
            Log::error("Error Fatal: " . $e->getMessage());
            throw new \Exception("Terjadi kesalahan sistem: " . $e->getMessage());
        }
    }

    // =========================================================================
    // ENRICH ALASAN (FALLBACK KALO AI MABOK)
    // =========================================================================
    public function enrichAlasan(Collection $results): Collection
    {
        return $results->map(function (PredictionResult $result) {
            $result->alasan = $this->fallbackAlasan($result);
            return $result;
        });
    }

   public function fallbackAlasan(PredictionResult $r): string
   {
    $c           = $r->category ?? 'fast_moving';
    $s           = $r->stokSisa;
    $sales       = $r->salesScore;
    $sentiment   = $r->sentimentScore ?? 0.5;
    $trend       = $r->trendScore ?? 0.3;
    $rekomendasi = $r->rekomendasiJumlah ?? 0;

    $status = 'safe'; 

    $penjualanLabel = match (true) {
        $sales >= 0.4  => 'Tinggi',
        $sales >= 0.25 => 'Stabil',
        $sales >= 0.1  => 'Moderat',
        $sales >= 0.05 => 'Terbatas',
        default        => 'Rendah',
    };

    $sentimenLabel = match (true) {
        $sentiment >= 0.7 => 'Positif',
        $sentiment >= 0.4 => 'Netral',
        default           => 'Negatif',
    };

    $trendLabel = match (true) {
        $trend >= 0.7 => 'Naik',
        $trend >= 0.4 => 'Stabil',
        default       => 'Turun',
    };

    $stokLabel = '';
    $stokEfek  = '';

    $dangerLimit = 0;
    $overLimit   = 999;

    if ($c === 'fast_moving') {
        $dangerLimit = (int)env('RULE_FAST_DANGER_THRESHOLD', 5);
        $overLimit   = (int)env('RULE_FAST_OVERSTOCK_THRESHOLD', 15);
    } elseif ($c === 'slow_moving') {
        $dangerLimit = (int)env('RULE_SLOW_DANGER_THRESHOLD', 2);
        $overLimit   = (int)env('RULE_SLOW_OVERSTOCK_THRESHOLD', 5);
    } elseif ($c === 'premium') {
        $dangerLimit = (int)env('RULE_PREMIUM_DANGER_THRESHOLD', 1);
        $overLimit   = (int)env('RULE_PREMIUM_OVERSTOCK_THRESHOLD', 2);
    }

    if ($r->isNew) {
        $stokLabel = 'Unit display tersedia';
        $stokEfek  = 'stok awal disiapkan untuk display showroom';
    } else {
        [$stokLabel, $stokEfek] = match (true) {
            $s === 0           => ['Kosong', 'sistem mempertimbangkan pengisian ulang'],
            $s <= $dangerLimit => ["Sangat minim (sisa {$s})", 'sistem menaikkan prioritas pengadaan'],
            $s >= $overLimit   => ["Melampaui batas (sisa {$s})", 'risiko penumpukan, perlu pengendalian'],
            default            => ["Proporsional (sisa {$s})", 'berada pada level aman dan stabil'],
        };
    }

    $kesimpulan = '';

    if ($r->isNew) {
        $status = 'info';
        $kesimpulan = "PRODUK BARU: Alokasi {$rekomendasi} unit difokuskan untuk penetrasi pasar awal.";
        
    } elseif ($rekomendasi === 0) {
        $status = 'danger';
        $kesimpulan = "PENGADAAN DIHENTIKAN: Sistem tidak mengalokasikan unit baru. "
            . ($s > 0
                ? "Fokuskan penjualan pada sisa {$s} unit stok lama."
                : "Evaluasi keberlanjutan produk ini di showroom.");
                
    } else {
        if ($c === 'fast_moving') {
            if ($s >= $overLimit) {
                $status = 'danger';
                $kesimpulan = "PENGENDALIAN KETAT: Stok melebihi batas (≥ {$overLimit}). Pengadaan dibatasi {$rekomendasi} unit.";
            } else {
                $status = 'safe';
                $kesimpulan = "TINDAKAN OTOMATIS: Stok berada pada level aman. Alokasi {$rekomendasi} unit untuk menjaga perputaran.";
            }
        } else {
            if ($s <= $dangerLimit) {
                $status = 'review';
                $kesimpulan = "PRIORITAS DISPLAY: Stok kritis, alokasi {$rekomendasi} unit untuk menjaga eksistensi produk.";
            } elseif ($s >= $overLimit) {
                $status = 'danger';
                $kesimpulan = "PENGENDALIAN KETAT: Stok berlebih, pengadaan ditekan menjadi {$rekomendasi} unit.";
            } else {
                $status = 'review';
                $kesimpulan = "PERLU MONITORING: Stok proporsional. Pengadaan {$rekomendasi} unit bersifat selektif.";
            }
        }
    }

    if (!$r->isNew) {
        if ($trend >= 0.7) $kesimpulan .= " Tren pasar meningkat menjadi faktor pendukung.";
        elseif ($trend < 0.4) $kesimpulan .= " Tren pasar melemah, perlu kehati-hatian.";
    }

    $r->status = $status;

    return "
    <div style='line-height: 1.6;'>
        <strong>Penjualan (C1):</strong> {$penjualanLabel}<br>
        <strong>Kondisi Stok (C2):</strong> {$stokLabel}<br>
        <strong>Sentimen (C3):</strong> {$sentimenLabel}<br>
        <strong>Trend Pasar (C4):</strong> {$trendLabel}<br>
        <div style='margin-top: 8px; padding-top: 8px; border-top: 1px dashed #cbd5e1;'>
            <strong>Kesimpulan:</strong> {$kesimpulan}
        </div>
    </div>
    ";
   }
}