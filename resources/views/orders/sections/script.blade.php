<script>
    const servicesData = @json($services);

    // Isi Data Customer
    function fillCustomerData() {
        let select = document.getElementById("customerName");
        let option = select.options[select.selectedIndex];

        document.getElementById("customerPhone").value = option.getAttribute("data-phone") || "";
        document.getElementById("customerAddress").value = option.getAttribute("data-address") || "";
    }

    let cart = [];
    // let transactions = JSON.parse(localStorage.getItem('laundryTransactions')) || [];
    let transactions = @json($data) || [];
    let transactionCounter = transactions.length + 1;

    function addService(serviceName, price) {
        document.getElementById('serviceType').value = serviceName;
        document.getElementById('serviceWeight').focus();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartDisplay();
    }

    function clearCart() {
        cart = [];
        updateCartDisplay();
        document.getElementById('transactionForm').reset();
    }

    async function processTransaction() {
        const customerName = document.getElementById('customerName').value;
        const customerPhone = document.getElementById('customerPhone').value;
        const customerAddress = document.getElementById('customerAddress').value;

        if (!customerName || !customerPhone || cart.length === 0) {
            alert('Mohon lengkapi data pelanggan dan pastikan ada item di keranjang!');
            return;
        }

        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);

        const transaction = {
            id: `TRX-${transactionCounter.toString().padStart(3, '0')}`
            , customer: {
                id: customerName
                , phone: customerPhone
                , address: customerAddress
            }
            , items: [...cart]
            , total: total
            , order_date: new Date().toISOString()
            , order_status: 0
        };

        try {
            const res = await fetch("{{ route('orders.newstore') }}", {
                method: "POST"
                , headers: {
                    "Content-Type": "application/json"
                    , "Accept": "application/json"
                    , "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
                , body: JSON.stringify(transaction)
            })

            if (!res.ok) {
                throw new Error(`HTTP error! Status: ${res.status}`);
            }

            const result = await res.json();
            alert("Transaksi berhasil disimpan!");

            // Reset cart
            cart = [];
            updateCartDisplay();

            // 🔄 Ambil ulang data transaksi dari DB
            await loadTransactions();

            transactionCounter++;

            // Show receipt
            showReceipt(result.data);

            // Clear form and cart
            clearCart();
            await updateTransactionHistory();
            await updateStats();
        } catch (error) {
            console.error("Error saat menyimpan transaksi:" + error);
            alert("Terjadi kesalahan saat menyimpan transaksi.");
        }
    }

    async function loadTransactions() {
        try {
            const response = await fetch("{{ route('orders.loadTransaction') }}");
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (transactions.length === 0) {
                transactions = data;
                transactionCounter = transactions.length + 1;
            }


        } catch (error) {
            console.error("Gagal memuat transaksi:", error);
        }
    }

    function showReceipt(transaction) {
        const receiptHtml = `
        <div class="receipt" style="max-width: 400px; margin: auto; font-family: 'Arial', sans-serif; color: #333; line-height: 1.4;">
            <div class="receipt-header" style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 10px;">
                <h2 style="margin: 0; font-size: 20px;">🧺 LAUNDRY RECEIPT</h2>
                <p style="margin: 2px 0; font-size: 14px;">ID: ${transaction.order_code}</p>
                <p style="margin: 2px 0; font-size: 14px;">Tanggal: ${new Date(transaction.order_date).toLocaleString('id-ID')}</p>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Pelanggan:</strong><br>
                ${transaction.customer.customer_name}<br>
                ${transaction.customer.phone}<br>
                ${transaction.customer.address}
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Detail Pesanan:</strong><br>
                ${transaction.trans_order_details.map(item => `
                    <div class="receipt-item" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>${item.type_of_service.service_name} (${item.qty}kg)</span>
                        <span>Rp ${parseFloat(item.subtotal).toLocaleString('id-ID')}</span>
                    </div>
                `).join('')}
            </div>
            
            <div class="receipt-total" style="border-top: 2px solid #333; padding-top: 8px; font-weight: bold; display: flex; justify-content: space-between;">
                <span>TOTAL:</span>
                <span>Rp ${parseFloat(transaction.total ?? 0).toLocaleString('id-ID')}</span>
            </div>
            
            <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #555;">
                <p>Terima kasih atas kepercayaan Anda!</p>
                <p>Barang akan siap dalam 1-2 hari kerja</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <button class="btn btn-primary" onclick="printReceipt()" style="padding: 8px 16px; margin-right: 10px;">🖨️ Cetak Struk</button>
            <button class="btn btn-success" onclick="closeModal()" style="padding: 8px 16px;">✅ Selesai</button>
        </div>
    `;

        document.getElementById('modalContent').innerHTML = receiptHtml;
        document.getElementById('transactionModal').style.display = 'block';
    }

    function printReceipt() {
        const receiptContent = document.querySelector('.receipt').outerHTML;

        const printWindow = window.open('', '', 'width=600,height=800');
        printWindow.document.write(`
        <html>
            <head>
                <title>Cetak Struk</title>
                <style>
                    @page { size: 80mm auto; margin: 0; }
                    body { font-family: 'Arial', sans-serif; color: #333; padding: 20px; }
                    .receipt { max-width: 400px; margin: auto; }
                    .receipt-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 10px; }
                    .receipt-header h2 { margin: 0; font-size: 20px; }
                    .receipt-header p { margin: 2px 0; font-size: 14px; }
                    .receipt-item { display: flex; justify-content: space-between; margin-bottom: 5px; }
                    .receipt-total { border-top: 2px solid #333; padding-top: 8px; font-weight: bold; display: flex; justify-content: space-between; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #555; }
                </style>
            </head>
            <body>
                ${receiptContent}
            </body>
        </html>
    `);
        printWindow.document.close();
        printWindow.focus();

        printWindow.onafterprint = function() {
            printWindow.close();
            document.getElementById('transactionModal').style.display = 'block';
        };

        printWindow.print();
    }

    function updateTransactionHistory() {
        const historyContainer = document.getElementById('transactionHistory');
        const recentTransactions = transactions.slice(-5).reverse();

        const html = recentTransactions.map(transaction => `
<div class="transaction-item">
    <h4>${transaction.id} - ${transaction.customer.name}</h4>
    <p>📞 ${transaction.customer.phone}</p>
    <p>🛍️ ${transaction.items.map(item => `${item.service} - ${item.weight}${item.service.includes('Sepatu') ? 'pasang' : item.service.includes('Karpet') ? 'm²' : 'kg'}`).join(', ')}</p>
    <p>💰 Rp. ${parseFloat(transaction.total || 0).toLocaleString('id-ID')}</p>
    <p>📅 ${new Date(transaction.date).toLocaleString('id-ID')}</p>
    <span class="status-badge status-${transaction.status_badge}">${getStatusText(transaction.status)}</span>
</div>
`).join('');

        historyContainer.innerHTML = html || '<p>Belum ada transaksi</p>';
    }

    function getStatusText(status) {
        const statusMap = {
            '0': 'Proses'
            , '1': 'Selesai'
        };
        return statusMap[status] || status;
    }

    function updateStats() {
        const totalTransactions = transactions.length;
        const totalRevenue = transactions.reduce((sum, t) => sum + parseFloat(t.total), 0);
        const activeOrders = transactions.filter(t => t.status !== 'delivered').length;
        const completedOrders = transactions.filter(t => t.status === 'delivered').length;

        document.getElementById('totalTransactions').textContent = totalTransactions;
        document.getElementById('totalRevenue').textContent = `Rp. ${totalRevenue.toLocaleString()}`;
        document.getElementById('activeOrders').textContent = activeOrders;
        document.getElementById('completedOrders').textContent = completedOrders;
    }

    function showAllTransactions() {
        const allTransactionsHtml = `
        <h2>📋 Semua Transaksi</h2>
        <div style="max-height: 400px; overflow-y: auto;">
            ${transactions.map(transaction => `
            <div class="transaction-item">
                <h4>${transaction.id} - ${transaction.customer.name}</h4>
                <p>📞 ${transaction.customer.phone}</p>
                <p>🛍️ ${transaction.items.map(item => `${item.service} - ${item.weight}${item.service.includes('Sepatu') ? 'pasang' : item.service.includes('Karpet') ? 'm²' : 'kg'}`).join(', ')}</p>
                <p>💰 Rp. ${parseFloat(transaction.total || 0).toLocaleString('id-ID')}</p>
                <p>📅 ${new Date(transaction.date).toLocaleString('id-ID')}</p>
                <span class="status-badge status-${transaction.status_badge}">${getStatusText(transaction.status)}</span>
                <button class="btn btn-primary" onclick="updateTransactionStatus('${transaction.id}')" style="margin-top: 10px; padding: 5px 15px; font-size: 12px;">
                    📝 Update Status
                </button>
            </div>
            `).join('')}
        </div>
        `;

        document.getElementById('modalContent').innerHTML = allTransactionsHtml;
        document.getElementById('transactionModal').style.display = 'block';
    }

    function showReports() {
        const today = new Date();
        const thisMonth = today.getMonth();
        const thisYear = today.getFullYear();

        const monthlyTransactions = transactions.filter(t => {
            const tDate = new Date(t.date);
            return tDate.getMonth() === thisMonth && tDate.getFullYear() === thisYear;
        });

        const monthlyRevenue = monthlyTransactions.reduce((sum, t) => sum + parseFloat(t.total), 0);

        const serviceStats = {};
        transactions.forEach(t => {
            t.items.forEach(item => {
                if (!serviceStats[item.service]) {
                    serviceStats[item.service] = {
                        count: 0
                        , revenue: 0
                    };
                }
                serviceStats[item.service].count++;
                serviceStats[item.service].revenue += parseFloat(item.subtotal);
            });
        });

        const reportsHtml = `
        <h2>📈 Laporan Penjualan</h2>

        <div class="stats-grid" style="margin-bottom: 20px;">
            <div class="stat-card">
                <h3>${transactions.length}</h3>
                <p>Total Transaksi</p>
            </div>
            <div class="stat-card">
                <h3>${monthlyTransactions.length}</h3>
                <p>Transaksi Bulan Ini</p>
            </div>
            <div class="stat-card">
                <h3>Rp ${monthlyRevenue.toLocaleString()}</h3>
                <p>Pendapatan Bulan Ini</p>
            </div>
        </div>

        <h3>📊 Statistik Layanan</h3>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Jumlah Order</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                ${Object.entries(serviceStats).map(([service, stats]) => `
                <tr>
                    <td>${service}</td>
                    <td>${stats.count}</td>
                    <td>Rp ${stats.revenue.toLocaleString()}</td>
                </tr>
                `).join('')}
            </tbody>
        </table>
        `;

        document.getElementById('modalContent').innerHTML = reportsHtml;
        document.getElementById('transactionModal').style.display = 'block';
    }

    function manageServices() {
        const servicesHtml = `
        <h2>⚙️ Kelola Layanan</h2>
        <p>Fitur ini memungkinkan Anda mengelola jenis layanan dan harga.</p>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Harga</th>
                    <th>Satuan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Cuci Kering</td>
                    <td>Rp 5.000</td>
                    <td>per kg</td>
                    <td><span class="status-badge status-ready">Aktif</span></td>
                </tr>
                <tr>
                    <td>Cuci Setrika</td>
                    <td>Rp 7.000</td>
                    <td>per kg</td>
                    <td><span class="status-badge status-ready">Aktif</span></td>
                </tr>
                <tr>
                    <td>Setrika Saja</td>
                    <td>Rp 3.000</td>
                    <td>per kg</td>
                    <td><span class="status-badge status-ready">Aktif</span></td>
                </tr>
                <tr>
                    <td>Dry Clean</td>
                    <td>Rp 15.000</td>
                    <td>per kg</td>
                    <td><span class="status-badge status-ready">Aktif</span></td>
                </tr>
                <tr>
                    <td>Cuci Sepatu</td>
                    <td>Rp 25.000</td>
                    <td>per pasang</td>
                    <td><span class="status-badge status-ready">Aktif</span></td>
                </tr>
                <tr>
                    <td>Cuci Karpet</td>
                    <td>Rp 20.000</td>
                    <td>per m²</td>
                    <td><span class="status-badge status-ready">Aktif</span></td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 20px;">
            <button class="btn btn-primary" onclick="alert('Fitur akan segera tersedia!')">
                ➕ Tambah Layanan Baru
            </button>
        </div>
        `;

        document.getElementById('modalContent').innerHTML = servicesHtml;
        document.getElementById('transactionModal').style.display = 'block';
    }

    function updateTransactionStatus(transactionId) {
        const transaction = transactions.find(t => t.id === transactionId);
        if (!transaction) return;

        const statusOptions = [{
                value: 0
                , text: 'Sedang Proses'
            }
            , {
                value: 1
                , text: 'Selesai'
            }
        ];

        const statusHtml = `
<h2>📝 Update Status Transaksi</h2>
<h3>${transaction.id} - ${transaction.customer.name}</h3>
<p>Status saat ini: <span class="status-badge status-${transaction.status}">${getStatusText(transaction.status)}</span></p>

<div class="form-group">
    <label>Pilih Status Baru:</label>
    <select id="newStatus" style="width: 100%; padding: 10px; margin: 10px 0;">
        ${statusOptions.map(option => `
        <option value="${option.value}" ${transaction.status===option.value ? 'selected' : '' }>
            ${option.text}
        </option>
        `).join('')}
    </select>
</div>

<div style="text-align: center; margin-top: 20px;">
    <button class="btn btn-success" onclick="saveStatusUpdate('${transactionId}')">
        ✅ Simpan Update
    </button>
    <button class="btn btn-danger" onclick="closeModal()" style="margin-left: 10px;">
        ❌ Batal
    </button>
</div>
`;

        document.getElementById('modalContent').innerHTML = statusHtml;
        document.getElementById('transactionModal').style.display = 'block';
    }

    async function saveStatusUpdate(transactionId) {
        const newStatus = parseInt(document.getElementById('newStatus').value);

        // Find the transaction object
        const transaction = transactions.find(t => t.id === transactionId);
        if (!transaction) return;

        // Only pay if status is completed
        const orderPay = newStatus === 1 ? transaction.total : 0;

        // Generate the Laravel route dynamically
        const baseUrl = "{{ route('orders.pickup_laundry', ':id') }}"; // placeholder :id
        const url = baseUrl.replace(':id', transaction.db_id ? ? transaction.id); // use DB ID if available

        try {
            const res = await fetch(url, {
                method: 'POST'
                , headers: {
                    'Content-Type': 'application/json'
                    , 'Accept': 'application/json'
                    , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
                , body: JSON.stringify({
                    order_pay: orderPay
                    , notes: '' // optional, can be replaced with an input value
                })
            });

            if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);

            const result = await res.json();

            alert(result.message || 'Status berhasil diupdate!');

            // Refresh transactions from backend
            await loadTransactions();
            updateTransactionHistory();
            updateStats();
            closeModal();

        } catch (error) {
            console.error('Gagal mengupdate status:', error);
            alert('Gagal mengupdate status transaksi.');
        }
    }

    function closeModal() {
        document.getElementById('transactionModal').style.display = 'none';
    }

    function formatNumber(input) {
        // Replace comma with dot for decimal separator
        let value = input.value.replace(',', '.');

        // Ensure only valid decimal number
        if (!/^\d*\.?\d*$/.test(value)) {
            value = value.slice(0, -1);
        }

        // Update input value
        input.value = value;
    }

    function parseDecimal(value) {
        // Handle both comma and dot as decimal separator
        return parseFloat(value.toString().replace(',', '.')) || 0;
    }

    // Initialize the application
    document.addEventListener('DOMContentLoaded', function() {
        loadTransactions();
        updateTransactionHistory();
        updateStats();

        // Add event listener for weight input to handle decimal with comma
        const weightInput = document.getElementById('serviceWeight');
        weightInput.addEventListener('input', function() {
            formatNumber(this);
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('transactionModal');
            if (event.target === modal) {
                closeModal();
            }
        };
    });

    // Update addToCart function to handle decimal with comma
    function addToCart() {
        const serviceType = document.getElementById('serviceType').value.trim();
        const weightValue = document.getElementById('serviceWeight').value;
        const weight = parseDecimal(weightValue);
        const notes = document.getElementById('notes').value.trim();

        if (!serviceType || !weightValue || isNaN(weight) || weight <= 0) {
            alert('Mohon lengkapi semua field yang diperlukan!');
            return;
        }

        // Cari service berdasarkan pilihan
        const service = servicesData.find(s => s.service_name === serviceType);
        if (!service) {
            alert('Layanan tidak ditemukan!');
            return;
        }

        const price = parseFloat(service.price) || 0;
        const subtotal = price * weight;

        const item = {
            id_service: service.id
            , service: serviceType
            , qty: weight
            , price: price
            , subtotal: subtotal
            , notes: notes || null
        };

        cart.push(item);
        updateCartDisplay();
        console.log("Cart updated:", cart);

        // Reset form
        document.getElementById('serviceType').value = '';
        document.getElementById('serviceWeight').value = '';
        document.getElementById('notes').value = '';
    }

    // Update cart display to show decimal properly
    function updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        const cartSection = document.getElementById('cartSection');
        const totalAmount = document.getElementById('totalAmount');

        if (cart.length === 0) {
            cartSection.style.display = 'none';
            return;
        }

        cartSection.style.display = 'block';

        let html = '';
        let total = 0;

        cart.forEach((item, index) => {
            const unit = item.service.includes('Sepatu') ? 'pasang' :
                item.service.includes('Karpet') ? 'm²' : 'kg';

            const formattedQty = item.qty % 1 === 0 ?
                item.qty.toString() :
                item.qty.toFixed(1).replace('.', ',');

            html += `
    <tr>
        <td>${item.service}</td>
        <td>${formattedQty} ${unit}</td>
        <td>Rp ${item.price.toLocaleString()}</td>
        <td>Rp ${item.subtotal.toLocaleString()}</td>
        <td>
            <button class="btn btn-danger" onclick="removeFromCart(${index})" style="padding: 5px 10px; font-size: 12px;">
                🗑️
            </button>
        </td>
    </tr>
    `;
            total += item.subtotal;
        });

        cartItems.innerHTML = html;
        totalAmount.textContent = `Rp ${total.toLocaleString()}`;
    }

    // Add some sample data for demonstration
    function addSampleData() {
        const sampleTransactions = [{
                id: 'TRX-001'
                , customer: {
                    name: 'John Doe'
                    , phone: '0812-3456-7890'
                    , address: 'Jl. Merdeka 123'
                }
                , items: [{
                    service: 'Cuci Setrika'
                    , weight: 2.5
                    , price: 7000
                    , subtotal: 17500
                }]
                , total: 17500
                , date: new Date().toISOString()
                , status: 'process'
            }
            , {
                id: 'TRX-002'
                , customer: {
                    name: 'Jane Smith'
                    , phone: '0813-7654-3210'
                    , address: 'Jl. Sudirman 456'
                }
                , items: [{
                    service: 'Cuci Kering'
                    , weight: 3
                    , price: 5000
                    , subtotal: 15000
                }]
                , total: 15000
                , date: new Date(Date.now() - 3600000).toISOString()
                , status: 'ready'
            }
        ];

        if (transactions.length === 0) {
            transactions = sampleTransactions;
            localStorage.setItem('laundryTransactions', JSON.stringify(transactions));
            transactionCounter = transactions.length + 1;
        }
    }

    // Initialize with sample data
    // addSampleData();

</script>
