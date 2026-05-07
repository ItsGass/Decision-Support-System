<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Motor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::query();

        // 🔽 FILTER MOTOR
        if ($request->motor_id) {
            $query->where('motor_id', $request->motor_id);
        }

        // 🔽 FILTER TANGGAL (FIX FORMAT)
        if ($request->start && $request->end) {
            $start = Carbon::parse($request->start)->format('Y-m-d');
            $end = Carbon::parse($request->end)->format('Y-m-d');

            $query->whereBetween('tanggal', [$start, $end]);
        }

        $totalPenjualan = (clone $query)->sum('jumlah');

        // 🔥 FIX FORMAT TANGGAL UNTUK CHART
        $chart = (clone $query)
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m-%d') as tanggal, SUM(jumlah) as total")
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $topMotor = (clone $query)
            ->selectRaw('motor_id, SUM(jumlah) as total')
            ->groupBy('motor_id')
            ->orderByDesc('total')
            ->with('motor')
            ->first();

        $motors = Motor::orderBy('nama')->get();

        return view('dashboard', compact(
            'totalPenjualan',
            'chart',
            'topMotor',
            'motors'
        ));
    }

    // 🔥 AJAX CHART
    public function dashboardData(Request $request)
{
    $query = Penjualan::query();

    // FILTER MOTOR
    if ($request->motor_id) {
        $query->where('motor_id', $request->motor_id);
    }

    // FILTER TANGGAL
    if ($request->start && $request->end) {
        $query->whereBetween('tanggal', [$request->start, $request->end]);
    }

    // CHART
    $chart = (clone $query)
        ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m-%d') as tanggal, SUM(jumlah) as total")
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->get();

    // TOTAL
    $total = (clone $query)->sum('jumlah');

    // TOP MOTOR
    $topMotor = (clone $query)
        ->selectRaw('motor_id, SUM(jumlah) as total')
        ->groupBy('motor_id')
        ->orderByDesc('total')
        ->with('motor')
        ->first();

    // SELECTED MOTOR
    $selectedMotor = null;
    if ($request->motor_id) {
        $selectedMotor = Motor::find($request->motor_id)?->nama;
    }

    return response()->json([
        'labels' => $chart->pluck('tanggal'),
        'data' => $chart->pluck('total'),
        'total' => $total,
        'top_motor' => $topMotor?->motor?->nama ?? '-',
        'selected_motor' => $selectedMotor
    ]);
}
}