    <script>
        // Isi Data Customer
        function fillCustomerData() {
            let select = document.getElementById("customerId");
            let option = select.options[select.selectedIndex];

            document.getElementById("customerPhone").value = option.getAttribute("data-phone") || "";
            document.getElementById("customerAddress").value = option.getAttribute("data-address") || "";
        }

        // let services = {
        //     !!$services!!
        // };
        let services = @json($services);

        let cart = [];
        let transactions = [];
        let transactionCounter = 0;

        function addService(serviceId, price) {
            document.getElementById('serviceType').value = serviceId;
            document.getElementById('serviceWeight').focus();
        }

        function removeFromCart(itemId) {
            // cart.splice(index, 1)
            cart = cart.filter(item => item.id !== itemId);
            updateCartDisplay();
        }

        function clearCart() {
            cart = [];
            updateCartDisplay();
            document.getElementById('transactionForm').reset();
        }

        async function processTransaction() {
            const customerId = document.getElementById('customerId').value;
            const customerPhone = document.getElementById('customerPhone').value;
            const customerAddress = document.getElementById('customerAddress').value;

            if (!customerId || !customerPhone || cart.length === 0) {
                alert('Mohon lengkapi data pelanggan dan pastikan ada item di keranjang!');
                return;
            }

            const total = cart.reduce((sum, item) => sum + item.subtotal, 0);

            const transaction = {
                id: `LDY-${formatDateYMD()}-${transactionCounter.toString().padStart(4, '0')}`,
                customer: {
                    id: customerId,
                    phone: customerPhone,
                    address: customerAddress
                },
                items: [...cart],
                total: total,
                order_date: new Date().toISOString(),
                order_status: 0
            };

            try {
                const res = await fetch("{{ route('orders.laundry_post') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            "content")
                    },
                    body: JSON.stringify(transaction)
                })

                if (!res.ok) {
                    throw new Error(`HTTP error!! Status: ${res.status}`)
                }

                const result = await res.json();
                alert("Transaksi berhasil disimpan!");

                loadDataTransactions();

                // Reset Cart
                clearCart();
                showReceipt(result.data);

            } catch (error) {
                console.error("Gagal Menyimpan Data Transaksi: ", error)
            }
        }

        function showReceipt(transaction) {
            const receiptHtml = `
                <div class="receipt">
                    <div class="receipt-header">
                        <h2>🧺 LAUNDRY RECEIPT</h2>
                        <p>ID: ${transaction.order_code}</p>
                        <p>Tanggal: ${new Date(transaction.order_date).toLocaleString('id-ID')}</p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <strong>Pelanggan:</strong><br>
                        ${transaction.customer.customer_name}<br>
                        ${formatPhoneNumberDynamic(transaction.customer.phone)}<br>
                        ${transaction.customer.address}
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <strong>Detail Pesanan:</strong><br>
                        ${transaction.trans_order_details.map(item => `
                            <div class="receipt-item">
                                <span>${item.type_of_service.service_name} (${item.qty}kg)</span>
                                <span>Rp ${parseFloat(item.subtotal ?? 0).toLocaleString('id-ID')}</span>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div class="receipt-total">
                        <div class="receipt-item">
                            <span>TOTAL:</span>
                            <span>Rp ${parseFloat(transaction.total ?? 0).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <p>Terima kasih atas kepercayaan Anda!</p>
                        <p>Barang akan siap dalam 1-3 hari kerja</p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <button class="btn btn-primary" onclick="printReceipt()">🖨️ Cetak Struk</button>
                    <button class="btn btn-success" onclick="closeModal()">✅ Selesai</button>
                </div>
            `;

            document.getElementById('modalContent').innerHTML = receiptHtml;
            document.getElementById('transactionModal').style.display = 'block';
        }

        function printReceipt() {
            const receiptContent = document.querySelector('.receipt').outerHTML;

            const printWindow = window.open('', '', 'width=400,height=600');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Cetak Struk</title>
                        <style>
                            @page { size: 80mm auto; margin: 0; }
                            body {
                                font-family: 'Courier New', monospace;
                                font-size: 13px;
                                color: #000;
                                margin: 0;
                                padding: 10px;
                            }

                            .receipt {
                                width: 100%;
                                max-width: 80mm;
                                margin: 0 auto;
                            }

                            .receipt-header {
                                text-align: center;
                                border-bottom: 1px dashed #000;
                                padding-bottom: 8px;
                                margin-bottom: 8px;
                            }
                            .receipt-header h2 {
                                margin: 0;
                                font-size: 16px;
                                font-weight: bold;
                            }
                            .receipt-header p {
                                margin: 2px 0;
                            }

                            .section-title {
                                font-weight: bold;
                                margin: 8px 0 4px;
                                text-decoration: underline;
                            }

                            .receipt-item {
                                display: flex;
                                justify-content: space-between;
                                margin: 2px 0;
                            }

                            .receipt-total {
                                border-top: 1px dashed #000;
                                margin-top: 8px;
                                padding-top: 8px;
                                font-weight: bold;
                                display: flex;
                                justify-content: space-between;
                            }

                            .footer {
                                text-align: center;
                                margin-top: 12px;
                                font-size: 12px;
                            }
                            .footer p {
                                margin: 2px 0;
                            }
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
                    <p>📞 ${formatPhoneNumberDynamic(transaction.customer.phone)}</p>
                    <p>🛍️ ${transaction.items.map(item => `${item.service} - ${item.weight}${item.service.includes('Sepatu') ? 'pasang' : item.service.includes('Karpet') ? 'm²' : 'kg'}`).join(', ')}</p>
                    <p>💰 Rp. ${parseFloat(transaction.total ?? 0).toLocaleString('id-ID')}</p>
                    <p>📅 ${new Date(transaction.date).toLocaleString('id-ID')}</p>
                    <span class="status-badge status-${transaction.status}">${getStatusText(transaction.status)}</span>
                </div>
            `).join('');

            historyContainer.innerHTML = html || '<p>Belum ada transaksi</p>';
        }

        function getStatusText(status) {
            const statusMap = {
                'baru': 'Process',
                'completed': 'Completed'
            };
            return statusMap[status] || status;
        }

        function updateStats() {
            const totalTransactions = transactions.length;
            const totalRevenue = transactions.reduce((sum, t) => sum + parseFloat(t.total), 0);
            const activeOrders = transactions.filter(t => t.status !== 'completed').length;
            const completedOrders = transactions.filter(t => t.status === 'completed').length;

            document.getElementById('totalTransactions').textContent = totalTransactions;
            document.getElementById('totalRevenue').textContent = `Rp. ${totalRevenue.toLocaleString('id-ID')}`;
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
                            <p>📞 ${formatPhoneNumberDynamic(transaction.customer.phone)}</p>
                            <p>🛍️ ${transaction.items.map(item => 
                                `${item.service} - ${item.weight}${item.service.includes('Sepatu') ? 'pasang' : item.service.includes('Karpet') ? 'm²' : 'kg'}`
                            ).join(', ')}</p>
                            <p>💰 Rp ${parseFloat(transaction.total ?? 0).toLocaleString('id-ID')}</p>
                            <p>📅 ${new Date(transaction.date).toLocaleString('id-ID')}</p>
                            <span class="status-badge status-${transaction.status}">${getStatusText(transaction.status)}</span>
                            <button 
                                class="btn btn-primary" 
                                onclick="updateTransactionStatus('${transaction.id}')" 
                                style="margin-top: 10px; padding: 5px 15px; font-size: 12px;"
                                ${transaction.status === 'completed' ? 'disabled style="background:gray; cursor:not-allowed;"' : ''}>
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
                            count: 0,
                            revenue: 0
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
                        <h3>Rp. ${monthlyRevenue.toLocaleString('id-ID')}</h3>
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
                                                                                        <td>Rp. ${stats.revenue.toLocaleString('id-ID')}</td>
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
                value: '0',
                text: 'Process'
            }, {
                value: '1',
                text: 'Completed'
            }];

            const statusHtml = `
                <h2>📝 Update Status Transaksi</h2>
                <h3>${transaction.id} - ${transaction.customer.name}</h3>
                <p>Status saat ini: <span class="status-badge status-${transaction.status}">${getStatusText(transaction.status)}</span></p>
                
                <div class="form-group">
                    <label>Pilih Status Baru:</label>
                    <select id="newStatus" style="width: 100%; padding: 10px; margin: 10px 0;">
                        ${statusOptions.map(option => `
                                                                    <option value="${option.value}" ${transaction.order_status === option.value ? 'selected' : ''}>
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
            let url = `{{ route('orders.pickupLaundry', ':id') }}`.replace(':id', transactionId);
            const newStatus = document.getElementById('newStatus').value;

            try {
                const res = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            "content")
                    },
                    body: JSON.stringify({
                        order_status: newStatus
                    })
                })

                if (!res.ok) {
                    throw new Error(`HTTP error!! Status: ${res.status}`)
                }

                const result = await res.json();
                alert("Status Transaksi Berhasil diupdate!!")

                window.location.replace("{{ route('orders.index') }}")

                // loadDataTransactions();

                // closeModal();
            } catch (error) {
                console.error("Gagal Menyimpan Data Transaksi: ", error)
            }
        }

        function closeModal() {
            document.getElementById('transactionModal').style.display = 'none';
        }


        function formatPhoneNumberDynamic(number) {
            return number.match(/.{1,3}/g).join("-");
        }

        function formatDateYMD(date = new Date()) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}${month}${day}`;
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
            loadDataTransactions();

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
            const serviceType = document.getElementById('serviceType').value;
            const weightValue = document.getElementById('serviceWeight').value;
            const weight = parseDecimal(weightValue);
            const notes = document.getElementById('notes').value;

            if (!serviceType || !weightValue || weight <= 0) {
                alert('Mohon lengkapi semua field yang diperlukan!');
                return;
            }

            const service = services.find(s => s.id == serviceType);
            if (!service) {
                alert('Layanan tidak ditemukan!');
                return;
            }
            const price = parseFloat(service.price) || 0;
            const subtotal = price * weight;

            const item = {
                id: Date.now(),
                id_service: service.id,
                service: service.service_name,
                weight: weight,
                price: price,
                subtotal: subtotal,
                notes: notes || null
            };

            cart.push(item);
            updateCartDisplay();

            // Clear form
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

                // Format weight to show decimal properly
                const formattedWeight = item.weight % 1 === 0 ?
                    item.weight.toString() :
                    item.weight.toFixed(1).replace('.', ',');

                html += `
                    <tr>
                        <td>${item.service}</td>
                        <td>${formattedWeight} ${unit}</td>
                        <td>Rp. ${item.price.toLocaleString('id-ID')}</td>
                        <td>Rp. ${item.subtotal.toLocaleString('id-ID')}</td>
                        <td>
                            <button class="btn btn-danger" onclick="removeFromCart(${item.id})" style="padding: 5px 10px; font-size: 12px;">
                                🗑️
                            </button>
                        </td>
                    </tr>
                `;
                total += item.subtotal;
            });

            cartItems.innerHTML = html;
            totalAmount.textContent = `Rp. ${total.toLocaleString('id-ID')}`;
        }

        // Add some sample data for demonstration
        async function loadDataTransactions() {
            try {
                const res = await fetch("{{ route('orders.getAllDataOrders') }}")

                if (!res.ok) {
                    throw new Error(`HTTP error!! Status:${res.status}`);
                }

                const result = await res.json();

                transactions = result;
                transactionCounter = transactions.length + 1
            } catch (error) {
                console.error("Gagal Memuat Data Transaksi: ", error)
            }
            updateTransactionHistory();
            updateStats();
        }
    </script>
