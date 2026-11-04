package com.keretakencana.mitra.ui.pesanan

import android.content.ActivityNotFoundException
import android.content.Intent
import android.graphics.Bitmap
import android.graphics.Canvas
import android.graphics.Color
import android.graphics.pdf.PdfDocument
import android.os.Bundle
import android.os.Environment
import android.view.Gravity
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.core.content.FileProvider
import androidx.lifecycle.lifecycleScope
import androidx.print.PrintHelper
import com.google.zxing.BarcodeFormat
import com.google.zxing.MultiFormatWriter
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.model.Transaksi
import com.keretakencana.mitra.databinding.ActivityFakturBinding
import com.keretakencana.mitra.utils.CurrencyHelper
import com.keretakencana.mitra.utils.DateHelper
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File
import java.io.FileOutputStream

class FakturActivity : AppCompatActivity() {

    private lateinit var binding: ActivityFakturBinding
    private var pesananId: Long = -1L
    private var currentTransaksi: Transaksi? = null
    private var currentNppbkc: String = "0821.1.2.00482"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityFakturBinding.inflate(layoutInflater)
        setContentView(binding.root)

        pesananId = intent.getLongExtra(EXTRA_PESANAN_ID, -1L)

        setSupportActionBar(binding.toolbarFaktur)
        binding.toolbarFaktur.setNavigationOnClickListener { finish() }

        setupActions()

        if (pesananId > 0) {
            loadFakturData()
        } else {
            Toast.makeText(this, "ID pesanan tidak valid.", Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    private fun setupActions() {
        binding.btnCetakNota.setOnClickListener {
            val transaksi = currentTransaksi
            if (transaksi == null) {
                Toast.makeText(this, "Data faktur belum siap dicetak.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            cetakSalinanNota(transaksi)
        }

        binding.btnSimpanPdf.setOnClickListener {
            val transaksi = currentTransaksi
            if (transaksi == null) {
                Toast.makeText(this, "Data faktur belum siap disimpan.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            simpanFakturSebagaiPdf(transaksi)
        }
    }

    private fun loadFakturData() {
        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@FakturActivity)

                // Muat NPPBKC resmi pabrik jika ada (Aturan K4)
                try {
                    val configRes = api.getPengaturan()
                    if (configRes.isSuccessful && configRes.body()?.success == true) {
                        val nppbkc = configRes.body()?.data?.nppbkc
                        if (!nppbkc.isNullOrBlank()) {
                            currentNppbkc = nppbkc
                        }
                    }
                } catch (_: Exception) {}

                // Muat data transaksi lengkap
                val response = api.getPesananDetail(pesananId)
                if (response.isSuccessful && response.body()?.success == true) {
                    val data = response.body()?.data
                    if (data != null) {
                        currentTransaksi = data
                        renderFaktur(data)
                    }
                } else {
                    Toast.makeText(this@FakturActivity, "Gagal memuat faktur.", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                Toast.makeText(this@FakturActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun renderFaktur(t: Transaksi) {
        binding.tvFakturNoTrx.text = "No. ${t.kodeTransaksi}"
        binding.tvFakturTanggal.text = DateHelper.formatIsoToIndonesianDateTime(t.createdAt)
        binding.tvFakturMitra.text = "Kepada: ${t.namaMitra}"
        binding.tvFakturAlamat.text = t.alamat
        binding.tvFakturGrandTotal.text = CurrencyHelper.formatRupiah(t.totalHarga)

        // Penandaan Status Lunas vs Belum Dibayar (QRIS vs COD)
        val isSelesai = t.status.equals("Selesai", ignoreCase = true)
        val isQris = t.metodePembayaran?.contains("qris", ignoreCase = true) == true
        val isLunas = t.statusPembayaran.equals("Lunas", ignoreCase = true) || isSelesai

        if (isQris) {
            binding.tvFakturMetodeBayar.text = "Metode bayar: QRIS Resmi PR Kereta Kencana"
        } else {
            binding.tvFakturMetodeBayar.text = "Metode bayar: COD, dibayar tunai saat kurir tiba"
        }

        if (isLunas) {
            binding.tvFakturStatusStempel.text = if (isQris) "LUNAS (QRIS)" else "LUNAS (COD)"
            binding.tvFakturStatusStempel.setBackgroundResource(R.drawable.bg_badge_lunas)
            binding.tvFakturStatusStempel.setTextColor(ContextCompat.getColor(this, R.color.status_green))
        } else {
            binding.tvFakturStatusStempel.text = if (isQris) "MENUNGGU PEMBAYARAN QRIS" else "BELUM DIBAYAR (COD)"
            binding.tvFakturStatusStempel.setBackgroundResource(R.drawable.bg_badge_belum_bayar)
            binding.tvFakturStatusStempel.setTextColor(ContextCompat.getColor(this, R.color.brand_gold))
        }

        // Tampilkan Identitas Invoice & NPPBKC Pabrik
        val invCode = "INV-${t.kodeTransaksi.replace("TRX-", "")}"
        binding.tvFakturInvCode.text = invCode
        binding.tvFakturNppbkc.text = "NPPBKC $currentNppbkc"

        // Generate QR Code Dinamis
        generateAndShowQr(invCode)

        populateFakturItemRows(t)
    }

    private fun populateFakturItemRows(t: Transaksi) {
        binding.layoutFakturItems.removeAllViews()

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

            val tvProduk = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 1.6f)
                text = item.namaBarang
                setTextColor(ContextCompat.getColor(context, R.color.text_primary))
                textSize = 12f
            }

            val tvQty = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 0.8f)
                text = "${item.jumlah} ${item.satuan.lowercase()}"
                setTextColor(ContextCompat.getColor(context, R.color.text_primary))
                textSize = 12f
                gravity = Gravity.CENTER
            }

            val tvSubtotal = TextView(this).apply {
                layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 1.4f)
                text = CurrencyHelper.formatRupiah(item.subtotal)
                setTextColor(ContextCompat.getColor(context, R.color.text_primary))
                textSize = 12f
                textAlignment = View.TEXT_ALIGNMENT_VIEW_END
                gravity = Gravity.END
            }

            row.addView(tvProduk)
            row.addView(tvQty)
            row.addView(tvSubtotal)
            binding.layoutFakturItems.addView(row)
        }
    }

    private fun generateAndShowQr(content: String) {
        lifecycleScope.launch(Dispatchers.Default) {
            try {
                val size = 400
                val bitMatrix = MultiFormatWriter().encode(content, BarcodeFormat.QR_CODE, size, size)
                val width = bitMatrix.width
                val height = bitMatrix.height
                val bitmap = Bitmap.createBitmap(width, height, Bitmap.Config.RGB_565)
                for (x in 0 until width) {
                    for (y in 0 until height) {
                        bitmap.setPixel(x, y, if (bitMatrix[x, y]) Color.BLACK else Color.WHITE)
                    }
                }
                withContext(Dispatchers.Main) {
                    binding.ivFakturQr.setImageBitmap(bitmap)
                }
            } catch (e: Exception) {
                // Fallback jika QR generation gagal
            }
        }
    }

    /**
     * Cetak Salinan Nota via Android PrintManager (Mendukung Thermal Printer & Wireless Print)
     */
    private fun cetakSalinanNota(t: Transaksi) {
        try {
            val bitmap = captureViewBitmap(binding.cardFaktur)
            val printHelper = PrintHelper(this).apply {
                scaleMode = PrintHelper.SCALE_MODE_FIT
            }
            printHelper.printBitmap("Faktur_${t.kodeTransaksi}", bitmap)
        } catch (e: Exception) {
            Toast.makeText(this, "Gagal memulai pencetakan: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
        }
    }

    /**
     * Simpan Faktur sebagai dokumen PDF dan buka/bagikan via Intent Android
     */
    private fun simpanFakturSebagaiPdf(t: Transaksi) {
        lifecycleScope.launch(Dispatchers.IO) {
            try {
                val cardView = binding.cardFaktur
                val viewWidth = withContext(Dispatchers.Main) { cardView.width }
                val viewHeight = withContext(Dispatchers.Main) { cardView.height }

                if (viewWidth <= 0 || viewHeight <= 0) {
                    withContext(Dispatchers.Main) {
                        Toast.makeText(this@FakturActivity, "Tampilan faktur belum selesai dimuat.", Toast.LENGTH_SHORT).show()
                    }
                    return@launch
                }

                val document = PdfDocument()
                val pageInfo = PdfDocument.PageInfo.Builder(viewWidth, viewHeight, 1).create()
                val page = document.startPage(pageInfo)

                withContext(Dispatchers.Main) {
                    cardView.draw(page.canvas)
                }
                document.finishPage(page)

                val dir = getExternalFilesDir(Environment.DIRECTORY_DOCUMENTS) ?: filesDir
                val file = File(dir, "Faktur_${t.kodeTransaksi}.pdf")
                val fos = FileOutputStream(file)
                document.writeTo(fos)
                fos.flush()
                fos.close()
                document.close()

                withContext(Dispatchers.Main) {
                    Toast.makeText(this@FakturActivity, "Faktur berhasil disimpan: ${file.name}", Toast.LENGTH_LONG).show()
                    bukaDokumenPdf(file)
                }
            } catch (e: Exception) {
                withContext(Dispatchers.Main) {
                    Toast.makeText(this@FakturActivity, "Gagal menyimpan PDF: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun captureViewBitmap(view: View): Bitmap {
        val bitmap = Bitmap.createBitmap(view.width, view.height, Bitmap.Config.ARGB_8888)
        val canvas = Canvas(bitmap)
        view.draw(canvas)
        return bitmap
    }

    private fun bukaDokumenPdf(file: File) {
        try {
            val uri = FileProvider.getUriForFile(this, "${packageName}.fileprovider", file)
            val intent = Intent(Intent.ACTION_VIEW).apply {
                setDataAndType(uri, "application/pdf")
                addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            startActivity(intent)
        } catch (e: ActivityNotFoundException) {
            // Jika belum ada penampil PDF default, tawarkan share intent
            val uri = FileProvider.getUriForFile(this, "${packageName}.fileprovider", file)
            val shareIntent = Intent(Intent.ACTION_SEND).apply {
                type = "application/pdf"
                putExtra(Intent.EXTRA_STREAM, uri)
                addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
            }
            startActivity(Intent.createChooser(shareIntent, "Bagikan Faktur PDF"))
        }
    }

    companion object {
        const val EXTRA_PESANAN_ID = "extra_pesanan_id"
    }
}
