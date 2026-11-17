package com.keretakencana.mitra.ui.akun

import android.content.Intent
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.net.Uri
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.databinding.FragmentAkunBinding
import com.keretakencana.mitra.ui.auth.LoginBottomSheetFragment
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import java.io.File
import java.io.FileOutputStream
import java.net.URLEncoder

class AkunFragment : Fragment() {

    private var _binding: FragmentAkunBinding? = null
    private val binding get() = _binding!!

    private var waPabrikNumber: String? = null

    private val selectAvatarLauncher = registerForActivityResult(
        ActivityResultContracts.GetContent()
    ) { uri: Uri? ->
        if (uri != null) {
            uploadAvatar(uri)
        }
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentAkunBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupListeners()
        fetchPengaturanPabrik()
    }

    override fun onResume() {
        super.onResume()
        updateProfileView()
    }

    private fun updateProfileView() {
        val session = SessionManager.getInstance(requireContext())
        val isLoggedIn = session.isLoggedIn()
        val user = session.getUser()

        if (isLoggedIn && user != null) {
            binding.tvAkunNamaToko.text = user.namaToko ?: user.name
            binding.tvAkunPemilikWa.text = "${user.name} • ${user.telepon ?: "-"}"
            binding.tvAkunBadgeStatus.text = "Mitra Resmi B2B"
            binding.tvAkunBadgeStatus.visibility = View.VISIBLE

            binding.cardMenuAkun.visibility = View.VISIBLE

            val fotoUrl = user.fotoProfilUrl
            if (!fotoUrl.isNullOrBlank()) {
                binding.ivAkunAvatar.imageTintList = null
                Glide.with(this)
                    .load(fotoUrl)
                    .circleCrop()
                    .placeholder(R.drawable.ic_account)
                    .error(R.drawable.ic_account)
                    .into(binding.ivAkunAvatar)
            } else {
                binding.ivAkunAvatar.setImageResource(R.drawable.ic_account)
                binding.ivAkunAvatar.imageTintList = ContextCompat.getColorStateList(requireContext(), R.color.brand_brown_dark)
            }

            binding.btnAkunAuthAction.text = "Keluar dari Akun"
            binding.btnAkunAuthAction.setTextColor(ContextCompat.getColor(requireContext(), R.color.status_red))
            binding.btnAkunAuthAction.setBackgroundResource(R.drawable.bg_rounded_card)
        } else {
            binding.tvAkunNamaToko.text = "Selamat Datang, Mitra"
            binding.tvAkunPemilikWa.text = "Masuk untuk mengelola profil & pesanan toko Anda"
            binding.tvAkunBadgeStatus.visibility = View.GONE

            binding.ivAkunAvatar.setImageResource(R.drawable.ic_account)
            binding.ivAkunAvatar.imageTintList = ContextCompat.getColorStateList(requireContext(), R.color.brand_brown_dark)

            binding.cardMenuAkun.visibility = View.GONE

            binding.btnAkunAuthAction.text = "Masuk / Daftar Akun"
            binding.btnAkunAuthAction.setTextColor(ContextCompat.getColor(requireContext(), R.color.white))
            binding.btnAkunAuthAction.setBackgroundResource(R.drawable.bg_button_primary)
        }
    }

    private fun uploadAvatar(uri: Uri) {
        val session = SessionManager.getInstance(requireContext())
        if (!session.isLoggedIn()) return

        binding.progressUploadAvatar.visibility = View.VISIBLE
        binding.ivCameraBadge.visibility = View.GONE

        viewLifecycleOwner.lifecycleScope.launch {
            try {
                val file = createTempFileFromUri(uri)
                if (file == null) {
                    binding.progressUploadAvatar.visibility = View.GONE
                    binding.ivCameraBadge.visibility = View.VISIBLE
                    Toast.makeText(requireContext(), "Gagal memproses berkas gambar.", Toast.LENGTH_SHORT).show()
                    return@launch
                }

                val reqFile = file.asRequestBody("image/*".toMediaTypeOrNull())
                val body = MultipartBody.Part.createFormData("foto", file.name, reqFile)

                val api = ApiClient.getService(requireContext())
                val response = api.uploadFotoProfil(body)

                binding.progressUploadAvatar.visibility = View.GONE
                binding.ivCameraBadge.visibility = View.VISIBLE

                if (response.isSuccessful && response.body()?.success == true) {
                    val updatedUser = response.body()?.data
                    if (updatedUser != null) {
                        session.saveUser(updatedUser)
                    }
                    Toast.makeText(requireContext(), "✓ Foto profil berhasil diperbarui.", Toast.LENGTH_SHORT).show()
                    updateProfileView()
                } else {
                    val rawError = response.errorBody()?.string()
                    val errMsg = try {
                        if (!rawError.isNullOrBlank()) {
                            val json = org.json.JSONObject(rawError)
                            json.optString("message", "Gagal mengunggah foto profil.")
                        } else {
                            response.body()?.message ?: "Gagal mengunggah foto profil."
                        }
                    } catch (_: Exception) {
                        response.body()?.message ?: "Gagal mengunggah foto profil."
                    }
                    Toast.makeText(requireContext(), errMsg, Toast.LENGTH_LONG).show()
                }
            } catch (e: Exception) {
                binding.progressUploadAvatar.visibility = View.GONE
                binding.ivCameraBadge.visibility = View.VISIBLE
                Toast.makeText(requireContext(), "Gagal upload: ${e.localizedMessage}", Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun createTempFileFromUri(uri: Uri): File? {
        return try {
            val context = context ?: return null
            val inputStream = context.contentResolver.openInputStream(uri) ?: return null
            val tempFile = File.createTempFile("avatar_mitra_", ".jpg", context.cacheDir)

            val bitmap = BitmapFactory.decodeStream(inputStream)
            inputStream.close()

            if (bitmap != null) {
                val maxDim = 1080
                val width = bitmap.width
                val height = bitmap.height
                val (scaledWidth, scaledHeight) = if (width > maxDim || height > maxDim) {
                    val ratio = width.toFloat() / height.toFloat()
                    if (ratio > 1) {
                        Pair(maxDim, (maxDim / ratio).toInt())
                    } else {
                        Pair((maxDim * ratio).toInt(), maxDim)
                    }
                } else {
                    Pair(width, height)
                }

                val scaledBitmap = Bitmap.createScaledBitmap(bitmap, scaledWidth, scaledHeight, true)
                val fos = FileOutputStream(tempFile)
                scaledBitmap.compress(Bitmap.CompressFormat.JPEG, 85, fos)
                fos.flush()
                fos.close()
                tempFile
            } else {
                val is2 = context.contentResolver.openInputStream(uri) ?: return null
                val fos = FileOutputStream(tempFile)
                is2.use { input -> fos.use { output -> input.copyTo(output) } }
                tempFile
            }
        } catch (_: Exception) {
            null
        }
    }

    private fun fetchPengaturanPabrik() {
        viewLifecycleOwner.lifecycleScope.launch {
            try {
                val api = ApiClient.getService(requireContext())
                val response = api.getPengaturan()
                if (response.isSuccessful && response.body()?.success == true) {
                    val data = response.body()?.data
                    waPabrikNumber = data?.waPabrik
                    if (!waPabrikNumber.isNullOrBlank()) {
                        binding.tvNomorWaPabrik.text = "Hubungi admin resmi (+${waPabrikNumber})"
                    }
                }
            } catch (_: Exception) {}
        }
    }

    private fun setupListeners() {
        // Upload Foto Profil
        binding.layoutAkunAvatarContainer.setOnClickListener {
            val session = SessionManager.getInstance(requireContext())
            if (session.isLoggedIn()) {
                selectAvatarLauncher.launch("image/*")
            } else {
                Toast.makeText(requireContext(), "Silakan masuk akun terlebih dahulu.", Toast.LENGTH_SHORT).show()
            }
        }

        // Menu 1: Data & Alamat Toko
        binding.itemMenuDataToko.setOnClickListener {
            val intent = Intent(requireContext(), DataTokoActivity::class.java)
            startActivity(intent)
        }

        // Menu 2: Ubah Sandi S20
        binding.itemMenuUbahSandi.setOnClickListener {
            val intent = Intent(requireContext(), UbahSandiActivity::class.java)
            startActivity(intent)
        }

        // Menu 3: WhatsApp Pabrik
        binding.itemMenuWaPabrik.setOnClickListener {
            hubungiWaPabrik()
        }

        // Tombol Keluar / Masuk
        binding.btnAkunAuthAction.setOnClickListener {
            val session = SessionManager.getInstance(requireContext())
            if (session.isLoggedIn()) {
                showDialogKeluar()
            } else {
                val loginSheet = LoginBottomSheetFragment.newInstance()
                loginSheet.onLoginSuccessCallback = {
                    updateProfileView()
                }
                loginSheet.show(childFragmentManager, LoginBottomSheetFragment.TAG)
            }
        }
    }

    private fun hubungiWaPabrik() {
        val targetWa = waPabrikNumber?.takeIf { it.isNotBlank() }
            ?: SessionManager.getInstance(requireContext()).getPengaturan().waPabrik
            ?: SessionManager.DEFAULT_WA_PABRIK
        val cleanPhone = targetWa.replace("+", "").replace("-", "").replace(" ", "").trim()

        val textMessage = "Halo Admin PR. Kereta Kencana, saya ingin menanyakan informasi pemesanan produk grosir tembakau."
        try {
            val url = "https://api.whatsapp.com/send?phone=$cleanPhone&text=${URLEncoder.encode(textMessage, "UTF-8")}"
            val intent = Intent(Intent.ACTION_VIEW).apply {
                data = Uri.parse(url)
            }
            startActivity(intent)
        } catch (e: Exception) {
            Toast.makeText(requireContext(), "Tidak dapat membuka WhatsApp: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
        }
    }

    private fun showDialogKeluar() {
        AlertDialog.Builder(requireContext())
            .setTitle("Keluar dari Akun?")
            .setMessage("Anda akan keluar dari sesi akun mitra toko pada aplikasi ini.")
            .setPositiveButton("Ya, Keluar") { _, _ ->
                eksekusiLogout()
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun eksekusiLogout() {
        viewLifecycleOwner.lifecycleScope.launch {
            try {
                val api = ApiClient.getService(requireContext())
                api.logout()
            } catch (_: Exception) {}

            // Bersihkan sesi lokal
            SessionManager.getInstance(requireContext()).clearSession()
            CartManager.getInstance(requireContext()).clearCart()

            Toast.makeText(requireContext(), "Anda telah keluar dari akun.", Toast.LENGTH_SHORT).show()
            updateProfileView()
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
