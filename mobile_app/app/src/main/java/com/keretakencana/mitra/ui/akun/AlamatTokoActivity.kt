package com.keretakencana.mitra.ui.akun

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.databinding.ActivityAlamatTokoBinding
import com.keretakencana.mitra.utils.ApiErrorHelper
import kotlinx.coroutines.launch

class AlamatTokoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityAlamatTokoBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityAlamatTokoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setSupportActionBar(binding.toolbarAlamatToko)
        binding.toolbarAlamatToko.setNavigationOnClickListener { finish() }

        populateExistingAddress()
        setupListeners()
    }

    private fun populateExistingAddress() {
        val user = SessionManager.getInstance(this).getUser()
        if (user != null) {
            binding.etEditAlamatToko.setText(user.alamat ?: "")
        }
    }

    private fun setupListeners() {
        binding.btnSimpanAlamatToko.setOnClickListener {
            val alamat = binding.etEditAlamatToko.text.toString().trim()
            if (alamat.isEmpty()) {
                Toast.makeText(this, "Alamat toko wajib diisi.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val session = SessionManager.getInstance(this)
            val user = session.getUser() ?: return@setOnClickListener

            setLoading(true)

            lifecycleScope.launch {
                try {
                    val api = ApiClient.getService(this@AlamatTokoActivity)
                    val payload = mapOf(
                        "name" to user.name,
                        "nama_toko" to (user.namaToko ?: user.name),
                        "email" to user.email,
                        "telepon" to (user.telepon ?: ""),
                        "alamat" to alamat
                    )

                    val response = api.updateProfile(payload)
                    setLoading(false)

                    if (response.isSuccessful && response.body()?.success == true) {
                        val updatedUser = response.body()?.data
                        if (updatedUser != null) {
                            session.saveUser(updatedUser)
                        }
                        Toast.makeText(this@AlamatTokoActivity, "✓ Alamat toko pengiriman berhasil diperbarui.", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        val msg = ApiErrorHelper.parseMessage(response, response.body()?.message ?: "Gagal memperbarui alamat toko.")
                        Toast.makeText(this@AlamatTokoActivity, msg, Toast.LENGTH_LONG).show()
                    }
                } catch (e: Exception) {
                    setLoading(false)
                    Toast.makeText(this@AlamatTokoActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressAlamatToko.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSimpanAlamatToko.isEnabled = !isLoading
        binding.btnSimpanAlamatToko.text = if (isLoading) "" else "Simpan Alamat Toko"
    }
}
