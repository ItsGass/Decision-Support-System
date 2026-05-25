<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motor;
use App\Models\Penjualan;
use App\Models\PenjualanAnalisis;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{

public function index()
{
    $datasetPenjualan = Penjualan::select('dataset_name')->distinct()->orderBy('dataset_name')->pluck('dataset_name');
    $datasetAnalisis  = PenjualanAnalisis::select('dataset_name')->distinct()->orderBy('dataset_name')->pluck('dataset_name');

    return view('penjualan', compact('datasetPenjualan', 'datasetAnalisis'));
}
    // =========================
    // 🔵 UPLOAD → PREVIEW (FIXED: RAW, TANPA GROUP)
    // =========================
    public function upload(Request $request)
    {
        if (!$request->hasFile('file')) {
            return back()->with('error', 'File belum dipilih');
        }

        try {
            $data = Excel::toArray([], $request->file('file'));
            $rows = $data[0];

            // 🔥 DETECT HEADER
            $headerIndex = null;
            foreach ($rows as $i => $row) {
                $lower = array_map(fn($h) => strtolower(trim((string)$h)), $row);

                if (in_array('tanggal', $lower) &&
                    in_array('motor', $lower) &&
                    in_array('jumlah', $lower)) {
                    $headerIndex = $i;
                    break;
                }
            }

            if ($headerIndex === null) {
                return back()->with('error', 'Header harus: tanggal | motor | jumlah');
            }

            $header = array_map(fn($h) => strtolower(trim((string)$h)), $rows[$headerIndex]);

            $iTgl = array_search('tanggal', $header);
            $iMot = array_search('motor', $header);
            $iJml = array_search('jumlah', $header);

            // =========================
            // 🔥 FIX: TIDAK ADA GROUPING
            // =========================
            $preview = [];

            foreach ($rows as $i => $row) {

                if ($i <= $headerIndex) continue;

                $rawTanggal = $row[$iTgl] ?? null;
                $namaMotor  = strtolower(trim((string)($row[$iMot] ?? '')));
                $jumlah     = (int) ($row[$iJml] ?? 0);

                if (!$namaMotor || $jumlah == 0) continue;

                // format tanggal
                if (is_numeric($rawTanggal)) {
                    $tanggal = Date::excelToDateTimeObject($rawTanggal)->format('Y-m-d');
                } else {
                    $tanggal = date('Y-m-d', strtotime($rawTanggal));
                }

                $motor = Motor::whereRaw('LOWER(nama) = ?', [$namaMotor])->first();
                if (!$motor) continue;

                // 🔥 SIMPAN RAW
                $preview[] = [
                    'motor_id' => $motor->id,
                    'motor'    => $motor->nama,
                    'tanggal'  => $tanggal,
                    'jumlah'   => $jumlah
                ];
            }
            // 🔥 HITUNG TOTAL
                    $total = array_sum(array_column($preview, 'jumlah'));

                    // 🔥 TAMBAH PERCENT
                    foreach ($preview as &$item) {
                        $item['percent'] = $total > 0
                            ? round(($item['jumlah'] / $total) * 100, 2)
                            : 0;
                    }
                    unset($item);

                    // 🔥 SORT BIAR RAPI (OPTIONAL)
                    usort($preview, fn($a, $b) => $b['jumlah'] <=> $a['jumlah']);
                                if (empty($preview)) {
                                    return back()->with('error', 'Tidak ada data valid');
            }

            session([
                'preview_penjualan' => $preview,
                'dataset_name' => $request->dataset_name ?? 'Tanpa Nama'
            ]);
            session()->forget('preview_grouped');
            return back()->with('success', 'Preview berhasil ditampilkan');

        } catch (\Exception $e) {
            return back()->with('error', 'File error / format salah');
        }
    }

    public function groupPreview()
{
    $data = session('preview_penjualan');

    if (!$data) {
        return back()->with('error', 'Tidak ada data untuk di-group');
    }

    $group = [];

    // 🔥 GROUP BY MOTOR
    foreach ($data as $row) {

        if (!isset($group[$row['motor_id']])) {
            $group[$row['motor_id']] = [
                'motor_id' => $row['motor_id'],
                'motor' => $row['motor'],
                'jumlah' => 0
            ];
        }

        $group[$row['motor_id']]['jumlah'] += $row['jumlah'];
    }

    // 🔥 HITUNG TOTAL
    $total = array_sum(array_column($group, 'jumlah'));

    $result = [];

    foreach ($group as $item) {

        $percent = $total > 0
            ? ($item['jumlah'] / $total) * 100
            : 0;

        $result[] = [
            'motor_id' => $item['motor_id'],
            'motor' => $item['motor'],
            'jumlah' => $item['jumlah'],
            'percent' => round($percent, 2)
        ];
    }

    // 🔥 SORT
    usort($result, fn($a, $b) => $b['percent'] <=> $a['percent']);

    session(['preview_grouped' => $result]);

    return back()->with('success', 'Data berhasil di-group');
}

    // =========================
    // 🟩 SIMPAN RAW (TIDAK DIUBAH)
    // =========================
    public function simpanRaw()
    {
        $data = session('preview_penjualan');
        $dataset = session('dataset_name');

        if (!$data) {
            return back()->with('error', 'Tidak ada data preview');
        }

        foreach ($data as $row) {
            Penjualan::create([
                'motor_id' => $row['motor_id'],
                'tanggal'  => $row['tanggal'],
                'jumlah'   => $row['jumlah'],
                'dataset_name' => $dataset,
                'user_id' => Auth::id()
            ]);
        }

        session()->forget(['preview_penjualan','dataset_name']);

        return back()->with('success', 'Data RAW berhasil disimpan');
    }

    // =========================
    // 🟣 SIMPAN ANALISIS (TIDAK DIUBAH)
    // =========================
    public function simpanAnalisis()
    {
        $data = session('preview_grouped') ?? session('preview_penjualan');
        $dataset = session('dataset_name');

        if (!$data) {
            return back()->with('error', 'Tidak ada data preview');
        }

        foreach ($data as $row) {
            PenjualanAnalisis::create([
                'motor_id' => $row['motor_id'],
                'jumlah'   => $row['jumlah'],
                'percent'  => $row['percent'] ?? 0,
                'dataset_name' => $dataset,
                'user_id' => Auth::id()
            ]);
        }

        session()->forget(['preview_penjualan','dataset_name']);

        return back()->with('success', 'Data analisis berhasil disimpan');
    }

    // =========================
    // 🔴 CLEAR PREVIEW (TIDAK DIUBAH)
    // =========================
    public function clear()
    {
        session()->forget(['preview_penjualan','dataset_name']);
        return back()->with('success', 'Preview dibersihkan');
    }

    public function tersimpan()
{
    $datasetPenjualan  = Penjualan::select('dataset_name')->distinct()->orderBy('dataset_name')->pluck('dataset_name');
    $datasetAnalisis   = PenjualanAnalisis::select('dataset_name')->distinct()->orderBy('dataset_name')->pluck('dataset_name');

    return view('penjualan.tersimpan', compact('datasetPenjualan', 'datasetAnalisis'));
}

public function loadData(Request $request)
{
    $tipe    = $request->tipe; // 'penjualan' atau 'analisis'
    $dataset = $request->dataset_name;

    if ($tipe === 'analisis') {
    $data = PenjualanAnalisis::with('motor')->where('dataset_name', $dataset)->get();
} else {
    $data = Penjualan::with('motor')->where('dataset_name', $dataset)->get();
}

    return response()->json($data);
}


}