<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Motor;
use App\Models\Opini;
use App\Models\Setting; 
use App\Models\Prompt; // 🔥 WAJIB DITAMBAHKAN UNTUK BANK PROMPT
use Illuminate\Http\Request;
use App\Services\GeminiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OpiniController extends Controller
{
    public function index()
    {
        $datasetOpini = Opini::select('dataset_name')->distinct()->orderBy('dataset_name')->pluck('dataset_name');
        return view('opini', compact('datasetOpini'));
    }

    // =========================
    // 🔵 UPLOAD → PREVIEW
    // =========================
    public function upload(Request $request)
    {
        if (!$request->hasFile('file')) {
            return back()->with('error', 'File belum dipilih');
        }

        try {
            $data = Excel::toArray([], $request->file('file'));
            $rows = $data[0] ?? [];

            if (empty($rows)) {
                return back()->with('error', 'File kosong / tidak terbaca');
            }

            // =========================
            // 🔥 HEADER DETECTION
            // =========================
            $required = ['tanggal', 'nama', 'motor', 'opini'];

            $headerIndex = null;
            $headerMap   = [];

            foreach ($rows as $i => $row) {
                $normalized = array_map(fn($h) => strtolower(trim($h)), $row);
                $found = 0;

                foreach ($required as $col) {
                    if (in_array($col, $normalized)) {
                        $found++;
                    }
                }

                if ($found >= 3) {
                    $headerIndex = $i;
                    foreach ($normalized as $colIndex => $colName) {
                        if (in_array($colName, $required)) {
                            $headerMap[$colName] = $colIndex;
                        }
                    }
                    break;
                }
            }

            if ($headerIndex === null) {
                return back()->with('error', 'Header tidak ditemukan / format bukan opini');
            }

            // =========================
            // 🔥 PARSE DATA
            // =========================
            $preview = [];

            foreach ($rows as $i => $row) {
                if ($i <= $headerIndex) continue;

                $rawTanggal = $row[$headerMap['tanggal']] ?? null;
                $nama       = trim($row[$headerMap['nama']] ?? '');
                $motorNama  = strtolower(trim($row[$headerMap['motor']] ?? ''));
                $opini      = trim($row[$headerMap['opini']] ?? '');

                if (!$nama || !$motorNama || !$opini) continue;

                // 🔥 FIX tanggal
                try {
                    if (is_numeric($rawTanggal)) {
                        $tanggal = Date::excelToDateTimeObject($rawTanggal)->format('Y-m-d');
                    } else {
                        $parsed = strtotime($rawTanggal);
                        $tanggal = $parsed ? date('Y-m-d', $parsed) : null;
                    }
                } catch (\Exception $e) {
                    $tanggal = null;
                }

                // 🔥 MATCH MOTOR
                $motor = Motor::all()->first(function ($m) use ($motorNama) {
                    $db = strtolower(str_replace(' ', '', $m->nama));
                    $excel = strtolower(str_replace(' ', '', $motorNama));
                    return str_contains($db, $excel);
                });

                if (!$motor) continue;

                $preview[] = [
                    'tanggal'  => $tanggal,
                    'nama'     => $nama,
                    'motor'    => $motor->nama,
                    'motor_id' => $motor->id,
                    'opini'    => $opini,
                    'ai'       => null // 🔥 placeholder AI
                ];
            }

            if (empty($preview)) {
                return back()->with('error', 'Data tidak valid / tidak cocok dengan motor');
            }

            session([
                'preview_opini' => $preview,
                'dataset_name'  => $request->dataset_name,
                'saved_opini'   => false
            ]);

            return back()->with('success', 'Preview opini berhasil ditampilkan');

        } catch (\Exception $e) {
            return back()->with('error', 'Format file salah / tidak terbaca');
        }
    }

    // =========================
    // 🟢 SIMPAN + AI (DENGAN DB TRANSACTION)
    // =========================
    public function simpan()
    {
        $data = session('preview_opini');
        $dataset = session('dataset_name');

        if (!$data) {
            return back()->with('error', 'Tidak ada data untuk disimpan');
        }

        set_time_limit(0);

        $allOpinions = collect($data);
        $chunks = $allOpinions->chunk(15); // 💡 Chunk per 15 biar aman

        $newPreview = [];

        // ==========================================================
        // 🔥 1. AMBIL PROMPT DARI BANK PROMPT (DI LUAR LOOPING) 🔥
        // ==========================================================
        $activeId = Setting::where('key', 'active_prompt_id')->value('value');
        $promptDb = $activeId ? Prompt::find($activeId) : null;
        
        // Jaring pengaman (Fallback) jika database diclear atau prompt terhapus
        $savedPrompt = $promptDb 
            ? $promptDb->isi_prompt 
            : "Kamu adalah sistem analisis sentimen NLP canggih otomotif Banten. Kembalikan HANYA JSON array string berisi 'positif', 'negatif', atau 'netral'.";

        // 🔥 MULAI DATABASE TRANSACTION (Cukup SATU KALI Saja)
        DB::beginTransaction();

        try {
            foreach ($chunks as $index => $chunk) {
                $opiniList = $chunk->pluck('opini')->toArray();

                // 🔥 2. OPER $savedPrompt KE DALAM FUNGSI GEMINI 🔥
                $sentiments = GeminiService::analyzeBatch($opiniList, $savedPrompt);

                // =========================
                // 🔥 NORMALISASI + SAFETY
                // =========================
                $sentiments = collect($sentiments)
                    ->map(function ($v) {
                        $v = strtolower(trim($v));
                        return match ($v) {
                            'positif', 'positive' => 'positif',
                            'negatif', 'negative' => 'negatif',
                            default               => 'netral',
                        };
                    })
                    ->values()
                    ->toArray();

                // 🔥 HANDLE KEKURANGAN
                if (count($sentiments) < count($chunk)) {
                    $sentiments = array_pad($sentiments, count($chunk), 'netral');
                }

                // 🔥 HANDLE KELEBIHAN
                if (count($sentiments) > count($chunk)) {
                    $sentiments = array_slice($sentiments, 0, count($chunk));
                }

                foreach ($chunk->values() as $i => $item) {
                    $sentiment = $sentiments[$i] ?? 'netral';
                    $score = match($sentiment) {
                        'positif' => 3,
                        'negatif' => -3,
                        default   => 0,
                    };

                    Opini::create([
                        'motor_id'     => $item['motor_id'],
                        'nama'         => $item['nama'],
                        'isi'          => $item['opini'],
                        'tanggal'      => $item['tanggal'],
                        'dataset_name' => $dataset ?? 'default',
                        'sentiment'    => $sentiment,
                        'score'        => $score
                    ]);

                    $item['ai'] = $sentiment;
                    $newPreview[] = $item;
                }

                // Jeda 20 detik antar request ke Gemini agar aman dari 429 Limit
                if ($chunks->count() > 1 && $index < ($chunks->count() - 1)) {
                    sleep(20);
                }
            }

            // 🔥 KALAU SUKSES SEMUA, SIMPAN PERMANEN
            DB::commit();

            // 🔥 UPDATE PREVIEW VIEW
            session([
                'preview_opini' => $newPreview,
                'saved_opini'   => true
            ]);

            return back()->with('success', 'Semua data berhasil dianalisis dan disimpan!');

        } catch (Exception $e) {
            // 💣 BATALKAN SEMUA INPUT JIKA AI ERROR / KONEKSI PUTUS
            DB::rollBack();
            
            Log::error("Gagal Proses Opini AI: " . $e->getMessage());

            // Balikin pesan error dari GeminiService ke Popup
            return back()->with('error', $e->getMessage());
        }
    }

    // =========================
    // 🔴 CLEAR
    // =========================
    public function clear()
    {
        session()->forget(['preview_opini', 'dataset_name', 'saved_opini']);
        return redirect()->route('opini')->with('success', 'Preview berhasil dihapus');
    }

    public function loadData(Request $request)
    {
        $data = Opini::with('motor')->where('dataset_name', $request->dataset_name)->get();
        return response()->json($data);
    }
}