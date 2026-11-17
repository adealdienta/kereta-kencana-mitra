package com.keretakencana.mitra.ui.pesanan

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.Pengaturan
import com.keretakencana.mitra.databinding.ActivityPembayaranTransferBinding
import com.keretakencana.mitra.utils.CurrencyHelper
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import java.io.File
import java.io.FileOutputStream
import java.net.URLEncoder

class PembayaranTransferActivity : AppCompatActivity() {

    private lateinit var binding: ActivityPembayaranTransferBinding
    private var pesananId: Long = -1L
    private var kodeTransaksi: String = ""
    private var totalHarga: Double = 0.0
    private var formattedTotal: String = ""
    private var selectedImageUri: Uri? = null
    private var isAlreadyUploaded: Boolean = false
    private lateinit var currentPengaturan: Pengaturan

    private val selectImageLauncher = registerForActivityResult(
        ActivityResultContracts.GetContent()
    ) { uri: Uri? ->
        if (uri != null) {
            selectedImageUri = uri
            showImagePreview(uri)
            binding.btnKirimBuktiTransfer.isEnabled = true
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityPembayaranTransferBinding.inflate(layoutInflater)
        setContentView(binding.root)

        pesananId = intent.getLongExtra(EXTRA_PESANAN_ID, -1L)
        kodeTransaksi = intent.getStringExtra(EXTRA_KODE_TRX) ?: ""
        totalHarga = intent.getDoubleExtra(EXTRA_TOTAL_HARGA, 0.0)
        formattedTotal = intent.getStringExtra(EXTRA_FORMATTED_TOTAL) ?: CurrencyHelper.formatRupiah(totalHarga)
        currentPengaturan = SessionManager.getInstance(this).getPengaturan()

        setSupportActionBar(binding.toolbarTransfer)
        binding.toolbarTransfer.setNavigationOnClickListener { navigateToDetailPesanan() }

        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                navigateToDetailPesanan()
            }
        })

        setupViews()
        bindRekeningInfo()
        fetchPengaturan()
        loadPesananStatus()
    }

    private fun fetchPengaturan() {
        lifecycleScope.launch {
            try {
                val res = ApiClient.getService(this@PembayaranTransferActivity).getPengaturan()
                val cfg = res.body()?.data
                if (res.isSuccessful && res.body()?.success == true && cfg != null) {
                    SessionManager.getInstance(this@PembayaranTransferActivity).savePengaturan(cfg)
                    currentPengaturan = SessionManager.getInstance(this@PembayaranTransferActivity).getPengaturan()
                    bindRekeningInfo()
                }
            } catch (_: Exception) {}
        }
    }

    private fun bindRekeningInfo() {
        val bankNama = currentPengaturan.bankNama?.trim()
        val bankRek = currentPengaturan.bankRekening?.trim()
        val bankAtasNama = currentPengaturan.bankAtasNama?.trim()

        if (!bankRek.isNullOrBlank()) {
            binding.tvTransferBankTitle.text = "🏛️ ${bankNama?.takeIf { it.isNotBlank() } ?: "Transfer Bank"}"
            binding.layoutRekeningTersedia.visibility = View.VISIBLE
            binding.layoutRekeningKonfirmasiWa.visibility = View.GONE
            binding.tvNomorRekening.text = bankRek
            binding.tvAtasNamaRekening.text = "Atas Nama: ${bankAtasNama?.takeIf { it.isNotBlank() } ?: "PR KERETA KENCANA"}"
        } else {
            binding.tvTransferBankTitle.text = "🏛️ Transfer Bank"
            binding.layoutRekeningTersedia.visibility = View.GONE
            binding.layoutRekeningKonfirmasiWa.visibility = View.VISIBLE
        }
    }

    private fun setupViews() {
        binding.tvTransferTotalTagihan.text = formattedTotal
        binding.tvTransferKodeTrx.text = "No. Pesanan: $kodeTransaksi"

        // Salin nomor rekening ke clipboard
        binding.btnSalinRekening.setOnClickListener {
            val nomorRekening = binding.tvNomorRekening.text.toString().trim()
            val clipboard = getSystemService(Context.CLIPBOARD_SERVICE) as ClipboardManager
            val clip = ClipData.newPlainText("Nomor Rekening", nomorRekening)
            clipboard.setPrimaryClip(clip)
            Toast.makeText(this, "Nomor rekening $nomorRekening berhasil disalin!", Toast.LENGTH_SHORT).show()
        }

        binding.btnHubungiWaRekening.setOnClickListener {
            val waRaw = currentPengaturan.waPabrik?.takeIf { it.isNotBlank() } ?: SessionManager.DEFAULT_WA_PABRIK
            val cleanPhone = waRaw.replace("+", "").replace("-", "").replace(" ", "").trim()
            val pesan = "Halo Admin PR. Kereta Kencana, saya baru saja membuat pesanan *$kodeTransaksi* senilai *$formattedTotal* dengan metode *Transfer Bank*. Mohon info nomor rekening resmi tujuan transfernya ya Admin. Terima kasih."
            try {
                val url = "https://api.whatsapp.com/send?phone=$cleanPhone&text=${URLEncoder.encode(pesan, "UTF-8")}"
                startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
            } catch (_: Exception) {
                Toast.makeText(this, "Tidak dapat membuka aplikasi WhatsApp.", Toast.LENGTH_SHORT).show()
            }
        }

        binding.btnPilihFotoBukti.setOnClickListener {
            selectImageLauncher.launch("image/*")
        }

        binding.btnKirimBuktiTransfer.setOnClickListener {
            val uri = selectedImageUri
            if (uri != null) {
                confirmAndUploadBukti(uri)
            } else {
                Toast.makeText(this, "Silakan pilih foto bukti transfer terlebih dahulu.", Toast.LENGTH_SHORT).show()
            }
        }

        binding.btnActionTransfer.setOnClickListener {
            navigateToDetailPesanan()
        }
    }

    private fun showImagePreview(uri: Uri) {
        binding.layoutPlaceholderUpload.visibility = View.GONE
        binding.ivPreviewBukti.visibility = View.VISIBLE
        binding.ivPreviewBukti.setImageURI(uri)
    }

    private fun loadPesananStatus() {
        if (pesananId <= 0) return

        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@PembayaranTransferActivity)
                val response = api.getPesananDetail(pesananId)

                if (response.isSuccessful && response.body()?.success == true) {
                    val trx = response.body()?.data
                    if (trx != null) {
                        updateStatusBadge(trx.statusPembayaran ?: "Belum Dibayar")

                        if (!trx.buktiTransferUrl.isNullOrBlank()) {
                            isAlreadyUploaded = true
                            binding.layoutPlaceholderUpload.visibility = View.GONE
                            binding.ivPreviewBukti.visibility = View.VISIBLE
                            Glide.with(this@PembayaranTransferActivity)
                                .load(trx.buktiTransferUrl)
                                .into(binding.ivPreviewBukti)

                            binding.btnPilihFotoBukti.text = "Ganti Foto Bukti Transfer"
                            binding.btnKirimBuktiTransfer.text = "Kirim Ulang Bukti"
                        }
                    }
                }
            } catch (_: Exception) {
                // Ignore network error on status load
            }
        }
    }

    private fun updateStatusBadge(statusPembayaran: String) {
        binding.tvTransferStatusBadge.text = statusPembayaran
        when {
            statusPembayaran.equals("Lunas", ignoreCase = true) -> {
                binding.tvTransferStatusBadge.setBackgroundResource(R.drawable.bg_badge_lunas)
                binding.tvTransferStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.status_green))
                binding.btnKirimBuktiTransfer.visibility = View.GONE
                binding.btnPilihFotoBukti.visibility = View.GONE
            }
            statusPembayaran.contains("Menunggu", ignoreCase = true) -> {
                binding.tvTransferStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
                binding.tvTransferStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.brand_brown_dark))
            }
            statusPembayaran.contains("Ditolak", ignoreCase = true) ||
            statusPembayaran.contains("Tidak Valid", ignoreCase = true) ||
            statusPembayaran.contains("Belum Valid", ignoreCase = true) -> {
                binding.tvTransferStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
                binding.tvTransferStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.status_red))
                binding.btnKirimBuktiTransfer.visibility = View.VISIBLE
                binding.btnPilihFotoBukti.visibility = View.VISIBLE
                binding.btnKirimBuktiTransfer.text = "Kirim Ulang Bukti Transfer"
            }
            else -> {
                binding.tvTransferStatusBadge.setBackgroundResource(R.drawable.bg_badge_belum_bayar)
                binding.tvTransferStatusBadge.setTextColor(ContextCompat.getColor(this, R.color.status_orange))
            }
        }
    }

    private fun confirmAndUploadBukti(uri: Uri) {
        AlertDialog.Builder(this)
            .setTitle("Kirim Bukti Pembayaran?")
            .setMessage("Pastikan nominal transfer, nomor rekening tujuan, dan bukti struk terlihat jelas agar verifikasi dapat diproses cepat oleh pabrik.")
            .setPositiveButton("Ya, Kirim") { _, _ ->
                executeUpload(uri)
            }
            .setNegativeButton("Periksa Ulang", null)
            .show()
    }

    private fun executeUpload(uri: Uri) {
        setLoading(true)

        lifecycleScope.launch {
            try {
                val file = createTempFileFromUri(uri)
                if (file == null) {
                    setLoading(false)
                    Toast.makeText(this@PembayaranTransferActivity, "Gagal memproses file gambar.", Toast.LENGTH_SHORT).show()
                    return@launch
                }

                val reqFile = file.asRequestBody("image/*".toMediaTypeOrNull())
                val body = MultipartBody.Part.createFormData("bukti_transfer", file.name, reqFile)

                val api = ApiClient.getService(this@PembayaranTransferActivity)
                val response = api.uploadBuktiTransfer(pesananId, body)

                setLoading(false)

                if (response.isSuccessful && response.body()?.success == true) {
                    updateStatusBadge("Menunggu Verifikasi")
                    AlertDialog.Builder(this@PembayaranTransferActivity)
                        .setTitle("✓ Bukti Transfer Terkirim")
                        .setMessage("Bukti transfer berhasil diunggah. Bagian keuangan pabrik akan memverifikasi dan pesanan Anda segera diproses.")
                        .setPositiveButton("Lihat Pesanan") { _, _ ->
                            navigateToDetailPesanan()
                        }
                        .setCancelable(false)
                        .show()
                } else {
                    val errorMsg = response.body()?.message ?: "Gagal mengunggah bukti transfer."
                    Toast.makeText(this@PembayaranTransferActivity, errorMsg, Toast.LENGTH_LONG).show()
                }
            } catch (e: Exception) {
                setLoading(false)
                Toast.makeText(this@PembayaranTransferActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun createTempFileFromUri(uri: Uri): File? {
        return try {
            val contentResolver = contentResolver
            val inputStream = contentResolver.openInputStream(uri) ?: return null
            val tempFile = File.createTempFile("bukti_transfer_", ".jpg", cacheDir)
            val outputStream = FileOutputStream(tempFile)
            inputStream.use { input ->
                outputStream.use { output ->
                    input.copyTo(output)
                }
            }
            tempFile
        } catch (_: Exception) {
            null
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressUploadBukti.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnKirimBuktiTransfer.isEnabled = !isLoading
        binding.btnPilihFotoBukti.isEnabled = !isLoading
    }

    private fun navigateToDetailPesanan() {
        val intent = Intent(this, DetailPesananActivity::class.java).apply {
            putExtra(DetailPesananActivity.EXTRA_PESANAN_ID, pesananId)
            flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
        }
        startActivity(intent)
        finish()
    }

    companion object {
        const val EXTRA_PESANAN_ID = "extra_pesanan_id"
        const val EXTRA_KODE_TRX = "extra_kode_trx"
        const val EXTRA_TOTAL_HARGA = "extra_total_harga"
        const val EXTRA_FORMATTED_TOTAL = "extra_formatted_total"
    }
}
