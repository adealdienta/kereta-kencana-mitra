package com.keretakencana.mitra.ui.pesanan

import android.content.Intent
import android.os.Bundle
import android.view.Gravity
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.data.model.Pengaturan
import com.keretakencana.mitra.data.model.Transaksi
import com.keretakencana.mitra.databinding.ActivityDetailPesananBinding
import com.keretakencana.mitra.ui.cart.CartActivity
import com.keretakencana.mitra.utils.CurrencyHelper
import com.keretakencana.mitra.utils.DateHelper
import kotlinx.coroutines.launch

class DetailPesananActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetailPesananBinding
    private var pesananId: Long = -1L
    private var currentTransaksi: Transaksi? = null
    private lateinit var currentPengaturan: Pengaturan

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDetailPesananBinding.inflate(layoutInflater)
        setContentView(binding.root)

        pesananId = intent.getLongExtra(EXTRA_PESANAN_ID, -1L)
        currentPengaturan = SessionManager.getInstance(this).getPengaturan()

        setSupportActionBar(binding.toolbarDetailPesanan)
        binding.toolbarDetailPesanan.setNavigationOnClickListener { finish() }

        if (pesananId > 0) {
            fetchPengaturanTerbaru()
            loadPesananDetail()
        } else {
            Toast.makeText(this, "ID pesanan tidak valid.", Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    private fun fetchPengaturanTerbaru() {
        lifecycleScope.launch {
            try {
                val res = ApiClient.getService(this@DetailPesananActivity).getPengaturan()
                val cfg = res.body()?.data
                if (res.isSuccessful && res.body()?.success == true && cfg != null) {
                    SessionManager.getInstance(this@DetailPesananActivity).savePengaturan(cfg)
                    currentPengaturan = SessionManager.getInstance(this@DetailPesananActivity).getPengaturan()
                    currentTransaksi?.let { renderPesanan(it) }
                }
            } catch (_: Exception) {}
        }
    }

    override fun onResume() {
        super.onResume()
        if (pesananId > 0) {
            loadPesananDetail()
        }
    }

    private fun loadPesananDetail() {
        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@DetailPesananActivity)
                val response = api.getPesananDetail(pesananId)

                if (response.isSuccessful && response.body()?.success == true) {
                    val data = response.body()?.data
                    if (data != null) {
                        currentTransaksi = data
                        renderPesanan(data)
                    }
                } else if (response.code() == 401) {
                    Toast.makeText(this@DetailPesananActivity, "Sesi akun berakhir. Silakan masuk kembali.", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Toast.makeText(this@DetailPesananActivity, "Gagal memuat detail pesanan.", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                Toast.makeText(this@DetailPesananActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun renderPesanan(t: Transaksi) {
        binding.tvDetailKodeTrx.text = t.kodeTransaksi
        binding.tvDetailWaktuPesan.text = "Dipesan pada ${DateHelper.formatIsoToIndonesianDateTime(t.createdAt)}"
        binding.tvDetailGrandTotal.text = CurrencyHelper.formatRupiah(t.totalHarga)

        // Setup Badge Status
        setupBadgeStatus(t.status)

        // Setup Stepper Tracking S25, S27, S31, S33
        setupStepper(t.status)

        // Rincian Muatan Produk
        populateItemRows(t)

        // Catatan Kurir / Pengiriman & Label Ongkir
        val isAmbilMandiri = t.catatan?.contains("ambil", ignoreCase = true) == true ||
                t.alamat.contains("Ambil Mandiri", ignoreCase = true)
        binding.tvDetailCatatanKurir.text = if (!t.catatan.isNullOrBlank()) t.catatan else "Tidak ada catatan khusus."
        binding.tvDetailOngkir.text = if (isAmbilMandiri) {
            "Rp 0 (Ambil Mandiri)"
        } else {
            "Area Blitar Gratis (Jarak jauh hubungi Admin)"
        }
        binding.tvStep3Desc.text = if (isAmbilMandiri) {
            "Pesanan disiapkan untuk diambil langsung di gudang pabrik"
        } else {
            "Kurir / ekspedisi mengantar pesanan ke tujuan toko"
        }

        // Status & Metode Pembayaran
        val isTransfer = t.metodePembayaran?.contains("transfer", ignoreCase = true) == true ||
                t.metodePembayaran?.contains("bca", ignoreCase = true) == true
        val isLunas = t.statusPembayaran.equals("Lunas", ignoreCase = true)
        val isMenungguVerifikasi = t.statusPembayaran?.contains("Menunggu", ignoreCase = true) == true
        val isDitolak = t.statusPembayaran?.contains("Ditolak", ignoreCase = true) == true ||
                t.statusPembayaran?.contains("Tidak Valid", ignoreCase = true) == true ||
                t.statusPembayaran?.contains("Belum Valid", ignoreCase = true) == true

        binding.tvDetailMetodePembayaran.text = if (isTransfer) "Transfer Bank" else (t.metodePembayaran ?: "Bayar di Tempat (COD)")
        binding.tvDetailStatusPembayaran.text = t.statusPembayaran ?: (if (isLunas) "Lunas" else "Belum Dibayar")
        binding.tvDetailStatusPembayaran.background = null
        binding.tvDetailStatusPembayaran.setPadding(0, 0, 0, 0)
        binding.tvDetailStatusPembayaran.setTextColor(
            ContextCompat.getColor(this, 
                if (isLunas) R.color.status_green 
                else if (isDitolak) R.color.status_red
                else R.color.text_primary
            )
        )
        binding.tvDetailLabelTotal.text = if (isTransfer) "Total Tagihan Transfer" else "Total Tagihan COD"

        // Card Aksi Transfer Bank
        val isDibatalkan = t.status.equals("Dibatalkan", ignoreCase = true)
        if (isTransfer && !isDibatalkan) {
            binding.cardDetailQrisAction.visibility = View.VISIBLE
            if (isLunas) {
                binding.tvDetailQrisTitle.text = "Pembayaran Transfer Bank Lunas"
                binding.tvDetailQrisBadge.text = "✓ Terverifikasi"
                binding.tvDetailQrisBadge.background = null
                binding.tvDetailQrisBadge.setPadding(0, 0, 0, 0)
                binding.tvDetailQrisBadge.setTextColor(ContextCompat.getColor(this, R.color.status_green))
                binding.tvDetailQrisDesc.text = "Dana transfer telah diverifikasi lunas oleh bagian keuangan pabrik."
                binding.btnDetailBukaQris.text = "Lihat Bukti Transfer"
            } else if (isDitolak) {
                binding.tvDetailQrisTitle.text = "Bukti Transfer Ditolak / Belum Valid"
                binding.tvDetailQrisBadge.text = "Ditolak"
                binding.tvDetailQrisBadge.background = null
                binding.tvDetailQrisBadge.setPadding(0, 0, 0, 0)
                binding.tvDetailQrisBadge.setTextColor(ContextCompat.getColor(this, R.color.status_red))
                binding.tvDetailQrisDesc.text = "Bukti transfer Anda ditolak oleh admin pabrik. Silakan periksa mutasi rekening dan unggah ulang bukti transfer yang sah."
                binding.btnDetailBukaQris.text = "Unggah Ulang Bukti Transfer"
            } else if (isMenungguVerifikasi || !t.buktiTransferUrl.isNullOrBlank()) {
                binding.tvDetailQrisTitle.text = "Bukti Transfer Sedang Diverifikasi"
                binding.tvDetailQrisBadge.text = "Menunggu Verifikasi"
                binding.tvDetailQrisBadge.background = null
                binding.tvDetailQrisBadge.setPadding(0, 0, 0, 0)
                binding.tvDetailQrisBadge.setTextColor(ContextCompat.getColor(this, R.color.text_secondary))
                binding.tvDetailQrisDesc.text = "Bukti transfer telah diterima dan sedang diperiksa oleh bagian keuangan pabrik."
                binding.btnDetailBukaQris.text = "Lihat / Kirim Ulang Bukti Transfer"
            } else {
                binding.tvDetailQrisTitle.text = "Menunggu Pembayaran Transfer Bank"
                binding.tvDetailQrisBadge.text = "Belum Bayar"
                binding.tvDetailQrisBadge.background = null
                binding.tvDetailQrisBadge.setPadding(0, 0, 0, 0)
                binding.tvDetailQrisBadge.setTextColor(ContextCompat.getColor(this, R.color.text_primary))
                val bankRek = currentPengaturan.bankRekening?.trim()
                val bankNama = currentPengaturan.bankNama?.trim()
                binding.tvDetailQrisDesc.text = if (!bankRek.isNullOrBlank()) {
                    "Silakan transfer ke ${bankNama ?: "Bank"} $bankRek (a.n. ${currentPengaturan.bankAtasNama ?: "PR. Kereta Kencana"}) lalu unggah foto bukti transfer."
                } else {
                    "Hubungi Admin via WhatsApp untuk meminta nomor rekening resmi pabrik, lalu unggah foto bukti transfer."
                }
                binding.btnDetailBukaQris.text = "Bayar / Unggah Bukti Transfer"
            }

            binding.btnDetailBukaQris.setOnClickListener {
                val intent = Intent(this, PembayaranTransferActivity::class.java).apply {
                    putExtra(PembayaranTransferActivity.EXTRA_PESANAN_ID, t.id)
                    putExtra(PembayaranTransferActivity.EXTRA_KODE_TRX, t.kodeTransaksi)
                    putExtra(PembayaranTransferActivity.EXTRA_TOTAL_HARGA, t.totalHarga)
                    putExtra(PembayaranTransferActivity.EXTRA_FORMATTED_TOTAL, CurrencyHelper.formatRupiah(t.totalHarga))
                }
                startActivity(intent)
            }
        } else {
            binding.cardDetailQrisAction.visibility = View.GONE
        }

        // Foto Bukti Penerimaan (Jika Selesai)
        if (!t.buktiPenerimaanUrl.isNullOrBlank()) {
            binding.cardBuktiPenerimaan.visibility = View.VISIBLE
            Glide.with(this)
                .load(t.buktiPenerimaanUrl)
                .placeholder(R.drawable.bg_rounded_card)
                .error(R.drawable.bg_rounded_card)
                .into(binding.ivDetailFotoBukti)

            val terimaText = if (!t.diterimaPada.isNullOrBlank()) {
                "Waktu Selesai: ${DateHelper.formatIsoToIndonesianDateTime(t.diterimaPada)}"
            } else {
                "Pesanan telah diselesaikan"
            }
            binding.tvDetailWaktuTerima.text = terimaText

            val catatan = t.catatanPenerima
            if (!catatan.isNullOrBlank()) {
                binding.tvDetailCatatanPenerima.visibility = View.VISIBLE
                if (catatan.contains("Verifikasi Manual", ignoreCase = true)) {
                    binding.tvDetailLabelBukti.text = "Surat Jalan Fisik (Verifikasi Pabrik)"
                    binding.tvDetailCatatanPenerima.text = "Catatan Otorisasi Pabrik: $catatan"
                } else {
                    binding.tvDetailLabelBukti.text = "Foto Bukti Penerimaan Fisik Toko"
                    binding.tvDetailCatatanPenerima.text = "Catatan Mitra: $catatan"
                }
            } else {
                binding.tvDetailLabelBukti.text = "Foto Bukti Penerimaan Fisik Toko"
                binding.tvDetailCatatanPenerima.visibility = View.GONE
            }
        } else {
            binding.cardBuktiPenerimaan.visibility = View.GONE
        }

        // Snapshot Alamat & Penerima
        binding.tvDetailNamaToko.text = t.namaMitra
        binding.tvDetailPenerimaWa.text = "${t.namaMitra} • ${t.telepon}"
        binding.tvDetailAlamatToko.text = t.alamat

        // Aksi WhatsApp Admin
        setupWhatsAppAction(t)

        // Bottom Action Buttons
        setupBottomActions(t)
    }

    private fun setupWhatsAppAction(t: Transaksi) {
        binding.tvHubungiWaLink.setOnClickListener {
            val isAmbilPabrik = t.catatan?.contains("ambil", ignoreCase = true) == true ||
                    t.alamat.contains("ponggok", ignoreCase = true)

            val metodeText = if (isAmbilPabrik) {
                "Metode: Ambil Langsung di Pabrik.\nSaya berencana mengambil muatan langsung ke pabrik sekarang, mohon dibantu siapkan pesanan di loket gudang ya Admin."
            } else {
                "Metode: Diantar Kurir Pabrik ke Toko."
            }

            val pesanWa = "Halo Admin PR KERETA KENCANA, saya ingin konfirmasi pesanan:\n" +
                    "• No. Transaksi: ${t.kodeTransaksi}\n" +
                    "• Pemesan: ${t.namaMitra}\n" +
                    "• Total: ${CurrencyHelper.formatRupiah(t.totalHarga)}\n" +
                    "• Status Bayar: ${t.statusPembayaran ?: "Belum Dibayar"}\n" +
                    "$metodeText\n\n" +
                    "(Tangkapan layar rincian pesanan saya lampirkan berikut ini). Terima kasih!"

            try {
                val waRaw = currentPengaturan.waPabrik?.takeIf { it.isNotBlank() } ?: SessionManager.DEFAULT_WA_PABRIK
                val adminPhone = waRaw.replace("+", "").replace("-", "").replace(" ", "").trim()
                val url = "https://api.whatsapp.com/send?phone=$adminPhone&text=${java.net.URLEncoder.encode(pesanWa, "UTF-8")}"
                val intent = Intent(Intent.ACTION_VIEW).apply {
                    data = android.net.Uri.parse(url)
                }
                startActivity(intent)
            } catch (_: Exception) {
                Toast.makeText(this, "Tidak dapat membuka aplikasi WhatsApp.", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun setupBadgeStatus(status: String) {
        binding.tvDetailStatusBadge.text = status
        when (status) {
            "Dikirim" -> {
                binding.tvDetailStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.brand_brown_dark))
                binding.tvDetailStatusBadge.setBackgroundResource(R.drawable.bg_rounded_card_selected)
            }
            "Selesai" -> {
                binding.tvDetailStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.status_green))
                binding.tvDetailStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
            }
            "Dibatalkan" -> {
                binding.tvDetailStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.status_red))
                binding.tvDetailStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
            }
            else -> {
                binding.tvDetailStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.brand_brown_dark))
                binding.tvDetailStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
            }
        }
    }

    private fun setupStepper(status: String) {
        if (status.equals("Dibatalkan", ignoreCase = true)) {
            binding.cardTrackingStepper.visibility = View.GONE
            binding.cardBannerDibatalkan.visibility = View.VISIBLE
            return
        }

        binding.cardTrackingStepper.visibility = View.VISIBLE
        binding.cardBannerDibatalkan.visibility = View.GONE

        val greenColor = ContextCompat.getColor(this, R.color.status_green)
        val mutedColor = ContextCompat.getColor(this, R.color.text_muted)

        // Step 1: Dipesan
        binding.ivStep1Icon.setColorFilter(greenColor)

        // Step 2: Disiapkan
        if (status in listOf("Diproses", "Dikirim", "Selesai")) {
            binding.ivStep2Icon.setColorFilter(greenColor)
        } else {
            binding.ivStep2Icon.setColorFilter(mutedColor)
        }

        // Step 3: Dikirim
        if (status in listOf("Dikirim", "Selesai")) {
            binding.ivStep3Icon.setColorFilter(greenColor)
        } else {
            binding.ivStep3Icon.setColorFilter(mutedColor)
        }

        // Step 4: Selesai
        if (status == "Selesai") {
            binding.ivStep4Icon.setColorFilter(greenColor)
        } else {
            binding.ivStep4Icon.setColorFilter(mutedColor)
        }
    }

    private fun populateItemRows(t: Transaksi) {
        binding.layoutDetailItems.removeAllViews()

        val items = t.items ?: emptyList()
        for (item in items) {
            val row = LinearLayout(this).apply {
                orientation = LinearLayout.HORIZONTAL
                layoutParams = LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.MATCH_PARENT,
                    LinearLayout.LayoutParams.WRAP_CONTENT
                ).apply {
                    topMargin = 6
                    bottomMargin = 6
                }
            }

            val tvNameAndQty = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 1f)
                text = "${item.namaBarang} (${item.jumlah} ${item.satuan.lowercase()})"
                setTextColor(ContextCompat.getColor(context, R.color.text_primary))
                textSize = 13f
            }

            val tvSubtotal = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT,
                    LinearLayout.LayoutParams.WRAP_CONTENT
                )
                text = CurrencyHelper.formatRupiah(item.subtotal)
                setTextColor(ContextCompat.getColor(context, R.color.text_primary))
                textSize = 13f
                gravity = Gravity.END
            }

            row.addView(tvNameAndQty)
            row.addView(tvSubtotal)
            binding.layoutDetailItems.addView(row)
        }
    }

    private fun setupBottomActions(t: Transaksi) {
        when (t.status) {
            "Baru Masuk" -> {
                binding.layoutBottomDetailBar.visibility = View.VISIBLE
                binding.btnDetailSecondaryAction.visibility = View.VISIBLE
                binding.btnDetailSecondaryAction.text = "Batalkan Pesanan"
                binding.btnDetailSecondaryAction.setOnClickListener {
                    showDialogBatalPesanan(t.id)
                }

                binding.btnDetailPrimaryAction.visibility = View.GONE
            }
            "Dikirim" -> {
                binding.layoutBottomDetailBar.visibility = View.VISIBLE
                binding.btnDetailSecondaryAction.visibility = View.GONE

                binding.btnDetailPrimaryAction.visibility = View.VISIBLE
                binding.btnDetailPrimaryAction.text = "Konfirmasi Penerimaan Barang"
                binding.btnDetailPrimaryAction.setOnClickListener {
                    val intent = Intent(this, KonfirmasiPenerimaanActivity::class.java).apply {
                        putExtra(KonfirmasiPenerimaanActivity.EXTRA_PESANAN_ID, t.id)
                        putExtra(KonfirmasiPenerimaanActivity.EXTRA_KODE_TRX, t.kodeTransaksi)
                        putExtra(KonfirmasiPenerimaanActivity.EXTRA_METODE_PEMBAYARAN, t.metodePembayaran ?: "COD")
                    }
                    startActivity(intent)
                }
            }
            "Selesai" -> {
                binding.layoutBottomDetailBar.visibility = View.VISIBLE
                binding.btnDetailSecondaryAction.visibility = View.VISIBLE
                binding.btnDetailSecondaryAction.text = "Pesan Lagi"
                binding.btnDetailSecondaryAction.setOnClickListener {
                    handlePesanLagi(t)
                }

                binding.btnDetailPrimaryAction.visibility = View.VISIBLE
                binding.btnDetailPrimaryAction.text = "Lihat Faktur & Nota"
                binding.btnDetailPrimaryAction.setOnClickListener {
                    val intent = Intent(this, FakturActivity::class.java).apply {
                        putExtra(FakturActivity.EXTRA_PESANAN_ID, t.id)
                    }
                    startActivity(intent)
                }
            }
            else -> {
                binding.layoutBottomDetailBar.visibility = View.GONE
            }
        }
    }

    private fun showDialogBatalPesanan(id: Long) {
        AlertDialog.Builder(this)
            .setTitle("Batalkan Pesanan?")
            .setMessage("Apakah Anda yakin ingin membatalkan pesanan ini? Stok barang akan dikembalikan ke gudang pabrik.")
            .setPositiveButton("Ya, Batalkan") { _, _ ->
                eksekusiBatalPesanan(id)
            }
            .setNegativeButton("Tidak", null)
            .show()
    }

    private fun eksekusiBatalPesanan(id: Long) {
        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@DetailPesananActivity)
                val response = api.batalkanPesanan(id)

                if (response.isSuccessful && response.body()?.success == true) {
                    Toast.makeText(this@DetailPesananActivity, "Pesanan berhasil dibatalkan.", Toast.LENGTH_SHORT).show()
                    loadPesananDetail()
                } else {
                    Toast.makeText(this@DetailPesananActivity, "Gagal membatalkan pesanan.", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                Toast.makeText(this@DetailPesananActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun handlePesanLagi(t: Transaksi) {
        val items = t.items ?: emptyList()
        if (items.isEmpty()) {
            Toast.makeText(this, "Tidak ada item untuk dipesan ulang.", Toast.LENGTH_SHORT).show()
            return
        }

        AlertDialog.Builder(this)
            .setTitle("Pesan Lagi?")
            .setMessage("Muatan dari pesanan ini akan ditambahkan ke keranjang belanja Anda (maks. ${Barang.MAX_SLOP_PER_ORDER} Slop online).")
            .setPositiveButton("Ya, Tambah ke Keranjang") { _, _ ->
                lifecycleScope.launch {
                    try {
                        val api = ApiClient.getService(this@DetailPesananActivity)
                        val res = api.getBarang()
                        val catalogMap = (res.body()?.data ?: emptyList()).associateBy { it.id }

                        val groupedSlop = linkedMapOf<Long, Int>()
                        for (detail in items) {
                            val barang = catalogMap[detail.barangId] ?: continue
                            val slopQty = if (detail.satuan.equals("Bal", ignoreCase = true)) {
                                detail.jumlah * (if (barang.slopPerBal > 0) barang.slopPerBal else 20)
                            } else {
                                detail.jumlah
                            }
                            groupedSlop[barang.id] = (groupedSlop[barang.id] ?: 0) + slopQty
                        }

                        val cartPairs = groupedSlop.mapNotNull { (bId, qty) ->
                            val barang = catalogMap[bId]
                            if (barang != null && barang.stok > 0 && qty > 0) barang to qty else null
                        }

                        if (cartPairs.isEmpty()) {
                            Toast.makeText(this@DetailPesananActivity, "Produk pada pesanan ini sedang kosong atau tidak aktif.", Toast.LENGTH_LONG).show()
                            return@launch
                        }

                        CartManager.getInstance(this@DetailPesananActivity).replaceWithSlopItems(cartPairs)
                        val totalRequested = groupedSlop.values.sum()
                        if (totalRequested > Barang.MAX_SLOP_PER_ORDER) {
                            Toast.makeText(
                                this@DetailPesananActivity,
                                "Keranjang diisi maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop sesuai aturan pemesanan online.",
                                Toast.LENGTH_LONG
                            ).show()
                        }
                        startActivity(Intent(this@DetailPesananActivity, CartActivity::class.java))
                    } catch (e: Exception) {
                        Toast.makeText(this@DetailPesananActivity, "Gagal memuat katalog produk: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
                    }
                }
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    companion object {
        const val EXTRA_PESANAN_ID = "extra_pesanan_id"
    }
}
