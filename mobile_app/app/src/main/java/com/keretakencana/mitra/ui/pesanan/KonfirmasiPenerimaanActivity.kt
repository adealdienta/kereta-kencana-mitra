package com.keretakencana.mitra.ui.pesanan

import android.net.Uri
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.databinding.ActivityKonfirmasiPenerimaanBinding
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.File
import java.io.FileOutputStream

class KonfirmasiPenerimaanActivity : AppCompatActivity() {

    private lateinit var binding: ActivityKonfirmasiPenerimaanBinding

    private var pesananId: Long = -1L
    private var kodeTrx: String = ""
    private var selectedImageUri: Uri? = null

    private val selectImageLauncher = registerForActivityResult(
        ActivityResultContracts.GetContent()
    ) { uri: Uri? ->
        if (uri != null) {
            selectedImageUri = uri
            showImagePreview(uri)
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKonfirmasiPenerimaanBinding.inflate(layoutInflater)
        setContentView(binding.root)

        pesananId = intent.getLongExtra(EXTRA_PESANAN_ID, -1L)
        kodeTrx = intent.getStringExtra(EXTRA_KODE_TRX) ?: ""

        setSupportActionBar(binding.toolbarKonfirmasiTerima)
        binding.toolbarKonfirmasiTerima.setNavigationOnClickListener { finish() }

        binding.tvKonfirmasiKodeTrx.text = "Pesanan #$kodeTrx"

        setupListeners()
    }

    private fun setupListeners() {
        binding.frameFotoPicker.setOnClickListener {
            selectImageLauncher.launch("image/*")
        }

        binding.btnGantiFoto.setOnClickListener {
            selectImageLauncher.launch("image/*")
        }

        binding.btnSubmitBukti.setOnClickListener {
            validateAndSubmit()
        }
    }

    private fun showImagePreview(uri: Uri) {
        binding.layoutPlaceholderFoto.visibility = View.GONE
        binding.ivPreviewBukti.visibility = View.VISIBLE
        binding.layoutGantiFoto.visibility = View.VISIBLE
        binding.ivPreviewBukti.setImageURI(uri)
    }

    private fun validateAndSubmit() {
        val uri = selectedImageUri
        if (uri == null) {
            Toast.makeText(this, "Silakan ambil foto bukti fisik terlebih dahulu.", Toast.LENGTH_SHORT).show()
            return
        }

        AlertDialog.Builder(this)
            .setTitle("Konfirmasi Terima?")
            .setMessage("Dengan mengonfirmasi, Anda menyatakan bahwa muatan rokok pesanan #$kodeTrx telah diterima di toko dan pembayaran COD telah diselesaikan.")
            .setPositiveButton("Ya, Konfirmasi") { _, _ ->
                uploadBukti(uri)
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun uploadBukti(uri: Uri) {
        setLoading(true)

        lifecycleScope.launch {
            try {
                val file = createTempFileFromUri(uri)
                if (file == null) {
                    setLoading(false)
                    Toast.makeText(this@KonfirmasiPenerimaanActivity, "Gagal memproses berkas foto.", Toast.LENGTH_SHORT).show()
                    return@launch
                }

                val reqFile = file.asRequestBody("image/*".toMediaTypeOrNull())
                val body = MultipartBody.Part.createFormData("foto_bukti", file.name, reqFile)

                val catatanText = binding.etCatatanPenerima.text.toString().trim()
                val catatanPart = if (catatanText.isNotEmpty()) {
                    catatanText.toRequestBody("text/plain".toMediaTypeOrNull())
                } else null

                val api = ApiClient.getService(this@KonfirmasiPenerimaanActivity)
                val response = api.konfirmasiTerima(pesananId, body, catatanPart)

                setLoading(false)

                if (response.isSuccessful && response.body()?.success == true) {
                    Toast.makeText(
                        this@KonfirmasiPenerimaanActivity,
                        "✓ Penerimaan barang berhasil dikonfirmasi. Status pesanan selesai.",
                        Toast.LENGTH_LONG
                    ).show()
                    finish()
                } else {
                    val errMsg = response.body()?.message ?: "Gagal mengunggah bukti penerimaan."
                    Toast.makeText(this@KonfirmasiPenerimaanActivity, errMsg, Toast.LENGTH_LONG).show()
                }
            } catch (e: Exception) {
                setLoading(false)
                Toast.makeText(this@KonfirmasiPenerimaanActivity, "Terjadi kesalahan: ${e.localizedMessage}", Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun createTempFileFromUri(uri: Uri): File? {
        return try {
            val contentResolver = contentResolver
            val inputStream = contentResolver.openInputStream(uri) ?: return null
            val tempFile = File.createTempFile("bukti_terima_", ".jpg", cacheDir)
            val outputStream = FileOutputStream(tempFile)
            inputStream.copyTo(outputStream)
            inputStream.close()
            outputStream.close()
            tempFile
        } catch (_: Exception) {
            null
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressSubmitBukti.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSubmitBukti.isEnabled = !isLoading
        binding.btnSubmitBukti.text = if (isLoading) "" else "Kirim Bukti Penerimaan"
    }

    companion object {
        const val EXTRA_PESANAN_ID = "extra_pesanan_id"
        const val EXTRA_KODE_TRX = "extra_kode_trx"
    }
}
