<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AuditTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        return [
            [
                date('Y-m-d'),
                '17800T60',
                '000003',
                'GEMILANG',
                'TELLER 1780060',
                'Keterangan selisih kas teller sebesar Rp 100,000'
            ],
            [
                date('Y-m-d'),
                '17800CS63',
                '000004',
                'BUDI SANJAYA',
                'CUSTOMER SERVICE',
                'Kesalahan input data pembukaan rekening baru'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'tanggal_transaksi',
            'kode_user',
            'nomor_rekening',
            'nama_nasabah',
            'jenis_transaksi',
            'deskripsi'
        ];
    }

    public function title(): string
    {
        return 'Template Import Audit';
    }

    public function styles(Worksheet $sheet)
    {
        // Style for header row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F81BD'], // Blue header
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Wrap text for description
        $sheet->getStyle('F2:F3')->getAlignment()->setWrapText(true);

        // Style for borders on all data and headers
        $sheet->getStyle('A1:F3')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        
        // Auto filter
        $sheet->setAutoFilter('A1:F1');
    }
}
