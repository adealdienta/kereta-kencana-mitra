/**
 * Order Calculator & Validator: PR. KERETA KENCANA
 * Kalkulasi otomatis kuantitas bal, subtotal harga, dan validasi minimum order B2B
 */

document.addEventListener('DOMContentLoaded', function () {
  const qtyInputs = document.querySelectorAll('.product-qty-input');
  const summaryTotalBal = document.getElementById('summaryTotalBal');
  const summaryTotalNilai = document.getElementById('summaryTotalNilai');
  const summaryItemList = document.getElementById('summaryItemList');
  const orderForm = document.getElementById('b2bOrderForm');

  function formatRupiah(num) {
    return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function recalculateOrder() {
    let grandTotalBal = 0;
    let grandTotalPrice = 0;
    let activeItems = [];

    qtyInputs.forEach(input => {
      const qty = parseInt(input.value) || 0;
      const price = parseFloat(input.getAttribute('data-price')) || 0;
      const name = input.getAttribute('data-name');
      const minOrder = parseInt(input.getAttribute('data-min')) || 1;
      const subtotalEl = document.getElementById('subtotal_' + input.getAttribute('data-id'));

      if (qty > 0) {
        const subtotal = qty * price;
        grandTotalBal += qty;
        grandTotalPrice += subtotal;

        if (subtotalEl) {
          subtotalEl.textContent = formatRupiah(subtotal);
        }

        activeItems.push({
          name: name,
          qty: qty,
          price: price,
          subtotal: subtotal,
          valid: qty >= minOrder
        });
      } else {
        if (subtotalEl) {
          subtotalEl.textContent = 'Rp 0';
        }
      }
    });

    // Update Summary Card
    if (summaryTotalBal) summaryTotalBal.textContent = grandTotalBal + ' Bal';
    if (summaryTotalNilai) summaryTotalNilai.textContent = formatRupiah(grandTotalPrice);

    if (summaryItemList) {
      if (activeItems.length === 0) {
        summaryItemList.innerHTML = '<p class="text-muted" style="font-size: 0.85rem; font-style: italic;">Belum ada varian produk yang dipilih (kuantitas masih 0 bal).</p>';
      } else {
        let html = '';
        activeItems.forEach(item => {
          html += `
            <div class="summary-item">
              <div>
                <strong>${item.name}</strong><br>
                <small class="text-gold">${item.qty} Bal &times; ${formatRupiah(item.price)}</small>
              </div>
              <div style="font-weight: 700;">${formatRupiah(item.subtotal)}</div>
            </div>
          `;
        });
        summaryItemList.innerHTML = html;
      }
    }
  }

  // Bind change and input events
  qtyInputs.forEach(input => {
    input.addEventListener('input', recalculateOrder);
    input.addEventListener('change', recalculateOrder);
  });

  // Form Submit Validation
  if (orderForm) {
    orderForm.addEventListener('submit', function (e) {
      let totalBal = 0;
      let hasError = false;
      let errorMsg = '';

      qtyInputs.forEach(input => {
        const qty = parseInt(input.value) || 0;
        const minOrder = parseInt(input.getAttribute('data-min')) || 1;
        const name = input.getAttribute('data-name');

        if (qty > 0) {
          totalBal += qty;
          if (qty < minOrder) {
            hasError = true;
            errorMsg = `Varian "${name}" minimal pemesanan adalah ${minOrder} Bal (Anda memasukkan ${qty} Bal).`;
          }
        }
      });

      if (totalBal === 0) {
        e.preventDefault();
        alert('Silakan tentukan minimal 1 varian produk rokok dengan jumlah bal di atas 0.');
        return false;
      }

      if (hasError) {
        e.preventDefault();
        alert(errorMsg);
        return false;
      }
    });
  }

  // Initial calculation
  recalculateOrder();
});
