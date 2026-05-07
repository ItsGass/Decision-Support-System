<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PenjualanAnalisis;
use App\Models\Stok;
use App\Models\Opini;

class ClearDataController extends Controller
{
    public function clearSelected(Request $request)
{
    $selections = $request->input('selections', []);
    // selections = ['penjualan' => ['Dataset Jan', 'Dataset Feb'], 'opini' => ['Opini Q1'], ...]

    $map = [
        'penjualan_analisis' => [PenjualanAnalisis::class, 'dataset_name'],
        'penjualan'          => [Penjualan::class,         'dataset_name'],
        'opini'              => [Opini::class,             'dataset_name'],
        'stok'               => [Stok::class,              'snapshot_name'],
    ];

    foreach ($selections as $table => $names) {
        if (!isset($map[$table]) || empty($names)) continue;
        [$model, $column] = $map[$table];
        $model::whereIn($column, $names)->delete();
    }

    return back()->with('success', 'Data yang dipilih berhasil dihapus');
}

    public function getDatasetNames()
{
    return response()->json([
        'penjualan_analisis' => PenjualanAnalisis::distinct()->pluck('dataset_name'),
        'penjualan'          => Penjualan::distinct()->pluck('dataset_name'),
        'opini'              => Opini::distinct()->pluck('dataset_name'),
        'stok'               => Stok::distinct()->pluck('snapshot_name'),
    ]);
}
}