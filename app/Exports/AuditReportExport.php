<?php

namespace App\Exports;

use App\Models\AuditTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AuditReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $transactions;
    private $rowNumber = 0;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Transaksi',
            'ID User',
            'Kode User',
            'Nomor Rekening',
            'Nama Nasabah',
            'Jenis Transaksi',
            'Deskripsi',
            'Status',
            'Dibuat Oleh',
        ];
    }

    public function map($transaction): array
    {
        return [
            ++$this->rowNumber,
            $transaction->transaction_date,
            $transaction->user_id,
            $transaction->user_code,
            $transaction->account_number,
            $transaction->customer_name,
            $transaction->transaction_type,
            $transaction->description,
            $transaction->status,
            $transaction->creator ? $transaction->creator->name : 'System',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->transactions->count() + 1;

        // Style for header row
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F81BD'], // A nice blue header
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style for borders on all data and headers
        $sheet->getStyle('A1:J' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
    }
}
