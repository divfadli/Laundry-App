let serviceIndex = document.querySelectorAll('.service-item').length;

function calculateSubtotal(row) {
    const serviceSelect = row.querySelector('.service-select');
    const qtyInput = row.querySelector('.qty-input');
    const subtotalDisplay = row.querySelector('.subtotal-display');

    const price = parseFloat(serviceSelect.selectedOptions[0]?.dataset.price || 0);
    let qty = parseFloat(qtyInput.value) || 0;
    if (qty < 0.1) qty = 0.1;
    qtyInput.value = qty;

    const subtotal = price * qty;
    subtotalDisplay.value = 'Rp ' + subtotal.toLocaleString('id-ID');
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.service-item').forEach(row => {
        const serviceSelect = row.querySelector('.service-select');
        const qtyInput = row.querySelector('.qty-input');
        const price = parseFloat(serviceSelect.selectedOptions[0]?.dataset.price || 0);
        const qty = parseFloat(qtyInput.value) || 0;
        total += price * qty;
    });
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', () => {
    const addServiceBtn = document.getElementById('addService');
    const container = document.getElementById('serviceContainer');

    function toggleRemoveButtons() {
        const rows = container.querySelectorAll('.service-item');
        rows.forEach(row => {
            row.querySelector('.remove-service').style.display = rows.length > 1 ? 'block' : 'none';
        });
    }

    addServiceBtn.addEventListener('click', () => {
        const newRow = container.querySelector('.service-item').cloneNode(true);

        newRow.querySelectorAll('select, input').forEach(el => {
            if (el.name) el.name = el.name.replace(/\[\d+\]/, `[${serviceIndex}]`);
            if (el.classList.contains('qty-input')) el.value = 1;
            if (el.classList.contains('subtotal-display')) el.value = '';
            if (!el.classList.contains('qty-input') && !el.classList.contains('subtotal-display') && el.type !== 'button') el.value = '';

            // Set required
            if ((el.tagName === 'SELECT') || (el.tagName === 'INPUT' && el.type !== 'button' && !el.classList.contains('subtotal-display') && !el.name.includes('notes'))) {
                el.required = true;
            }
        });

        newRow.querySelector('.remove-service').style.display = 'block';
        container.appendChild(newRow);
        serviceIndex++;

        toggleRemoveButtons();
    });

    container.addEventListener('click', e => {
        if (e.target.classList.contains('remove-service') || e.target.closest('.remove-service')) {
            if (container.querySelectorAll('.service-item').length > 1) {
                e.target.closest('.service-item').remove();
                calculateGrandTotal();
                toggleRemoveButtons();
            }
        }
    });

    container.addEventListener('change', e => {
        if (e.target.classList.contains('service-select') || e.target.classList.contains('qty-input')) {
            calculateSubtotal(e.target.closest('.service-item'));
        }
    });

    container.addEventListener('input', e => {
        if (e.target.classList.contains('qty-input')) {
            calculateSubtotal(e.target.closest('.service-item'));
        }
    });

    // Hitung awal
    container.querySelectorAll('.service-item').forEach(row => calculateSubtotal(row));
    toggleRemoveButtons();
});
