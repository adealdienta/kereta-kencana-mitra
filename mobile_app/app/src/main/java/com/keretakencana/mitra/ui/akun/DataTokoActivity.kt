package com.keretakencana.mitra.ui.akun

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.databinding.ActivityDataTokoBinding
import com.keretakencana.mitra.utils.ApiErrorHelper
import kotlinx.coroutines.launch

class DataTokoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDataTokoBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDataTokoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setSupportActionBar(binding.toolbarDataToko)
        binding.toolbarDataToko.setNavigationOnClickListener { finish() }

        populateExistingData()
        setupListeners()
    }

    private fun populateExistingData() {
        val user = SessionManager.getInstance(this).getUser()
        if (user != null) {
            binding.etEditNamaToko.setText(user.namaToko ?: user.name)
            binding.etEditNamaPemilik.setText(user.name)
            binding.etEditEmail.setText(user.email)

            var phone = user.telepon ?: ""
            if (phone.startsWith("0")) phone = phone.substring(1)
            else if (phone.startsWith("62")) phone = phone.substring(2)
            else if (phone.startsWith("+62")) phone = phone.substring(3)
            binding.etEditTelepon.setText(phone)

            binding.etEditAlamatToko.setText(user.alamat ?: "")
        }
    }

    private fun setupListeners() {
        binding.btnSimpanDataToko.setOnClickListener {
            val namaToko = binding.etEditNamaToko.text.toString().trim()
            val name = binding.etEditNamaPemilik.text.toString().trim()
            val email = binding.etEditEmail.text.toString().trim()
            val phone = binding.etEditTelepon.text.toString().trim()
            val alamat = binding.etEditAlamatToko.text.toString().trim()

            if (namaToko.isEmpty()) {
                Toast.makeText(this, "Nama toko wajib diisi.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            if (name.isEmpty()) {
                Toast.makeText(this, "Nama pemilik wajib diisi.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            if (email.isEmpty()) {
                Toast.makeText(this, "Email wajib diisi.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            if (alamat.isEmpty()) {
                Toast.makeText(this, "Alamat pengiriman toko wajib diisi.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val session = SessionManager.getInstance(this)

            setLoading(true)

            lifecycleScope.launch {
                try {
                    val api = ApiClient.getService(this@DataTokoActivity)
                    val payload = mapOf(
                        "name" to name,
                        "nama_toko" to namaToko,
                        "email" to email,
                        "telepon" to phone,
                        "alamat" to alamat
                    )

                    val response = api.updateProfile(payload)
                    setLoading(false)

                    if (response.isSuccessful && response.body()?.success == true) {
                        val updatedUser = response.body()?.data
                        if (updatedUser != null) {
                            session.saveUser(updatedUser)
                        }
                        Toast.makeText(this@DataTokoActivity, "✓ Data & alamat toko berhasil diperbarui.", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        val msg = ApiErrorHelper.parseMessage(response, response.body()?.message ?: "Gagal memperbarui data profil.")
                        Toast.makeText(this@DataTokoActivity, msg, Toast.LENGTH_LONG).show()
                    }
                } catch (e: Exception) {
                    setLoading(false)
                    Toast.makeText(this@DataTokoActivity, "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressDataToko.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSimpanDataToko.isEnabled = !isLoading
        binding.btnSimpanDataToko.text = if (isLoading) "" else "Simpan Perubahan"
    }
}
