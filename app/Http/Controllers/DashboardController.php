<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Motor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilan Utama Dashboard
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Query dengan Filter
        $query = $this->applyFilters(Penjualan::query(), $request);

        // 2. Data Statistik Utama
        $totalPenjualan = (clone $query)->sum('jumlah');

        // 3. Data untuk Chart (Penjualan Harian)
        $chart = (clone $query)
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m-%d') as tanggal, SUM(jumlah) as total")
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // 4. Motor Paling Laris (Top Motor)
        $topMotor = (clone $query)
            ->selectRaw('motor_id, SUM(jumlah) as total')
            ->groupBy('motor_id')
            ->orderByDesc('total')
            ->with('motor')
            ->first();

        // 5. List Motor untuk Dropdown Filter
        $motors = Motor::orderBy('nama')->get();

        // 6. Logika Growth Rate (Dinamis: Bulan Terbaru vs Sebelumnya)
        $growthRate = $this->calculateGrowth(clone $query, $request);

        // 7. Stok Kritis (Mengambil data stok sisa terbaru dari tabel stok)
        $latestStokIds = DB::table('stok')
            ->select(DB::raw('MAX(id)'))
            ->groupBy('motor_id');

        $stokKritis = Motor::join('stok', 'motor.id', '=', 'stok.motor_id')
            ->whereIn('stok.id', $latestStokIds)
            ->select('motor.nama', 'stok.stok_sisa')
            ->where('stok.stok_sisa', '<', 5)
            ->orderBy('stok.stok_sisa', 'asc')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalPenjualan', 
            'chart', 
            'topMotor', 
            'motors', 
            'growthRate', 
            'stokKritis'
        ));
    }

    /**
     * API untuk Update Dashboard lewat AJAX (Filter)
     */
    public function dashboardData(Request $request)
    {
        $query = $this->applyFilters(Penjualan::query(), $request);

        // Update Chart
        $chart = (clone $query)
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m-%d') as tanggal, SUM(jumlah) as total")
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Update Total & Growth
        $total = (clone $query)->sum('jumlah');
        $growthRate = $this->calculateGrowth(clone $query, $request);

        // Update Top Motor
        $topMotor = (clone $query)
            ->selectRaw('motor_id, SUM(jumlah) as total')
            ->groupBy('motor_id')
            ->orderByDesc('total')
            ->with('motor')
            ->first();

        $selectedMotor = $request->motor_id ? Motor::find($request->motor_id)?->nama : null;

        return response()->json([
            'labels' => $chart->pluck('tanggal'),
            'data' => $chart->pluck('total'),
            'total' => number_format($total, 0, ',', '.'),
            'growth' => $growthRate, // Mengirim angka growth baru ke JS
            'top_motor' => $topMotor?->motor?->nama ?? '-',
            'selected_motor' => $selectedMotor
        ]);
    }

    /**
     * Helper: Menerapkan Filter Motor dan Tanggal
     */
    private function applyFilters($query, $request)
    {
        if ($request->motor_id) {
            $query->where('motor_id', $request->motor_id);
        }

        if ($request->start && $request->end) {
            $query->whereBetween('tanggal', [
                Carbon::parse($request->start)->format('Y-m-d'),
                Carbon::parse($request->end)->format('Y-m-d')
            ]);
        }

        return $query;
    }

    /**
     * Helper: Menghitung Growth Rate Dinamis
     */
    private function calculateGrowth($query, $request)
{
    // Jika ada Filter Tanggal
    if ($request->start && $request->end) {
        $startFilter = Carbon::parse($request->start)->format('Y-m-d');
        $endFilter = Carbon::parse($request->end)->format('Y-m-d');

        // Cari data paling PERTAMA yang tersedia di dalam rentang filter
        $firstEntry = (clone $query)
            ->whereBetween('tanggal', [$startFilter, $endFilter])
            ->orderBy('tanggal', 'asc')
            ->first();

        // Cari data paling TERAKHIR yang tersedia di dalam rentang filter
        $lastEntry = (clone $query)
            ->whereBetween('tanggal', [$startFilter, $endFilter])
            ->orderBy('tanggal', 'desc')
            ->first();

        // Jika tidak ada data sama sekali di rentang tersebut
        if (!$firstEntry || !$lastEntry) return 0;

        $firstPeriod = Carbon::parse($firstEntry->tanggal);
        $lastPeriod = Carbon::parse($lastEntry->tanggal);

        // Jika data cuma ada di 1 bulan yang sama (misal cuma ada data Januari saja)
        if ($firstPeriod->format('Y-m') == $lastPeriod->format('Y-m')) return 100;

        // Hitung total bulan pertama yang tersedia
        $startTotal = (clone $query)
            ->whereMonth('tanggal', $firstPeriod->month)
            ->whereYear('tanggal', $firstPeriod->year)
            ->sum('jumlah');

        // Hitung total bulan terakhir yang tersedia
        $endTotal = (clone $query)
            ->whereMonth('tanggal', $lastPeriod->month)
            ->whereYear('tanggal', $lastPeriod->year)
            ->sum('jumlah');

        if ($startTotal == 0) return 100;
        return round((($endTotal - $startTotal) / $startTotal) * 100, 1);
    }

    // LOGIKA DEFAULT (Jika tidak ada filter, bandingkan 2 bulan terbaru di DB)
    $periods = $query->selectRaw('MONTH(tanggal) as month, YEAR(tanggal) as year, SUM(jumlah) as total')
        ->groupBy('year', 'month')
        ->orderByDesc('year')
        ->orderByDesc('month')
        ->limit(2)
        ->get();

    if ($periods->count() < 1) return 0;
    if ($periods->count() == 1) return 100;

    $current = $periods->first()->total;
    $previous = $periods->last()->total;

    return ($previous == 0) ? 100 : round((($current - $previous) / $previous) * 100, 1);
}
}
