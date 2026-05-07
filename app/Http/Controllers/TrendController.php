<?php

namespace App\Http\Controllers;

use App\Models\TrendAnalisis;
use App\Models\Motor;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Exception;

class TrendController extends Controller
{
   public function index()
{
    $trends = collect(); // Kosong, karena tidak ada filter awal
    $datasetTrend = TrendAnalisis::select('periode')->distinct()->orderBy('periode')->pluck('periode');

    return view('trend', compact('trends', 'datasetTrend'));
}

    // 1. CUMA GENERATE & LEMPAR KE SESSION (PREVIEW) - KAGA MASUK DB DULU!
    public function generate(Request $request)
{
    $request->validate([
        'periode' => 'required|string|max:255'
    ], [
        'periode.required' => 'Periode wajib diisi!'
    ]);

    $periode = $request->periode;

    $motors = Motor::all();
    $motorNames = $motors->pluck('nama')->toArray();

    try {
        // 🔥 Panggil AI di dalam blok Try-Catch
        $trendScores = GeminiService::getTrendScores($motorNames);

        // Jaga-jaga kalau request sukses, tapi AI malah ngerespons JSON kosong
        if (empty($trendScores)) {
            throw new Exception("AI berhasil dihubungi, tapi gagal membaca daftar motor. Silakan coba generate ulang!");
        }

        // Bikin array buat preview doang
        $previewData = [];
        foreach ($motors as $motor) {
            // Kalau nama motor nggak disebut sama AI, fallback ke 0.3
            $score = $trendScores[$motor->nama] ?? 0.3; 
            
            $previewData[] = [
                'motor_id'   => $motor->id,
                'nama_motor' => $motor->nama,
                'kategori'   => $motor->category,
                'skor_trend' => $score,
                'alasan_ai'  => $this->generateReason($motor->nama, $score)
            ];
        }

        // Simpen di Session (Preview mode ON)
        session([
            'preview_trend' => $previewData,
            'periode'       => $periode,
            'saved_trend'   => false
        ]);

        return redirect()->route('trend.index');

    } catch (Exception $e) {
        // 💣 TANGKAP BOM ERROR (502, 503, 429, dll) DI SINI!
        // Langsung balikin ke halaman index dengan membawa pesan error dari Service
        return redirect()->route('trend.index')->with('error', $e->getMessage());
    }
}

    // 2. FUNGSI SIMPAN KE DATABASE (TOMBOL YANG LU CARI-CARI)
    public function simpan()
    {
        $previewData = session('preview_trend');
        $periode = session('periode');

        if (!$previewData) {
            return redirect()->route('trend.index')->with('error', 'Data preview udah ilang ditelan bumi!');
        }

        foreach ($previewData as $item) {
            TrendAnalisis::updateOrCreate(
                [
                    'motor_id' => $item['motor_id'],
                    'periode'  => $periode
                ],
                [
                    'skor_trend' => $item['skor_trend'],
                    'alasan_ai'  => $item['alasan_ai']
                ]
            );
        }

        // Tandain udah disave
        session(['saved_trend' => true]);

        return redirect()->route('trend.index')->with('success', "Data Trend periode '$periode' sukses mendarat di database!");
    }

    // 3. FUNGSI CLEAR / RESET SESSION
   public function clear(Request $request)
{
    // 1. BANTAI semua session yang berkaitan sama preview trend AI
    $request->session()->forget([
        'preview_trend', 
        'saved_trend', 
        'periode'
    ]);

    // Opsi Ekstra: Kalau lu ngerasa sessionnya nge-bug parah, 
    // lu bisa flush session khusus yang ini (tapi forget aja udah cukup harusnya).

    // 2. Redirect ke halaman index POLOSAN. 
    // JANGAN PAKE return back() karena ntar GET parameternya (request('periode')) ngikut lagi!
    return redirect()->route('trend.index')->with('success', 'Data preview dan filter berhasil di-reset!');
}

    private function generateReason($nama, $score)
    {
        if ($score >= 0.8) return "🔥 Motor $nama lagi gila-gilaan diminati di Banten. Hype abis!";
        if ($score >= 0.4) return "✅ Tren $nama stabil. Cocok buat jualan santai.";
        return "📉 Tren mati. Orang Serang lagi nggak minat sama $nama.";
    }

    public function loadData(Request $request)
    {
        $periode = $request->query('periode');

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode wajib dipilih.'
            ], 422);
        }

        $trends = TrendAnalisis::with('motor')
            ->where('periode', $periode)
            ->latest()
            ->get();

        return response()->json($trends);
    }
}