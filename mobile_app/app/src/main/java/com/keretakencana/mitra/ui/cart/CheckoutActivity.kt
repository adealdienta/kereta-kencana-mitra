package com.keretakencana.mitra.ui.cart

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.content.Intent
import android.graphics.Color
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
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.data.model.CartItem
import com.keretakencana.mitra.data.model.Pengaturan
import com.keretakencana.mitra.databinding.ActivityCheckoutBinding
import com.keretakencana.mitra.ui.akun.AlamatTokoActivity
import com.keretakencana.mitra.utils.CurrencyHelper
import com.keretakencana.mitra.ui.pesanan.PembayaranTransferActivity
import kotlinx.coroutines.launch
import org.json.JSONObject
import java.util.UUID

class CheckoutActivity : AppCompatActivity() {

    private lateinit var binding: ActivityCheckoutBinding

    private var catatanKurir: String = ""
    private var cartItems: List<CartItem> = emptyList()
    private var grandTotal: Double = 0.0
    private var selectedPaymentMethod: String = "COD" // "COD" atau "TRANSFER_BANK"
    private var isAmbilDiPabrik: Boolean = false
    private lateinit var currentPengaturan: Pengaturan

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCheckoutBinding.inflate(layoutInflater)
        setContentView(binding.root)

        catatanKurir = intent.getStringExtra(EXTRA_CATATAN) ?: ""
        currentPengaturan = SessionManager.getInstance(this).getPengaturan()

        if (catatanKurir.isNotBlank()) {
            binding.cardCatatanPesanan.visibility = View.VISIBLE
            binding.tvCatatanPesananPreview.text = catatanKurir
        } else {
            binding.cardCatatanPesanan.visibility = View.GONE
        }

        setupProfileSnapshot()
        bindTransferInfo()
        fetchPengaturanTerbaru()
        populateCargoItems()
        setupListeners()
        selectDeliveryMode(isAmbilDiPabrik)
    }

    override fun onResume() {
        super.onResume()
        setupProfileSnapshot()
    }

    private fun fetchPengaturanTerbaru() {
        lifecycleScope.launch {
            try {
                val res = ApiClient.getService(this@CheckoutActivity).getPengaturan()
                val data = res.body()?.data
                if (res.isSuccessful && res.body()?.success == true && data != null) {
                    SessionManager.getInstance(this@CheckoutActivity).savePengaturan(data)
                    currentPengaturan = SessionManager.getInstance(this@CheckoutActivity).getPengaturan()
                    bindTransferInfo()
                }
            } catch (_: Exception) {}
        }
    }

    private fun bindTransferInfo() {
        val bankNama = currentPengaturan.bankNama?.trim()
        val bankRekening = currentPengaturan.bankRekening?.trim()
        val bankAtasNama = currentPengaturan.bankAtasNama?.trim()

        if (!bankRekening.isNullOrBlank()) {
            binding.tvTransferBankBadge.text = bankNama?.takeIf { it.isNotBlank() } ?: "Transfer Bank"
            binding.tvTransferRekeningNumber.visibility = View.VISIBLE
            binding.tvTransferRekeningNumber.text = bankRekening
            binding.tvTransferAtasNama.visibility = if (!bankAtasNama.isNullOrBlank()) View.VISIBLE else View.GONE
            binding.tvTransferAtasNama.text = "a.n. ${bankAtasNama ?: "PR KERETA KENCANA"}"
            binding.tvTransferInfoDesc.text =
                "Setelah mengirim pesanan, transfer sesuai total tagihan dan unggah foto struk / screenshot bukti transfer pada menu Pesanan Saya."
        } else {
            binding.tvTransferBankBadge.text = "Konfirmasi Admin"
            binding.tvTransferRekeningNumber.visibility = View.GONE
            binding.tvTransferAtasNama.visibility = View.GONE
            binding.tvTransferInfoDesc.text =
                "Nomor rekening bank resmi pabrik akan diinformasikan langsung oleh Admin via WhatsApp setelah pesanan dibuat. Setelah transfer, unggah bukti transfer melalui menu Pesanan Saya."
        }
    }

    private fun setupProfileSnapshot() {
        val user = SessionManager.getInstance(this).getUser()
        if (user != null) {
            binding.tvCheckoutNamaToko.text = user.namaToko ?: user.name
            binding.tvCheckoutPemilikWa.text = "${user.name} • ${user.telepon ?: "-"}"
            binding.tvCheckoutAlamat.text = user.alamat ?: "Alamat toko belum diisi lengkap."
        }
    }

    private fun populateCargoItems() {
        val cartManager = CartManager.getInstance(this)
        cartItems = cartManager.getItems()

        if (cartItems.isEmpty()) {
            Toast.makeText(this, "Keranjang belanja kosong.", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        binding.layoutItemsMuatan.removeAllViews()
        val totalSlopOrder = cartManager.getTotalSlopQty()
        grandTotal = 0.0
        var totalBungkus = 0

        for (item in cartItems) {
            val itemSubtotal = item.getTieredSubtotal(totalSlopOrder)
            grandTotal += itemSubtotal

            val qty = item.jumlah
            totalBungkus += qty * item.barang.bungkusPerSlop

            val rowLayout = LinearLayout(this).apply {
                orientation = LinearLayout.HORIZONTAL
                layoutParams = LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.MATCH_PARENT,
                    LinearLayout.LayoutParams.WRAP_CONTENT
                ).apply {
                    topMargin = 4
                    bottomMargin = 4
                }
            }

            val tvItemName = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 1f)
                text = "${item.barang.nama} (${item.jumlah} slop)"
                setTextColor(getColor(R.color.text_primary))
                textSize = 13f
            }

            val tvItemSubtotal = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT,
                    LinearLayout.LayoutParams.WRAP_CONTENT
                )
                text = CurrencyHelper.formatRupiah(itemSubtotal)
                setTextColor(getColor(R.color.text_primary))
                textSize = 13f
                gravity = Gravity.END
            }

            rowLayout.addView(tvItemName)
            rowLayout.addView(tvItemSubtotal)
            binding.layoutItemsMuatan.addView(rowLayout)
        }

        binding.tvRincianTotalKuantitas.text = "$totalSlopOrder slop ($totalBungkus bungkus)"
        binding.tvCheckoutTotalHarga.text = CurrencyHelper.formatRupiah(grandTotal)
        binding.tvRincianTotalHarga.text = CurrencyHelper.formatRupiah(grandTotal)
    }

    private fun setupListeners() {
        binding.btnBackCheckout.setOnClickListener { finish() }

        binding.btnModeAntar.setOnClickListener {
            selectDeliveryMode(ambilDiPabrik = false)
        }

        binding.btnModeAmbil.setOnClickListener {
            selectDeliveryMode(ambilDiPabrik = true)
        }

        binding.btnUbahAlamat.setOnClickListener {
            startActivity(Intent(this, AlamatTokoActivity::class.java))
        }

        binding.cardPaymentCod.setOnClickListener {
            selectPaymentMethod("COD")
        }

        binding.cardPaymentTransfer.setOnClickListener {
            selectPaymentMethod("TRANSFER_BANK")
        }

        binding.btnSubmitOrder.setOnClickListener {
            confirmAndSendOrder()
        }
    }

    private fun selectDeliveryMode(ambilDiPabrik: Boolean) {
        isAmbilDiPabrik = ambilDiPabrik

        if (ambilDiPabrik) {
            // Ambil di Pabrik aktif
            binding.btnModeAmbil.setBackgroundResource(R.drawable.bg_card_sharp_selected)
            binding.tvBtnModeAmbil.setTextColor(ContextCompat.getColor(this, R.color.price_red))

            binding.btnModeAntar.setBackgroundColor(Color.TRANSPARENT)
            binding.tvBtnModeAntar.setTextColor(ContextCompat.getColor(this, R.color.text_secondary))

            binding.cardAlamatToko.visibility = View.GONE
            binding.cardAlamatPabrik.visibility = View.VISIBLE

            binding.tvRincianPenerimaan.text = "Ambil Mandiri di Pabrik"
            binding.tvRincianOngkir.text = "Rp 0 (Ambil Mandiri)"
        } else {
            // Diantar ke Toko aktif
            binding.btnModeAntar.setBackgroundResource(R.drawable.bg_card_sharp_selected)
            binding.tvBtnModeAntar.setTextColor(ContextCompat.getColor(this, R.color.price_red))

            binding.btnModeAmbil.setBackgroundColor(Color.TRANSPARENT)
            binding.tvBtnModeAmbil.setTextColor(ContextCompat.getColor(this, R.color.text_secondary))

            binding.cardAlamatToko.visibility = View.VISIBLE
            binding.cardAlamatPabrik.visibility = View.GONE

            binding.tvRincianPenerimaan.text = "Diantar Kurir / Ekspedisi"
            binding.tvRincianOngkir.text = "Area Blitar Gratis (Jarak jauh hubungi Admin)"
        }
    }

    private fun selectPaymentMethod(method: String) {
        selectedPaymentMethod = method
        val isCod = method == "COD"

        binding.rbPaymentCod.isChecked = isCod
        binding.rbPaymentTransfer.isChecked = !isCod

        binding.cardTransferInfo.visibility = if (isCod) View.GONE else View.VISIBLE
    }

    private fun confirmAndSendOrder() {
        val methodDesc = if (selectedPaymentMethod == "COD") {
            "Bayar di Tempat (COD)"
        } else {
            "Transfer Bank"
        }

        val message = if (isAmbilDiPabrik) {
            "Pesanan grosir Anda akan disiapkan di loket gudang pabrik PR Kereta Kencana Ponggok Blitar untuk diambil mandiri dengan metode $methodDesc."
        } else {
            "Pesanan Anda akan dikirim ke alamat toko dengan metode $methodDesc. Pengiriman area Blitar gratis ongkir (untuk lokasi jarak jauh silakan hubungi Admin via WhatsApp)."
        }

        AlertDialog.Builder(this)
            .setTitle("Kirim Pesanan?")
            .setMessage(message)
            .setPositiveButton("Ya, Kirim") { _, _ ->
                executeOrderCreation()
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun executeOrderCreation() {
        setLoading(true)

        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@CheckoutActivity)
                val user = SessionManager.getInstance(this@CheckoutActivity).getUser()
                val idempotencyKey = UUID.randomUUID().toString()

                val itemsPayload = cartItems.map {
                    mapOf(
                        "barang_id" to it.barang.id,
                        "satuan" to it.satuan,
                        "jumlah" to it.jumlah
                    )
                }

                val catatanFinal = if (isAmbilDiPabrik) {
                    if (catatanKurir.isNotBlank()) "Ambil langsung ke pabrik. Catatan: $catatanKurir" else "Ambil langsung ke pabrik"
                } else {
                    catatanKurir
                }

                val alamatFinal = if (isAmbilDiPabrik) {
                    "Gudang Distribusi PR KERETA KENCANA, Ponggok, Blitar (Ambil Mandiri)"
                } else {
                    user?.alamat ?: binding.tvCheckoutAlamat.text.toString()
                }

                val payload = mapOf(
                    "alamat" to alamatFinal,
                    "catatan" to catatanFinal,
                    "metode_pembayaran" to if (selectedPaymentMethod == "COD") "Bayar di Tempat (COD)" else "Transfer Bank",
                    "items" to itemsPayload
                )

                val response = api.createPesanan(idempotencyKey, payload)
                setLoading(false)

                if (response.isSuccessful && response.body()?.success == true) {
                    val transaksi = response.body()?.data
                    val pesananId = transaksi?.id ?: 0L
                    val kodeTrx = transaksi?.kodeTransaksi ?: "TRX-BERHASIL"
                    val formattedTotal = transaksi?.formattedTotal
                        ?: CurrencyHelper.formatRupiah(transaksi?.totalHarga ?: grandTotal)
                    val totalHargaFinal = transaksi?.totalHarga ?: grandTotal

                    // Bersihkan keranjang lokal
                    CartManager.getInstance(this@CheckoutActivity).clearCart()

                    if (selectedPaymentMethod == "TRANSFER_BANK") {
                        // Arahkan ke Layar Pembayaran Transfer Bank & Unggah Bukti
                        val intent = Intent(this@CheckoutActivity, PembayaranTransferActivity::class.java).apply {
                            flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
                            putExtra(PembayaranTransferActivity.EXTRA_PESANAN_ID, pesananId)
                            putExtra(PembayaranTransferActivity.EXTRA_KODE_TRX, kodeTrx)
                            putExtra(PembayaranTransferActivity.EXTRA_TOTAL_HARGA, totalHargaFinal)
                            putExtra(PembayaranTransferActivity.EXTRA_FORMATTED_TOTAL, formattedTotal)
                        }
                        startActivity(intent)
                        finish()
                    } else {
                        // COD: Pindah ke layar sukses pesanan S10
                        val intent = Intent(this@CheckoutActivity, OrderSuccessActivity::class.java).apply {
                            flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
                            putExtra(OrderSuccessActivity.EXTRA_KODE_TRANSAKSI, kodeTrx)
                            putExtra(OrderSuccessActivity.EXTRA_FORMATTED_TOTAL, formattedTotal)
                        }
                        startActivity(intent)
                        finish()
                    }
                } else {
                    handleOrderError(response.code(), response.errorBody()?.string())
                }
            } catch (e: Exception) {
                setLoading(false)
                AlertDialog.Builder(this@CheckoutActivity)
                    .setTitle("Gagal Mengirim")
                    .setMessage("Tidak dapat terhubung ke server pabrik: ${e.localizedMessage}")
                    .setPositiveButton("Tutup", null)
                    .show()
            }
        }
    }

    private fun handleOrderError(code: Int, errorBodyString: String?) {
        val defaultMsg = "Gagal membuat pesanan. Periksa kembali stok dan rincian muatan."
        val serverMsg = parseErrorMessage(errorBodyString) ?: defaultMsg

        if (code == 409) {
            // Order Lock Conflict (Aturan B4)
            AlertDialog.Builder(this)
                .setTitle("⚠️ Pengiriman Masih Berjalan")
                .setMessage(serverMsg)
                .setPositiveButton("Mengerti") { _, _ -> finish() }
                .show()
        } else {
            AlertDialog.Builder(this)
                .setTitle("Pemberitahuan Pabrik")
                .setMessage(serverMsg)
                .setPositiveButton("Tutup", null)
                .show()
        }
    }

    private fun parseErrorMessage(errorJson: String?): String? {
        if (errorJson.isNullOrBlank()) return null
        return try {
            val obj = JSONObject(errorJson)
            if (obj.has("message")) obj.getString("message") else null
        } catch (_: Exception) {
            null
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressSubmitOrder.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSubmitOrder.isEnabled = !isLoading
        binding.btnSubmitOrder.text = if (isLoading) "" else "Kirim Pesanan"
    }

    companion object {
        const val EXTRA_CATATAN = "extra_catatan"
    }
}
