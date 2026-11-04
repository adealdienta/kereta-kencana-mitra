package com.keretakencana.mitra.ui.pesanan

import android.content.Intent
import android.graphics.Bitmap
import android.graphics.Color
import android.os.Bundle
import android.os.CountDownTimer
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.lifecycleScope
import com.google.zxing.BarcodeFormat
import com.google.zxing.MultiFormatWriter
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.databinding.ActivityPembayaranQrisBinding
import com.keretakencana.mitra.utils.CurrencyHelper
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.util.Locale

class PembayaranQrisActivity : AppCompatActivity() {

    private lateinit var binding: ActivityPembayaranQrisBinding
    private var pesananId: Long = -1L
    private var kodeTransaksi: String = ""
    private var totalHarga: Double = 0.0
    private var formattedTotal: String = ""
    private var isPaid: Boolean = false
    private var countDownTimer: CountDownTimer? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityPembayaranQrisBinding.inflate(layoutInflater)
        setContentView(binding.root)

        pesananId = intent.getLongExtra(EXTRA_PESANAN_ID, -1L)
        kodeTransaksi = intent.getStringExtra(EXTRA_KODE_TRX) ?: ""
        totalHarga = intent.getDoubleExtra(EXTRA_TOTAL_HARGA, 0.0)
        formattedTotal = intent.getStringExtra(EXTRA_FORMATTED_TOTAL) ?: CurrencyHelper.formatRupiah(totalHarga)

        setSupportActionBar(binding.toolbarQris)
        binding.toolbarQris.setNavigationOnClickListener { handleBackNavigation() }

        setupViews()
        startCountdownTimer()
        generateQrisBarcode()
        checkInitialStatus()
    }

    private fun setupViews() {
        binding.tvQrisTotalTagihan.text = formattedTotal
        binding.tvQrisKodeTrx.text = "No. Pesanan: $kodeTransaksi"

        binding.btnSimulasiBayarQris.setOnClickListener {
            executeSimulasiQris()
        }

        binding.btnActionQris.setOnClickListener {
            if (isPaid) {
                navigateToDetailPesanan()
            } else {
                checkPaymentStatusFromBackend(manualCheck = true)
            }
        }
    }

    private fun startCountdownTimer() {
        // 15 menit countdown timer
        val totalMillis = 15 * 60 * 1000L
        countDownTimer = object : CountDownTimer(totalMillis, 1000L) {
            override fun onTick(millisUntilFinished: Long) {
                val minutes = (millisUntilFinished / 1000) / 60
                val seconds = (millisUntilFinished / 1000) % 60
                binding.tvQrisTimer.text = String.format(Locale.getDefault(), "⏳ %02d:%02d", minutes, seconds)
            }

            override fun onFinish() {
                binding.tvQrisTimer.text = "⚠️ Waktu Habis"
                binding.tvQrisTimer.setTextColor(ContextCompat.getColor(this@PembayaranQrisActivity, R.color.price_red))
            }
        }.start()
    }

    private fun generateQrisBarcode() {
        val nominalInt = totalHarga.toLong()
        // Format payload QRIS standar EMVCo dengan merchant PR KERETA KENCANA & kode transaksi
        val qrPayload = "00020101021226600014ID.GPN.QRIS0118ID1020039281729520459995303360540$nominalInt" +
                "5802ID5917PR KERETA KENCANA6006BLITAR62240520$kodeTransaksi" +
                "6304"

        lifecycleScope.launch(Dispatchers.Default) {
            try {
                val size = 500
                val bitMatrix = MultiFormatWriter().encode(qrPayload, BarcodeFormat.QR_CODE, size, size)
                val width = bitMatrix.width
                val height = bitMatrix.height
                val bitmap = Bitmap.createBitmap(width, height, Bitmap.Config.RGB_565)

                for (x in 0 until width) {
                    for (y in 0 until height) {
                        bitmap.setPixel(x, y, if (bitMatrix[x, y]) Color.BLACK else Color.WHITE)
                    }
                }

                withContext(Dispatchers.Main) {
                    binding.ivQrisBarcode.setImageBitmap(bitmap)
                }
            } catch (e: Exception) {
                withContext(Dispatchers.Main) {
                    Toast.makeText(this@PembayaranQrisActivity, "Gagal merender QR Code.", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun checkInitialStatus() {
        if (pesananId <= 0) return
        checkPaymentStatusFromBackend(manualCheck = false)
    }

    private fun checkPaymentStatusFromBackend(manualCheck: Boolean) {
        if (isPaid) return

        if (manualCheck) {
            binding.btnActionQris.isEnabled = false
            binding.btnActionQris.text = "Memeriksa Status..."
        }

        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@PembayaranQrisActivity)
                val response = api.getPesananDetail(pesananId)

                if (response.isSuccessful && response.body()?.success == true) {
                    val trx = response.body()?.data
                    if (trx != null && trx.statusPembayaran.equals("Lunas", ignoreCase = true)) {
                        setPaidState(trx.kodeTransaksi)
                    } else if (manualCheck) {
                        Toast.makeText(
                            this@PembayaranQrisActivity,
                            "Belum ada pembayaran terdeteksi. Silakan scan barcode di atas.",
                            Toast.LENGTH_SHORT
                        ).show()
                    }
                }
            } catch (_: Exception) {
                if (manualCheck) {
                    Toast.makeText(this@PembayaranQrisActivity, "Gagal memeriksa status pembayaran.", Toast.LENGTH_SHORT).show()
                }
            } finally {
                if (!isPaid && manualCheck) {
                    binding.btnActionQris.isEnabled = true
                    binding.btnActionQris.text = "Cek Status Pembayaran"
                }
            }
        }
    }

    private fun executeSimulasiQris() {
        if (pesananId <= 0) {
            Toast.makeText(this, "ID transaksi tidak ditemukan.", Toast.LENGTH_SHORT).show()
            return
        }

        binding.btnSimulasiBayarQris.isEnabled = false
        binding.progressSimulasiQris.visibility = View.VISIBLE

        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@PembayaranQrisActivity)
                val response = api.simulasiBayarQris(pesananId)

                binding.progressSimulasiQris.visibility = View.GONE

                if (response.isSuccessful && response.body()?.success == true) {
                    val trx = response.body()?.data
                    setPaidState(trx?.kodeTransaksi ?: kodeTransaksi)

                    AlertDialog.Builder(this@PembayaranQrisActivity)
                        .setTitle("✓ Pembayaran Terverifikasi!")
                        .setMessage("Sistem gateway pembayaran telah memverifikasi pelunasan QRIS untuk pesanan $kodeTransaksi. Pesanan Anda kini siap diproses oleh pabrik.")
                        .setPositiveButton("Lihat Pesanan") { _, _ ->
                            navigateToDetailPesanan()
                        }
                        .setCancelable(false)
                        .show()
                } else {
                    binding.btnSimulasiBayarQris.isEnabled = true
                    val errorMsg = response.body()?.message ?: "Gagal memverifikasi simulasi QRIS."
                    Toast.makeText(this@PembayaranQrisActivity, errorMsg, Toast.LENGTH_LONG).show()
                }
            } catch (e: Exception) {
                binding.progressSimulasiQris.visibility = View.GONE
                binding.btnSimulasiBayarQris.isEnabled = true
                Toast.makeText(this@PembayaranQrisActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun setPaidState(kodeTrxUpdated: String) {
        isPaid = true
        countDownTimer?.cancel()

        if (kodeTrxUpdated.isNotBlank()) {
            kodeTransaksi = kodeTrxUpdated
            binding.tvQrisKodeTrx.text = "No. Pesanan: $kodeTransaksi"
        }

        binding.tvQrisTimer.text = "✓ LUNAS"
        binding.tvQrisTimer.setTextColor(ContextCompat.getColor(this, R.color.status_green))

        // Success overlay pada barcode
        binding.layoutQrisSuccessOverlay.visibility = View.VISIBLE

        // Update badge status
        binding.tvQrisStatusBadge.text = "✓ Pembayaran Berhasil Terverifikasi!"
        binding.tvQrisStatusBadge.setBackgroundResource(R.drawable.bg_badge_lunas)
        binding.tvQrisStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.status_green))

        // Ubah tampilan box simulasi
        binding.btnSimulasiBayarQris.visibility = View.GONE
        binding.cardSimulasiQris.visibility = View.GONE

        // Ubah aksi tombol bawah
        binding.btnActionQris.isEnabled = true
        binding.btnActionQris.text = "Lihat Detail Pesanan"
    }

    private fun navigateToDetailPesanan() {
        val intent = Intent(this, DetailPesananActivity::class.java).apply {
            putExtra(DetailPesananActivity.EXTRA_PESANAN_ID, pesananId)
            flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
        }
        startActivity(intent)
        finish()
    }

    private fun handleBackNavigation() {
        if (isPaid) {
            navigateToDetailPesanan()
        } else {
            AlertDialog.Builder(this)
                .setTitle("Tinggalkan Halaman?")
                .setMessage("Pesanan Anda telah dicatat di sistem. Anda dapat melanjutkan pembayaran QRIS kapan saja dari menu Detail Pesanan.")
                .setPositiveButton("Ya, Keluar") { _, _ ->
                    navigateToDetailPesanan()
                }
                .setNegativeButton("Tetap di Sini", null)
                .show()
        }
    }

    override fun onBackPressed() {
        handleBackNavigation()
    }

    override fun onDestroy() {
        super.onDestroy()
        countDownTimer?.cancel()
    }

    companion object {
        const val EXTRA_PESANAN_ID = "extra_pesanan_id"
        const val EXTRA_KODE_TRX = "extra_kode_trx"
        const val EXTRA_TOTAL_HARGA = "extra_total_harga"
        const val EXTRA_FORMATTED_TOTAL = "extra_formatted_total"
    }
}
