<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Informasi Laundry - POS</title>
    @include('orders.sections.styles')
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🧺 Sistem Informasi Laundry</h1>
            <p class="subtitle">Point of Sales System - Kelola Transaksi Laundry dengan Mudah</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="totalTransactions">0</h3>
                <p>Total Transaksi</p>
            </div>
            <div class="stat-card">
                <h3 id="totalRevenue">Rp 0</h3>
                <p>Total Pendapatan</p>
            </div>
            <div class="stat-card">
                <h3 id="activeOrders">0</h3>
                <p>Pesanan Aktif</p>
            </div>
            <div class="stat-card">
                <h3 id="completedOrders">0</h3>
                <p>Pesanan Selesai</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Panel: New Transaction -->
            <div class="card">
                <h2>🛒 Transaksi Baru</h2>

                <form id="transactionForm">
                    {{-- Customer Info --}}
                    <div class="form-group">
                        <label for="customerId">Nama Pelanggan</label>
                        <select id="customerId" class="form-control" onchange="fillCustomerData()" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" data-phone="{{ $customer->phone }}"
                                    data-address="{{ $customer->address }}">
                                    {{ $customer->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="customerPhone">No. Telepon</label>
                            <input type="tel" id="customerPhone" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="customerAddress">Alamat</label>
                            <input type="text" id="customerAddress" readonly>
                        </div>
                    </div>

                    {{-- Services --}}
                    <div class="form-group">
                        <label>Pilih Layanan</label>
                        <div class="services-grid">
                            @foreach ($services as $service)
                                <button type="button" class="service-card"
                                    onclick="addService('{{ $service->id }}', {{ $service->price }})">
                                    <h3>{{ $service->service_name }}</h3>
                                    <div class="price">Rp. {{ number_format($service->price, 0, ',', '.') }}/kg</div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Weight & Type --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label for="serviceWeight">Berat/Jumlah</label>
                            <input type="number" id="serviceWeight" step="0.1" min="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="serviceType">Jenis Layanan</label>
                            <select id="serviceType" required>
                                <option value="">Pilih Layanan</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea id="notes" rows="3" placeholder="Catatan khusus untuk pesanan..."></textarea>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="addToCart()"
                        style="width: 100%; margin-bottom: 10px;">
                        ➕ Tambah ke Keranjang
                    </button>
                </form>

                <!-- Cart -->
                <div id="cartSection" style="display: none;">
                    <h3>📋 Keranjang Belanja</h3>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Layanan</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                        </tbody>
                    </table>

                    <div class="total-section">
                        <h3>Total Pembayaran</h3>
                        <div class="total-amount" id="totalAmount">Rp 0</div>
                        <button class="btn btn-success" onclick="processTransaction()"
                            style="width: 100%; margin-top: 15px;">
                            💳 Proses Transaksi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Transaction History -->
            <div class="card">
                <h2>📊 Riwayat Transaksi</h2>
                <div class="transaction-list" id="transactionHistory">
                    <div class="transaction-item">
                        <h4>TRX-001 - John Doe</h4>
                        <p>📞 0812-3456-7890</p>
                        <p>🛍️ Cuci Setrika - 2.5kg</p>
                        <p>💰 Rp 17.500</p>
                        <p>📅 13 Juli 2025, 14:30</p>
                        <span class="status-badge status-process">Proses</span>
                    </div>
                    <div class="transaction-item">
                        <h4>TRX-002 - Jane Smith</h4>
                        <p>📞 0813-7654-3210</p>
                        <p>🛍️ Cuci Kering - 3kg</p>
                        <p>💰 Rp 15.000</p>
                        <p>📅 13 Juli 2025, 13:15</p>
                        <span class="status-badge status-ready">Siap</span>
                    </div>
                </div>

                <button class="btn btn-warning" onclick="showAllTransactions()" style="width: 100%; margin-top: 15px;">
                    📋 Lihat Semua Transaksi
                </button>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="text-align: center; margin-top: 20px;">
            <button class="btn btn-primary" onclick="showReports()" style="margin: 0 10px;">
                📈 Laporan Penjualan
            </button>
            <button class="btn btn-warning" onclick="manageServices()" style="margin: 0 10px;">
                ⚙️ Kelola Layanan
            </button>
            <button class="btn btn-danger" onclick="clearCart()" style="margin: 0 10px;">
                🗑️ Bersihkan Keranjang
            </button>
        </div>
    </div>

    <!-- Modal for Transaction Details -->
    <div id="transactionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    @include('orders.sections.scripts')
