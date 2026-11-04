package com.keretakencana.mitra.ui.cart

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.databinding.ActivityOrderSuccessBinding
import com.keretakencana.mitra.ui.main.MainActivity
import kotlinx.coroutines.launch
import java.net.URLEncoder

class OrderSuccessActivity : AppCompatActivity() {

    private lateinit var binding: ActivityOrderSuccessBinding

    private var kodeTransaksi: String = ""
    private var formattedTotal: String = ""
    private var waPabrik: String? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityOrderSuccessBinding.inflate(layoutInflater)
        setContentView(binding.root)

        kodeTransaksi = intent.getStringExtra(EXTRA_KODE_TRANSAKSI) ?: "TRX-BERHASIL"
        formattedTotal = intent.getStringExtra(EXTRA_FORMATTED_TOTAL) ?: "Rp 0"

        binding.tvSuccessKodeTrx.text = "No. $kodeTransaksi"
        binding.tvSuccessTotalBayar.text = formattedTotal

        fetchPengaturanPabrik()
        setupListeners()
    }

    private fun fetchPengaturanPabrik() {
        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@OrderSuccessActivity)
                val response = api.getPengaturan()
                if (response.isSuccessful && response.body()?.success == true) {
                    waPabrik = response.body()?.data?.waPabrik
                }
            } catch (_: Exception) {}
        }
    }

    private fun setupListeners() {
        binding.btnLihatPesanan.setOnClickListener {
            val intent = Intent(this, MainActivity::class.java).apply {
                flags = Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_NEW_TASK
                putExtra(MainActivity.EXTRA_NAV_TAB, R.id.menu_pesanan)
            }
            startActivity(intent)
            finish()
        }

        binding.btnWaPabrik.setOnClickListener {
            val targetWa = waPabrik ?: "6285735064152"
            val cleanPhone = targetWa.replace("+", "").replace("-", "").replace(" ", "").trim()

            val textMessage = "Halo Admin PR. Kereta Kencana, saya mitra grosir baru saja membuat pesanan dengan nomor *${kodeTransaksi}* senilai *${formattedTotal}*. Mohon konfirmasi dan diproses pengirimannya. Terima kasih."

            try {
                val url = "https://api.whatsapp.com/send?phone=$cleanPhone&text=${URLEncoder.encode(textMessage, "UTF-8")}"
                val waIntent = Intent(Intent.ACTION_VIEW).apply {
                    data = Uri.parse(url)
                }
                startActivity(waIntent)
            } catch (e: Exception) {
                Toast.makeText(this, "Tidak dapat membuka WhatsApp: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
            }
        }
    }

    companion object {
        const val EXTRA_KODE_TRANSAKSI = "extra_kode_transaksi"
        const val EXTRA_FORMATTED_TOTAL = "extra_formatted_total"
    }
}
