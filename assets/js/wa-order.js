/**
 * WhatsApp Order Flow & Auto-Logger
 * PR. KERETA KENCANA
 */

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('waOrderModal');
  const closeModalBtn = document.getElementById('closeWaModal');
  const form = document.getElementById('waOrderForm');
  const varianSelect = document.getElementById('waVarianProduk');
  const openModalBtns = document.querySelectorAll('.btn-open-wa-order');

  // Buka modal saat tombol "Pesan via WhatsApp" diklik
  openModalBtns.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const prodName = this.getAttribute('data-product-name');
      const minOrder = this.getAttribute('data-min-order');

      if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Set varian terpilih otomatis
        if (varianSelect && prodName) {
          for (let i = 0; i < varianSelect.options.length; i++) {
            if (varianSelect.options[i].value === prodName) {
              varianSelect.selectedIndex = i;
              break;
            }
          }
        }

        // Set default minimum order
        const qtyInput = document.getElementById('waJumlah');
        if (qtyInput && minOrder) {
          qtyInput.value = minOrder;
        }
      }
    });
  });

  // Tutup modal
  function closeModal() {
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }
  }

  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeModal);
  }

  // Tutup jika klik area overlay luar
  window.addEventListener('click', function (e) {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Submit Form: Rangkai Pesan WhatsApp & Catat ke Database
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const namaToko = document.getElementById('waNamaToko').value.trim();
      const nomorHp = document.getElementById('waNomorHp').value.trim();
      const alamat = document.getElementById('waAlamat').value.trim();
      const varian = document.getElementById('waVarianProduk').value;
      const jumlah = document.getElementById('waJumlah').value;
      const satuan = document.getElementById('waSatuan').value;
      const catatan = document.getElementById('waCatatan').value.trim();
      const waAdminTarget = form.getAttribute('data-wa-target') || '6281234567890';

      // Hitung perkiraan total harga
      let pricePerUnit = 0;
      if (varianSelect.selectedIndex >= 0) {
        pricePerUnit = parseFloat(varianSelect.options[varianSelect.selectedIndex].getAttribute('data-price')) || 0;
      }
      const totalHarga = pricePerUnit * parseInt(jumlah);

      // 1. Kirim log ke backend database secara asinkron
      const payload = {
        nama_mitra: namaToko,
        telepon: nomorHp,
        alamat: alamat,
        varian_produk: varian,
        jumlah: jumlah,
        satuan: satuan,
        total_harga: totalHarga,
        catatan: catatan
      };

      fetch('api_log_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      }).catch(err => console.log('Log recorded offline/silently'));

      // 2. Rangkai teks pesan WhatsApp resmi
      let message = `*HALO ADMIN PR. KERETA KENCANA*\n` +
                    `Saya ingin memesan pasokan rokok resmi dengan rincian data:\n\n` +
                    `*Nama Toko/Mitra:* ${namaToko}\n` +
                    `*No. WhatsApp:* ${nomorHp}\n` +
                    `*Alamat Tujuan:* ${alamat}\n\n` +
                    `*Pesanan Varian:* ${varian}\n` +
                    `*Volume:* ${jumlah} ${satuan}\n`;

      if (catatan) {
        message += `*Catatan Tambahan:* ${catatan}\n`;
      }

      message += `\nMohon informasi ketersediaan stok pabrik Ponggok & prosedur pengirimannya. Terima kasih.`;

      // 3. Buka WhatsApp
      const waUrl = `https://wa.me/${waAdminTarget}?text=${encodeURIComponent(message)}`;
      closeModal();
      window.open(waUrl, '_blank');
    });
  }
});
