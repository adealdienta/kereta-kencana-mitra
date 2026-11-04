package com.keretakencana.mitra.ui.cart

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.Gravity
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.CartItem
import com.keretakencana.mitra.databinding.ActivityCheckoutBinding
import com.keretakencana.mitra.utils.CurrencyHelper
import com.keretakencana.mitra.ui.pesanan.PembayaranQrisActivity
import kotlinx.coroutines.launch
import org.json.JSONObject
import java.util.UUID

class CheckoutActivity : AppCompatActivity() {

    private lateinit var binding: ActivityCheckoutBinding

    private var catatanKurir: String = ""
    private var cartItems: List<CartItem> = emptyList()
    private var grandTotal: Double = 0.0
    private var selectedPaymentMethod: String = "COD" // "COD" atau "QRIS"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCheckoutBinding.inflate(layoutInflater)
        setContentView(binding.root)

        catatanKurir = intent.getStringExtra(EXTRA_CATATAN) ?: ""

        setupProfileSnapshot()
        populateCargoItems()
        setupListeners()
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
        grandTotal = 0.0

        for (item in cartItems) {
            grandTotal += item.subtotal

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
                text = "${item.barang.nama} (${item.jumlah} ${item.satuan.lowercase()})"
                setTextColor(getColor(R.color.text_primary))
                textSize = 13f
            }

            val tvItemSubtotal = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT,
                    LinearLayout.LayoutParams.WRAP_CONTENT
                )
                text = CurrencyHelper.formatRupiah(item.subtotal)
                setTextColor(getColor(R.color.text_primary))
                textSize = 13f
                gravity = Gravity.END
            }

            rowLayout.addView(tvItemName)
            rowLayout.addView(tvItemSubtotal)
            binding.layoutItemsMuatan.addView(rowLayout)
        }

        binding.tvCheckoutTotalHarga.text = CurrencyHelper.formatRupiah(grandTotal)
    }

    private fun setupListeners() {
        binding.btnBackCheckout.setOnClickListener { finish() }

        binding.btnUbahAlamat.setOnClickListener {
            Toast.makeText(this, "Alamat pengiriman dapat diperbarui pada menu profil akun.", Toast.LENGTH_SHORT).show()
        }

        binding.cardPaymentCod.setOnClickListener {
            selectPaymentMethod("COD")
        }

        binding.cardPaymentQris.setOnClickListener {
            selectPaymentMethod("QRIS")
        }

        binding.btnSubmitOrder.setOnClickListener {
            confirmAndSendOrder()
        }
    }

    private fun selectPaymentMethod(method: String) {
        selectedPaymentMethod = method
        val isCod = method == "COD"

        binding.rbPaymentCod.isChecked = isCod
        binding.rbPaymentQris.isChecked = !isCod

        binding.layoutPaymentCod.setBackgroundResource(
            if (isCod) R.drawable.bg_rounded_card_selected else R.drawable.bg_rounded_card
        )
        binding.layoutPaymentQris.setBackgroundResource(
            if (!isCod) R.drawable.bg_rounded_card_selected else R.drawable.bg_rounded_card
        )

        binding.cardQrisInfo.visibility = if (isCod) View.GONE else View.VISIBLE
    }

    private fun confirmAndSendOrder() {
        val methodDesc = if (selectedPaymentMethod == "COD") {
            "Bayar di Tempat (COD)"
        } else {
            "QRIS (Instan & Otomatis)"
        }

        AlertDialog.Builder(this)
            .setTitle("Kirim Pesanan?")
            .setMessage("Pesanan grosir akan dikirim langsung oleh kurir resmi pabrik ke alamat toko Anda dengan metode $methodDesc.")
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
                val idempotencyKey = UUID.randomUUID().toString()

                val itemsPayload = cartItems.map {
                    mapOf(
                        "barang_id" to it.barang.id,
                        "satuan" to it.satuan,
                        "jumlah" to it.jumlah
                    )
                }

                val payload = mapOf(
                    "catatan" to catatanKurir,
                    "metode_pembayaran" to if (selectedPaymentMethod == "COD") "Bayar di Tempat (COD)" else "QRIS",
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

                    if (selectedPaymentMethod == "QRIS") {
                        // Arahkan ke Layar Pembayaran QRIS Dinamis & Simulasi
                        val intent = Intent(this@CheckoutActivity, PembayaranQrisActivity::class.java).apply {
                            flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
                            putExtra(PembayaranQrisActivity.EXTRA_PESANAN_ID, pesananId)
                            putExtra(PembayaranQrisActivity.EXTRA_KODE_TRX, kodeTrx)
                            putExtra(PembayaranQrisActivity.EXTRA_TOTAL_HARGA, totalHargaFinal)
                            putExtra(PembayaranQrisActivity.EXTRA_FORMATTED_TOTAL, formattedTotal)
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
