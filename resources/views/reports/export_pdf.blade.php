<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Laundry</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        td {
            text-align: left;
        }

        td.center {
            text-align: center;
        }

        td.right {
            text-align: right;
        }

        td.status-pending {
            background-color: #fff3cd;
            color: #856404;
            font-weight: bold;
            text-align: center;
        }

        td.status-completed {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
            text-align: center;
        }

        tfoot td {
            font-weight: bold;
            background-color: #e6e6e6;
        }
    </style>
</head>

<body>
    <h3>Laporan Penjualan Laundry</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Order</th>
                <th>Nama Customer</th>
                <th>Tanggal Order</th>
                <th>Estimasi Selesai</th>
                <th>Tanggal Pengambilan</th>
                <th>PPN 11% (Rp)</th>
                <th>Total (Rp)</th>
                <th>Status Order</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Hitung total hanya untuk order_status == 1 (selesai) dari data yang sudah diambil
                $grandTotal = $orders->filter(fn($order) => $order->order_status == 1)->sum('total');
            @endphp

            @foreach ($orders as $index => $order)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $order->order_code }}</td>
                    <td>{{ $order->customer->customer_name ?? '-' }}</td>
                    <td class="center">{{ $order->order_date->format('d/m/Y') }}</td>
                    <td class="center">{{ $order->order_end_date->format('d/m/Y') }}</td>
                    <td class="center">{{ optional($order->transLaundryPickups)->pickup_date?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="right">Rp {{ number_format($order->ppn, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="{{ $order->order_status == 1 ? 'status-completed' : 'status-pending' }}">
                        {{ $order->status_text }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="right">Total Pendapatan (Selesai):</td>
                <td class="right" colspan="2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
