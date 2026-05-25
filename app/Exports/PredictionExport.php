<?php

namespace App\Exports;

use App\DTOs\PredictionResult;
use Illuminate\Support\Collection;
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
use Illuminate\Support\Facades\Auth;


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

    // =========================================================================
    // 🔥 MAP STATUS: dari properti PredictionResult
    // =========================================================================
    private const STATUS_LABEL_MAP = [
        'danger' => 'Darurat / Overstock',
        'review' => 'Warning / Perhatian',
        'info'   => 'Info / Model Baru',
        'safe'   => 'Aman / Normal',
    ];

    private const STATUS_STYLE_MAP = [
        'Darurat / Overstock' => ['fontColor' => 'FFDC2626', 'fillColor' => 'FFFEF2F2'],
        'Warning / Perhatian' => ['fontColor' => 'FFD97706', 'fillColor' => 'FFFFFBEB'],
        'Info / Model Baru'   => ['fontColor' => 'FF2563EB', 'fillColor' => 'FFEFF6FF'],
        'Aman / Normal'       => ['fontColor' => 'FF4B5563', 'fillColor' => null],
    ];

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
            'Status',
            'Analisis Sistem Pakar',
        ];
    }

    /**
     * @param PredictionResult $row
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        $statusKey   = $row->status ?? 'safe';
        $statusLabel = self::STATUS_LABEL_MAP[$statusKey] ?? 'Aman / Normal';

        // 🔥 PERBAIKAN: Rapihkan baris baru untuk C1, C2 dst agar terbaca rapi di Excel
        $alasan = $row->alasan ?? '';
        // Ubah tag HTML break/list jadi baris baru (\n)
        $alasan = str_ireplace(['<br>', '<br/>', '<br />', '<li>', '</li>'], "\n", $alasan);
        $alasan = strip_tags($alasan);
        
        // Beri enter paksa sebelum kata C1, C2, C3 dst kalau belum ada enternya
        $alasan = preg_replace('/(?<!\n)(C[1-5]\s*:)/i', "\n$1", $alasan);
        
        // Bersihkan spasi berlebih tanpa menghapus enter (\n)
        $alasanBersih = preg_replace('/[ \t]+/', ' ', $alasan); 
        $alasanBersih = preg_replace("/\n\s+/", "\n", $alasanBersih); 
        $alasanBersih = trim(preg_replace("/\n+/", "\n", $alasanBersih));

        return [
            $no,
            $row->namaMotor,
            (string) $row->rekomendasiJumlah,
            $row->percent . '%',
            number_format($row->finalScore, 4),
            (string) $row->stokSisa,
            $row->isNew ? 'Ya' : '-',
            $statusLabel,
            $alasanBersih,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 18,
            'D' => 15,
            'E' => 12,
            'F' => 12,
            'G' => 12,
            'H' => 22,
            'I' => 60, // Lebar sedikit ditambah agar tulisan analisis lega
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
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $this->results->count() + 1;

                // 1. Border ke seluruh tabel
                $sheet->getStyle("A1:I{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // 2. 🔥 ALIGNMENT (MIDDLE & CENTER)
                // Jadikan seluruh baris Vertical Center (Middle Align)
                $sheet->getStyle("A1:I{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Jadikan kolom A sampai H rata tengah (Horizontal Center)
                $sheet->getStyle("A2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Khusus kolom I (Analisis) Wrap Text dan Rata Kiri biar list C1, C2 rapih
                $sheet->getStyle("I2:I{$lastRow}")->getAlignment()
                    ->setWrapText(true)
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // 3. Pewarnaan Dinamis Status
                for ($row = 2; $row <= $lastRow; $row++) {
                    $statusValue = $sheet->getCell("H{$row}")->getValue();
                    $styleConfig = self::STATUS_STYLE_MAP[$statusValue] ?? null;

                    if ($styleConfig) {
                        $statusStyle = [
                            'font' => ['color' => ['argb' => $styleConfig['fontColor']], 'bold' => true],
                        ];
                        $textStyle = [
                            'font' => ['color' => ['argb' => $styleConfig['fontColor']]],
                        ];

                        if ($styleConfig['fillColor']) {
                            $statusStyle['fill'] = [
                                'fillType'   => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $styleConfig['fillColor']],
                            ];
                        }

                        $sheet->getStyle("H{$row}")->applyFromArray($statusStyle);
                        $sheet->getStyle("I{$row}")->applyFromArray($textStyle);
                    }
                }

                // 4. Freeze header row
                $sheet->freezePane('A2');

                // 5. 🔥 META INFO (Tambahan User Pembuat)
                $metaRow = $lastRow + 3;

                $sheet->setCellValue("A{$metaRow}", 'INFORMASI PREDIKSI');
                $sheet->mergeCells("A{$metaRow}:C{$metaRow}");
                $sheet->getStyle("A{$metaRow}")->getFont()->setBold(true)->setSize(12);

                $sheet->setCellValue("A" . ($metaRow + 2), 'Total Target Unit');
                $sheet->mergeCells("A" . ($metaRow + 2) . ":B" . ($metaRow + 2));
                $sheet->setCellValue("C" . ($metaRow + 2), ': ' . $this->totalTarget . ' Unit');

                $sheet->setCellValue("A" . ($metaRow + 3), 'Dicetak Pada');
                $sheet->mergeCells("A" . ($metaRow + 3) . ":B" . ($metaRow + 3));
                $sheet->setCellValue("C" . ($metaRow + 3), ': ' . now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') . ' WIB');

                $sheet->setCellValue("A" . ($metaRow + 1), 'Dicetak Oleh');
                $sheet->mergeCells("A" . ($metaRow + 1) . ":B" . ($metaRow + 1));
                $namaPencetak = Auth::check() ? Auth::user()->name : 'Sistem';                
                $sheet->setCellValue("C" . ($metaRow + 1), ': ' . $namaPencetak);

                $sheet->setCellValue("A" . ($metaRow + 4), 'Sistem Pendukung Keputusan (SAW & Sistem Pakar)');
                $sheet->mergeCells("A" . ($metaRow + 4) . ":E" . ($metaRow + 4));

                $sheet->getStyle("A" . ($metaRow + 1) . ":A" . ($metaRow + 3))->getFont()->setBold(true);
                $sheet->getStyle("A" . ($metaRow + 4))->getFont()->setItalic(true)->getColor()->setARGB('FF6B7280');
            },
        ];
    }
}