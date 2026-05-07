<?php

namespace App\Exports;

use App\DTOs\PredictionResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Str; // 🔥 Wajib import ini
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PredictionExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithEvents,
    WithColumnWidths
{
    private int $totalTarget;
    private Collection $results;

    public function __construct(Collection $results, int $totalTarget)
    {
        $this->results     = $results;
        $this->totalTarget = $totalTarget;
    }

    public function collection(): Collection
    {
        return $this->results;
    }

    public function title(): string
    {
        return 'Laporan Prediksi Stok';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Motor',
            'Rekomendasi (Unit)',
            'Penjualan (%)',
            'Skor Akhir',
            'Sisa Stok',
            'Motor Baru',
            'Status', // 🔥 Header Baru
            'Analisis Sistem Pakar',
        ];
    }

    /**
     * @param PredictionResult $row
     */
    /**
     * @param PredictionResult $row
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        // 🔥 Ekstrak Status Berdasarkan Alasan
        $alasan = strtolower($row->alasan ?? '');
        
        if (Str::contains($alasan, ['darurat', 'overstock', 'loss sales'])) {
            $status = 'Darurat / Overstock';
        } elseif (Str::contains($alasan, ['premium', 'slow-moving kosong'])) {
            $status = 'Warning / Perhatian';
        } elseif (Str::contains($alasan, 'model baru')) {
            $status = 'Info / Model Baru';
        } else {
            $status = 'Aman / Normal';
        }

        return [
            $no,
            $row->namaMotor,
            // 🔥 SOLUSI: Pakai (string) atau strval() agar angka 0 tetap tercetak
            (string) $row->rekomendasiJumlah,
            $row->percent . '%',
            number_format($row->finalScore, 4),
            // 🔥 SOLUSI: Pakai (string) di sini juga untuk stok
            (string) $row->stokSisa,
            $row->isNew ? 'Ya' : '-',
            $status, 
            $row->alasan, 
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // Nama
            'C' => 18,  // Rekomendasi
            'D' => 15,  // % Penjualan
            'E' => 12,  // Skor
            'F' => 12,  // Stok Sisa
            'G' => 12,  // Motor Baru
            'H' => 22,  // Status (Lebar disesuaikan)
            'I' => 50,  // Analisis (Lebar Fix agar bisa wrap text)
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'], 
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $lastRow   = $this->results->count() + 1;
                $lastCol   = 'I'; // 🔥 Berubah jadi 'I' karena ada kolom Status

                // 1. Terapkan Border ke semua tabel
                $sheet->getStyle("A1:{$lastCol}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // 2. Alignment standar
                $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Wrap text untuk "Analisis"
                $sheet->getStyle("I2:I{$lastRow}")->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // 3. 🔥 PEWARNAAN DINAMIS ALA DASHBOARD WEB
                for ($row = 2; $row <= $lastRow; $row++) {
                    $statusValue = $sheet->getCell("H{$row}")->getValue();
                    
                    $styleStatus = [];
                    $styleTeks   = [];

                    // Penentuan Warna berdasarkan value Kolom H
                    if ($statusValue === 'Darurat / Overstock') {
                        // Merah
                        $styleStatus = [
                            'font' => ['color' => ['argb' => 'FFDC2626'], 'bold' => true],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFEF2F2']]
                        ];
                        $styleTeks = ['font' => ['color' => ['argb' => 'FFDC2626'], 'bold' => true]];
                    } elseif ($statusValue === 'Warning / Perhatian') {
                        // Kuning/Amber
                        $styleStatus = [
                            'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFBEB']]
                        ];
                        $styleTeks = ['font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true]];
                    } elseif ($statusValue === 'Info / Model Baru') {
                        // Biru
                        $styleStatus = [
                            'font' => ['color' => ['argb' => 'FF2563EB'], 'bold' => true],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFF6FF']]
                        ];
                        $styleTeks = ['font' => ['color' => ['argb' => 'FF2563EB'], 'bold' => true]];
                    } else {
                        // Abu-abu (Normal)
                        $styleStatus = ['font' => ['color' => ['argb' => 'FF4B5563'], 'bold' => true]];
                        $styleTeks   = ['font' => ['color' => ['argb' => 'FF4B5563']]];
                    }

                    // Terapkan warna ke cell Status (Kolom H) dan teks Analisis (Kolom I)
                    $sheet->getStyle("H{$row}")->applyFromArray($styleStatus);
                    $sheet->getStyle("I{$row}")->applyFromArray($styleTeks);
                }

                // Freeze header row
                $sheet->freezePane('A2');

                // ... (kode di atasnya biarkan sama)

                // Freeze header row
                $sheet->freezePane('A2');

                // ==========================================
                // 🔥 FIX: META INFO (Lebih rapi, anti kepotong, WIB)
                // ==========================================
                $metaRow = $lastRow + 3;
                
                // Judul
                $sheet->setCellValue("A{$metaRow}", 'INFORMASI PREDIKSI');
                $sheet->mergeCells("A{$metaRow}:C{$metaRow}"); // Merge A sampai C
                $sheet->getStyle("A{$metaRow}")->getFont()->setBold(true)->setSize(12);

                // Baris Target Unit
                $sheet->setCellValue("A" . ($metaRow + 1), 'Total Target Unit');
                $sheet->mergeCells("A" . ($metaRow + 1) . ":B" . ($metaRow + 1)); // Gabung A & B biar teks gak kepotong
                $sheet->setCellValue("C" . ($metaRow + 1), ': ' . $this->totalTarget . ' Unit');
                
                // Baris Tanggal Cetak (Fix Timezone Jakarta)
                $sheet->setCellValue("A" . ($metaRow + 2), 'Dicetak Pada');
                $sheet->mergeCells("A" . ($metaRow + 2) . ":B" . ($metaRow + 2)); // Gabung A & B
                $sheet->setCellValue("C" . ($metaRow + 2), ': ' . now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') . ' WIB');
                
                // Footer Tambahan
                $sheet->setCellValue("A" . ($metaRow + 3), 'Sistem Pendukung Keputusan (SAW & Sistem Pakar)');
                $sheet->mergeCells("A" . ($metaRow + 3) . ":E" . ($metaRow + 3)); // Merge panjang ke kanan

                // Styling text
                $sheet->getStyle("A" . ($metaRow + 1) . ":A" . ($metaRow + 2))->getFont()->setBold(true);
                $sheet->getStyle("A" . ($metaRow + 3))->getFont()->setItalic(true)->getColor()->setARGB('FF6B7280');
            },
        ];
    }
}