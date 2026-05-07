<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motor;
use App\Models\Stok;
use Maatwebsite\Excel\Facades\Excel;

class StokController extends Controller
{
    public function index()
    {
        $motors = Motor::with('stok')->get();
        $datasetStok = \App\Models\Stok::select('snapshot_name')->distinct()->orderBy('snapshot_name')->pluck('snapshot_name');

        return view('stok', compact('motors', 'datasetStok'));
    }

    // 🔥 RESET SEMUA STOK
    public function reset()
    {
        Stok::query()->update([
            'stok_sisa' => 0
        ]);

        session()->forget(['preview_stok', 'snapshot_name']);

        return back()->with('success', 'Semua stok berhasil direset');
    }

    // 🔥 UPDATE MANUAL
    public function update(Request $request)
    {
        if (!$request->stok) {
            return back()->with('error', 'Data stok kosong');
        }

        $snapshot = $request->snapshot_name ?? 'Manual Update';

        foreach ($request->stok as $motorId => $jumlah) {
            // 🔥 Tambahin snapshot_name di pencarian biar ga nimpa data lama
            Stok::updateOrCreate(
                [
                    'motor_id' => $motorId,
                    'snapshot_name' => $snapshot 
                ],
                ['stok_sisa' => (int) $jumlah]
            );
        }

        return back()->with('success', 'Stok berhasil diperbarui');
    }

    // 🔥 UPLOAD → PREVIEW
    public function upload(Request $request)
    {
        if (!$request->hasFile('file')) {
            return back()->with('error', 'File belum dipilih');
        }

        try {

            $data = Excel::toArray([], $request->file('file'));
            $rows = $data[0];

            $headerIndex = null;

            foreach ($rows as $i => $row) {
                $lowerRow = array_map(fn($h) => strtolower(trim($h)), $row);

                if (in_array('motor', $lowerRow) && in_array('jumlah', $lowerRow)) {
                    $headerIndex = $i;
                    break;
                }
            }

            if ($headerIndex === null) {
                return back()->with('error', 'Header tidak ditemukan');
            }

            $header = array_map(fn($h) => strtolower(trim($h)), $rows[$headerIndex]);

            $indexMotor = array_search('motor', $header);
            $indexJumlah = array_search('jumlah', $header);

            if ($indexMotor === false || $indexJumlah === false) {
                return back()->with('error', 'Format Excel salah');
            }

            $dataTemp = [];

            foreach ($rows as $i => $row) {

                if ($i <= $headerIndex) continue;

                $namaMotor = strtolower(trim($row[$indexMotor] ?? ''));
                
                // 🔥 FIX BUG: Cukup skip kalau nama motornya kosong.
                if (!$namaMotor) continue;

                // Ambil value, kalau dia kosong (blank cell) di Excel, jadikan angka 0
                $cellValue = $row[$indexJumlah] ?? 0;
                $jumlah = $cellValue === '' ? 0 : (int) $cellValue;

                $motor = Motor::whereRaw('LOWER(nama) = ?', [$namaMotor])->first();
                if (!$motor) continue;

                // 🔥 AGREGASI (BIAR GA DUPLIKAT)
                if (!isset($dataTemp[$motor->id])) {
                    $dataTemp[$motor->id] = [
                        'motor_id' => $motor->id,
                        'motor' => $motor->nama,
                        'jumlah' => 0
                    ];
                }

                $dataTemp[$motor->id]['jumlah'] += $jumlah;
            }

            $preview = array_values($dataTemp);

            if (empty($preview)) {
                return back()->with('error', 'Tidak ada data valid');
            }

            session([
                'preview_stok' => $preview,
                'snapshot_name' => $request->snapshot_name ?? 'Tanpa Nama'
            ]);

            return back()->with('success', 'Preview berhasil dimuat');

        } catch (\Exception $e) {
            return back()->with('error', 'File tidak valid');
        }
    }

    // 🔥 CLEAR PREVIEW
    public function clearPreview()
    {
        session()->forget(['preview_stok', 'snapshot_name']);

        return back()->with('success', 'Preview dihapus');
    }

    // 🔥 SIMPAN PREVIEW KE DB
    public function simpanPreview()
    {
        $data = session('preview_stok');
        $snapshot = session('snapshot_name');

        if (!$data) {
            return back()->with('error', 'Tidak ada data untuk disimpan');
        }

        foreach ($data as $item) {
            // 🔥 KUNCI: Cari yang motor_id DAN snapshot_name-nya sama
            $stok = Stok::firstOrNew([
                'motor_id' => $item['motor_id'],
                'snapshot_name' => $snapshot // Masukin ini biar dia bikin baris baru di DB
            ]);

            // 🔥 Kalo mau dia nambahin (7+7=14) tetep pake += 
            // 🔥 Kalo mau dia cuma set nilainya aja, hapus bagian ($stok->stok_sisa ?? 0)
            $stok->stok_sisa = $item['jumlah']; 
            $stok->save();
        }

        session()->forget(['preview_stok', 'snapshot_name']);

        return back()->with('success', 'Stok berhasil disimpan sebagai snapshot: ' . $snapshot);
    }

    public function loadData(Request $request)
{
    $data = Stok::with('motor')->where('snapshot_name', $request->snapshot_name)->get();
    return response()->json($data);
}
}