<?php

namespace App\Http\Controllers;

use App\Exports\PredictionExport;
use App\Http\Requests\PredictionRequest;
use App\Models\Motor;
use App\Services\GeminiService;
use App\Services\PredictionService;
use App\Models\TrendAnalisis; 
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class PredictionController extends Controller
{
    public function __construct(
        private readonly PredictionService $predictionService,
        private readonly GeminiService $geminiService,
    ) {}

    // =========================================================================
    // INDEX
    // =========================================================================
    public function index(): View
    {
        return view('prediction.index', array_merge(
            ['motors' => Motor::orderBy('nama')->get()],
            $this->getDatasets()
        ));
    }

    // =========================================================================
    // PREVIEW
    // =========================================================================
    public function preview(PredictionRequest $request): View
    {
        // 🔥 reset notif
        $request->session()->forget(['error', 'warning', 'success']);

        $data = $this->preparePrediction($request);

        return view('prediction.index', array_merge(
            $data,
            $this->getDatasets(),
            ['motors' => Motor::orderBy('nama')->get()]
        ));
    }

    // =========================================================================
    // EXPORT
    // =========================================================================
    public function export(PredictionRequest $request): BinaryFileResponse
    {
        $data = $this->preparePrediction($request);

        $filename = 'prediksi_stok_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new PredictionExport($data['results'], $data['totalTarget']),
            $filename
        );
    }

    // =========================================================================
    // CORE LOGIC (INI OTAKNYA)
    // =========================================================================
    // =========================================================================
    // CORE LOGIC (INI OTAKNYA)
    // =========================================================================
    private function preparePrediction(PredictionRequest $request): array
    {
        // 🔥 GEMBOK VALIDASI KETAT: TOLAK JIKA ADA KRITERIA (C) YANG KOSONG!
        $request->validate([
            'periode_penjualan' => 'required|string',
            'periode_stok'      => 'required|string',
            'periode_opini'     => 'required|string',
            'periode_trend'     => 'required|string',
        ], [
            'periode_penjualan.required' => '❌ Gagal! Dataset Penjualan (C1) wajib dipilih.',
            'periode_stok.required'      => '❌ Gagal! Dataset Stok (C2) wajib dipilih.',
            'periode_opini.required'     => '❌ Gagal! Dataset Opini/Sentimen (C3) wajib dipilih.',
            'periode_trend.required'     => '❌ Gagal! Dataset Trend Banten (C4) wajib dipilih.',
        ]);

        $totalTarget  = (int) $request->validated('total_target');
        $motorBaruIds = $request->validated('motor_baru', []);

        $periodePenjualan = $request->input('periode_penjualan');
        $periodeStok      = $request->input('periode_stok');
        $periodeOpini     = $request->input('periode_opini');
        $periodeTrend     = $request->input('periode_trend'); 

        // 🔥 1. Proses Algoritma SAW 
        $results = $this->predictionService->predict(
            $totalTarget,
            $motorBaruIds,
            $periodePenjualan,
            $periodeStok,
            $periodeOpini,
            $periodeTrend 
        );

        // 🔥 2. Terapkan Sistem Pakar (Rule-Based Heuristic) untuk Alasan
        $results = $this->applyReasoning($results);

        return [
            'results'          => $results,
            'totalTarget'      => $totalTarget,
            'motorBaruIds'     => $motorBaruIds,
            'periodePenjualan' => $periodePenjualan,
            'periodeStok'      => $periodeStok,
            'periodeOpini'     => $periodeOpini,
            'periodeTrend'     => $periodeTrend,
        ];
    }

    // =========================================================================
    // RULE-BASED HANDLER (MENGGANTIKAN FUNGSI AI)
    // =========================================================================
    private function applyReasoning($results)
    {
        $gemini = $this->geminiService;

        // Petakan hasil prediksi dengan alasan dari Sistem Pakar (Kasta Motor)
        $results = $results->map(function ($r) use ($gemini) {
            $r->alasan = $gemini->fallbackAlasan($r);
            return $r;
        });

        // Flash message akademis untuk menandakan arsitektur Hybrid berhasil jalan
        session()->now('success', 'Prediksi Hybrid.');

        return $results;
    }

    // =========================================================================
    // DATASET HELPER (ANTI DUPLIKAT QUERY)
    // =========================================================================
    private function getDatasets(): array
    {
        return [
            'penjualan' => DB::table('penjualan_analisis')
                ->whereNotNull('dataset_name')
                ->distinct()
                ->orderBy('dataset_name')
                ->pluck('dataset_name'),

            'stok' => DB::table('stok')
                ->whereNotNull('snapshot_name')
                ->distinct()
                ->orderBy('snapshot_name')
                ->pluck('snapshot_name'),

            'opini' => DB::table('opini')
                ->whereNotNull('dataset_name')
                ->distinct()
                ->orderBy('dataset_name')
                ->pluck('dataset_name'),

            // 🔥 INI QUERY BUAT NARIK DATA TREND BIAR BLADE LU KAGA ERROR 500!
            'trend' => DB::table('trend_analisis')
                ->whereNotNull('periode')
                ->distinct()
                ->orderBy('periode')
                ->pluck('periode'),
        ];
    }
    
}