<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Laundry</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            line-height: 1.4;
            background: #f7f7f7;
        }

        .receipt-container {
            padding: 10px;
            background: #fff;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-small {
            font-size: 11px;
        }

        .flex-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .divider {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .divider-double {
            border-top: 2px solid #000;
            margin: 8px 0;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .service-table th,
        .service-table td {
            padding: 3px 0;
            border-bottom: 1px solid #ccc;
        }

        .service-table th {
            text-align: left;
            font-weight: bold;
        }

        .service-table td:nth-child(4),
        .service-table td:nth-child(5) {
            text-align: right;
        }

        .service-table tr:nth-child(even) {
            background: #fafafa;
        }

        .receipt-footer {
            margin-top: 8px;
            text-align: center;
            font-size: 11px;
        }

        .receipt-footer div {
            margin: 2px 0;
        }

        .receipt-footer em {
            font-style: italic;
            color: #444;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                width: 80mm;
            }

            .receipt-container {
                box-shadow: none;
                padding: 8px;
            }

            .service-table th {
                background: #f0f0f0 !important;
            }
        }
    </style>
</head>

<body onload="printAndRedirect()">
    <div class="receipt-container">
        <!-- Header -->
        <h3 class="text-center font-bold">&#x1F9FA; Laundry Bersih & Wangi</h3>
        <div class="text-center font-small">&#x1F4CD; Jl. Karet Baru Benhill, Jakarta Pusat</div>
        <div class="text-center font-small">Telp: 0812-3456-7890</div>

        <div class="divider-double"></div>

        <!-- Info Transaksi -->
        <div class="font-small">
            <div class="flex-row">
                <span>&#x1F4CB; Kode Transaksi</span>
                <span class="font-bold">{{ $order->order_code ?? '-' }}</span>
            </div>
            <div class="flex-row">
                <span>&#x1F4C5; Tanggal Pemesanan</span>
                <span class="font-bold">
                    {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '-' }}
                </span>
            </div>
            <div class="flex-row">
                <span>&#x23F0; Estimasi Selesai</span>
                <span class="font-bold">
                    {{ $order->order_end_date ? \Carbon\Carbon::parse($order->order_end_date)->format('d/m/Y') : '-' }}
                </span>
            </div>
            <div class="flex-row">
                <span>&#x1F464; Nama Customer</span>
                <span class="font-bold">{{ $order->customer->customer_name ?? '-' }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Detail Layanan -->
        <table class="service-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Layanan</th>
                    <th>Berat</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->transOrderDetails ?? [] as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->typeOfService->service_name ?? '-' }}</td>
                        <td>{{ $detail->qty ?? 0 }} kg</td>
                        <td>Rp.{{ number_format($detail->typeOfService->price ?? 0, 0, ',', '.') }}</td>
                        <td>Rp.{{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada layanan yang dipilih</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Total -->
        <div class="flex-row font-bold">
            <span>&#x1F4B0; Total Tagihan</span>
            <span>Rp.{{ number_format($order->total ?? 0, 0, ',', '.') }}</span>
        </div>

        <div class="divider-double"></div>

        <!-- Footer -->
        <footer class="receipt-footer">
            <div>🙏 <b>Terima kasih</b> telah mempercayakan laundry Anda kepada kami</div>
            <div>&#x1F4DD; <b>Mohon tunjukkan struk ini saat pengambilan laundry</b></div>
            <div><em>&#x23F3; Laundry hanya dapat diambil sesuai jadwal yang tercantum</em></div>
            <div><em>Barang yang tidak diambil lebih dari 30 hari bukan menjadi tanggung jawab kami</em></div>
            <div class="font-small">
                Dicetak pada: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
            </div>
        </footer>
    </div>

    <script>
        function printAndRedirect() {
            window.print();
            setTimeout(() => {
                window.location.href = "{{ route('orders.index') }}";
            }, 500);
        }
    </script>
</body>

</html>
