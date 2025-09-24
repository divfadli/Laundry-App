<?php

namespace App\Exports;

use App\Models\TransOrders;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $orders;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? Carbon::now()->format('Y-m-d');
    }

    public function collection()
    {
        $this->orders = TransOrders::with('customer', 'transOrderDetails.typeOfService', 'transLaundryPickups')
            ->whereBetween('order_date', [$this->startDate, $this->endDate])
            ->get();
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Order',
            'Nama Customer',
            'Tanggal Order',
            'Estimasi Selesai',
            'Tanggal Pengambilan',
            'PPN 11% (Rp)',
            'Total (Rp)',
            'Status Order'
        ];
    }

    public function map($order): array
    {
        static $counter = 1;
        return [
            $counter++,
            $order->order_code,
            $order->customer->customer_name ?? '-',
            $order->order_date?->format('d/m/Y') ?? '-',
            $order->order_end_date?->format('d/m/Y') ?? '-',
            $order->transLaundryPickups?->pickup_date?->format('d/m/Y') ?? '-',
            $order->ppn,
            $order->total,
            $order->status_text
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $totalCompleted = $this->orders->filter(fn($o) => $o->order_status == 1)->sum('total');

                $totalRow = $lastRow + 2;
                $sheet->setCellValue("G{$totalRow}", "Total Pendapatan (Rp)");
                $sheet->setCellValue("H{$totalRow}", $totalCompleted);

                // Styling header
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                // Style total (bold, right align, border atas saja)
                $sheet->getStyle("G{$totalRow}:H{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'right'],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                $sheet->getStyle("G2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("D2:F{$lastRow}")->getAlignment()->setHorizontal('center');

                // Border & auto width data rows
                $sheet->getStyle("A1:I{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Coloring rows berdasarkan status
                foreach ($this->orders as $i => $order) {
                    $row = $i + 2;
                    if ($order->order_status == 1) {
                        $sheet->getStyle("A{$row}:I{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('C6EFCE');
                    } elseif ($order->order_status == 0) {
                        $sheet->getStyle("A{$row}:I{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('FFC7CE');
                    }
                }
            }
        ];
    }


}