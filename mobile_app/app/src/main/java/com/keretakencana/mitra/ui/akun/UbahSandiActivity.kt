package com.keretakencana.mitra.ui.akun

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.databinding.ActivityUbahSandiBinding
import kotlinx.coroutines.launch

class UbahSandiActivity : AppCompatActivity() {

    private lateinit var binding: ActivityUbahSandiBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityUbahSandiBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setSupportActionBar(binding.toolbarUbahSandi)
        binding.toolbarUbahSandi.setNavigationOnClickListener { finish() }

        setupListeners()
    }

    private fun setupListeners() {
        binding.btnSimpanSandi.setOnClickListener {
            val passBaru = binding.etPassBaru.text.toString().trim()
            val passKonfirmasi = binding.etPassKonfirmasi.text.toString().trim()

            if (passBaru.length < 6) {
                Toast.makeText(this, "Kata sandi baru minimal 6 karakter.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if (passBaru != passKonfirmasi) {
                Toast.makeText(this, "Konfirmasi kata sandi tidak cocok.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val session = SessionManager.getInstance(this)
            val user = session.getUser() ?: return@setOnClickListener

            setLoading(true)

            lifecycleScope.launch {
                try {
                    val api = ApiClient.getService(this@UbahSandiActivity)
                    val payload = mapOf(
                        "name" to user.name,
                        "nama_toko" to (user.namaToko ?: user.name),
                        "email" to user.email,
                        "telepon" to (user.telepon ?: ""),
                        "alamat" to (user.alamat ?: ""),
                        "password" to passBaru,
                        "password_confirmation" to passKonfirmasi
                    )

                    val response = api.updateProfile(payload)
                    setLoading(false)

                    if (response.isSuccessful && response.body()?.success == true) {
                        com.keretakencana.mitra.data.local.SessionManager.getInstance(this@UbahSandiActivity).addSecurityNotification(
                            com.keretakencana.mitra.data.model.NotifikasiItem(
                                id = "sec_${System.currentTimeMillis()}",
                                judul = "Keamanan Akun: Kata Sandi Berhasil Diperbarui",
                                pesan = "Kata sandi akun toko Anda telah berhasil diperbarui. Jika bukan Anda yang melakukan perubahan ini, segera hubungi admin pabrik.",
                                waktu = com.keretakencana.mitra.utils.DateHelper.getCurrentWibDateTime(),
                                kategori = "keamanan"
                            )
                        )
                        Toast.makeText(this@UbahSandiActivity, "✓ Kata sandi berhasil diperbarui.", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        val msg = response.body()?.message ?: "Gagal memperbarui kata sandi."
                        Toast.makeText(this@UbahSandiActivity, msg, Toast.LENGTH_SHORT).show()
                    }
                } catch (e: Exception) {
                    setLoading(false)
                    Toast.makeText(this@UbahSandiActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressUbahSandi.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSimpanSandi.isEnabled = !isLoading
        binding.btnSimpanSandi.text = if (isLoading) "" else "Simpan Kata Sandi Baru"
    }
}
