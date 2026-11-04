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

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDetailPesananBinding.inflate(layoutInflater)
        setContentView(binding.root)

        pesananId = intent.getLongExtra(EXTRA_PESANAN_ID, -1L)

        setSupportActionBar(binding.toolbarDetailPesanan)
        binding.toolbarDetailPesanan.setNavigationOnClickListener { finish() }

        if (pesananId > 0) {
            loadPesananDetail()
        } else {
            Toast.makeText(this, "ID pesanan tidak valid.", Toast.LENGTH_SHORT).show()
            finish()
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

        // Catatan Kurir
        binding.tvDetailCatatanKurir.text = if (!t.catatan.isNullOrBlank()) t.catatan else "Tidak ada catatan khusus."

        // Status & Metode Pembayaran
        val isQris = t.metodePembayaran?.contains("qris", ignoreCase = true) == true
        val isLunas = t.statusPembayaran.equals("Lunas", ignoreCase = true)

        binding.tvDetailMetodePembayaran.text = if (isQris) "QRIS (Instan & Otomatis)" else (t.metodePembayaran ?: "Bayar di Tempat (COD)")
        binding.tvDetailStatusPembayaran.text = if (isLunas) "Lunas" else "Belum Dibayar"
        binding.tvDetailStatusPembayaran.setBackgroundResource(if (isLunas) R.drawable.bg_badge_lunas else R.drawable.bg_badge_belum_bayar)
        binding.tvDetailStatusPembayaran.setTextColor(ContextCompat.getColor(this, if (isLunas) R.color.status_green else R.color.brand_gold))
        binding.tvDetailLabelTotal.text = if (isQris) "Total Tagihan QRIS" else "Total Tagihan COD"

        // Card Aksi QRIS Dinamis
        val isDibatalkan = t.status.equals("Dibatalkan", ignoreCase = true)
        if (isQris && !isDibatalkan) {
            binding.cardDetailQrisAction.visibility = View.VISIBLE
            if (isLunas) {
                binding.tvDetailQrisTitle.text = "Pembayaran QRIS Lunas"
                binding.tvDetailQrisBadge.text = "✓ Terverifikasi"
                binding.tvDetailQrisBadge.setBackgroundResource(R.drawable.bg_badge_lunas)
                binding.tvDetailQrisBadge.setTextColor(ContextCompat.getColor(this, R.color.status_green))
                binding.tvDetailQrisDesc.text = "Pembayaran telah berhasil diverifikasi oleh sistem gateway pabrik."
                binding.btnDetailBukaQris.text = "Lihat Kode QRIS"
            } else {
                binding.tvDetailQrisTitle.text = "Pembayaran QRIS Menunggu Pelunasan"
                binding.tvDetailQrisBadge.text = "Menunggu Scan"
                binding.tvDetailQrisBadge.setBackgroundResource(R.drawable.bg_badge_belum_bayar)
                binding.tvDetailQrisBadge.setTextColor(ContextCompat.getColor(this, R.color.status_orange))
                binding.tvDetailQrisDesc.text = "Pesanan ini belum dibayar. Buka kode QRIS untuk melakukan scan dan simulasi pelunasan instan."
                binding.btnDetailBukaQris.text = "⚡ Buka Kode QRIS / Bayar Sekarang"
            }

            binding.btnDetailBukaQris.setOnClickListener {
                val intent = Intent(this, PembayaranQrisActivity::class.java).apply {
                    putExtra(PembayaranQrisActivity.EXTRA_PESANAN_ID, t.id)
                    putExtra(PembayaranQrisActivity.EXTRA_KODE_TRX, t.kodeTransaksi)
                    putExtra(PembayaranQrisActivity.EXTRA_TOTAL_HARGA, t.totalHarga)
                    putExtra(PembayaranQrisActivity.EXTRA_FORMATTED_TOTAL, CurrencyHelper.formatRupiah(t.totalHarga))
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

        // Bottom Action Buttons
        setupBottomActions(t)
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
            .setMessage("Muatan dari pesanan ini akan ditambahkan kembali ke keranjang belanja Anda.")
            .setPositiveButton("Ya, Tambah ke Keranjang") { _, _ ->
                val intent = Intent(this, CartActivity::class.java)
                startActivity(intent)
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    companion object {
        const val EXTRA_PESANAN_ID = "extra_pesanan_id"
    }
}
