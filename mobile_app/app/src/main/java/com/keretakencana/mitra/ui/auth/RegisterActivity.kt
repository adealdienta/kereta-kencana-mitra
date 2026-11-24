package com.keretakencana.mitra.ui.auth

import android.content.Intent
import android.os.Bundle
import android.text.InputType
import android.view.View
import android.widget.EditText
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.databinding.ActivityRegisterBinding
import kotlinx.coroutines.launch
import org.json.JSONObject

class RegisterActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterBinding

    private var isPasswordVisible = false
    private var isConfirmPasswordVisible = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupPasswordToggles()
        setupListeners()
    }

    private fun setupPasswordToggles() {
        binding.ivTogglePassword.setOnClickListener {
            isPasswordVisible = !isPasswordVisible
            togglePasswordVisibility(binding.etPassword, isPasswordVisible)
            binding.ivTogglePassword.setImageResource(
                if (isPasswordVisible) R.drawable.ic_visibility_off else R.drawable.ic_visibility
            )
        }

        binding.ivToggleConfirmPassword.setOnClickListener {
            isConfirmPasswordVisible = !isConfirmPasswordVisible
            togglePasswordVisibility(binding.etPasswordConfirmation, isConfirmPasswordVisible)
            binding.ivToggleConfirmPassword.setImageResource(
                if (isConfirmPasswordVisible) R.drawable.ic_visibility_off else R.drawable.ic_visibility
            )
        }
    }

    private fun togglePasswordVisibility(editText: EditText, isVisible: Boolean) {
        if (isVisible) {
            editText.inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_VISIBLE_PASSWORD
        } else {
            editText.inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
        }
        editText.setSelection(editText.text.length)
    }

    private fun setupListeners() {
        binding.btnBack.setOnClickListener { finish() }

        binding.tvLoginLink.setOnClickListener {
            finish()
        }

        binding.btnSubmitRegister.setOnClickListener {
            attemptRegister()
        }
    }

    private fun attemptRegister() {
        clearErrors()

        val namaToko = binding.etNamaToko.text.toString().trim()
        val namaPemilik = binding.etNamaPemilik.text.toString().trim()
        val telepon = binding.etTelepon.text.toString().trim()
        val email = binding.etEmail.text.toString().trim()
        val password = binding.etPassword.text.toString().trim()
        val passwordConfirmation = binding.etPasswordConfirmation.text.toString().trim()
        val alamat = binding.etAlamat.text.toString().trim()
        val isUsia21 = binding.cbUsia21.isChecked

        var hasError = false

        if (namaToko.isEmpty()) {
            showFieldError(binding.etNamaToko, null, "Nama toko wajib diisi")
            hasError = true
        }

        if (namaPemilik.isEmpty()) {
            showFieldError(binding.etNamaPemilik, null, "Nama pemilik wajib diisi")
            hasError = true
        }

        if (telepon.isEmpty()) {
            showFieldError(binding.etTelepon, binding.tvErrorTelepon, "Nomor WhatsApp wajib diisi")
            hasError = true
        }

        if (email.isEmpty() || !android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            showFieldError(binding.etEmail, binding.tvErrorEmail, "Masukkan alamat email yang valid")
            hasError = true
        }

        if (password.length < 6) {
            showFieldError(binding.etPassword, binding.tvErrorPassword, "Kata sandi minimal 6 karakter")
            hasError = true
        }

        if (password != passwordConfirmation) {
            showFieldError(binding.etPasswordConfirmation, binding.tvErrorPasswordConfirmation, "Kata sandi tidak sama")
            hasError = true
        }

        if (alamat.isEmpty()) {
            showFieldError(binding.etAlamat, null, "Alamat toko wajib diisi")
            hasError = true
        }

        if (!isUsia21) {
            binding.tvGeneralError.text = "Pendaftaran mensyaratkan pemilik berusia 21 tahun ke atas."
            binding.tvGeneralError.visibility = View.VISIBLE
            hasError = true
        }

        if (hasError) return

        setLoading(true)

        lifecycleScope.launch {
            try {
                val apiService = ApiClient.getService(this@RegisterActivity)
                val payload = mapOf(
                    "nama_toko" to namaToko,
                    "name" to namaPemilik,
                    "telepon" to telepon,
                    "email" to email,
                    "password" to password,
                    "password_confirmation" to passwordConfirmation,
                    "alamat" to alamat,
                    "usia_21" to isUsia21,
                    "role" to "pelanggan"
                )

                val response = apiService.register(payload)
                setLoading(false)

                if (response.isSuccessful && response.body()?.success == true) {
                    val authData = response.body()?.data
                    val token = authData?.token
                    val user = authData?.user

                    if (token != null && user != null) {
                        SessionManager.getInstance(this@RegisterActivity).saveAuth(token, user)
                    }

                    // Buka layar sukses S8
                    val intent = Intent(this@RegisterActivity, RegisterSuccessActivity::class.java)
                    startActivity(intent)
                    finish()
                } else {
                    handleBackendError(response.errorBody()?.string())
                }
            } catch (e: Exception) {
                setLoading(false)
                binding.tvGeneralError.text = "Gagal terhubung ke server: ${e.localizedMessage ?: "Jaringan bermasalah"}"
                binding.tvGeneralError.visibility = View.VISIBLE
            }
        }
    }

    private fun handleBackendError(errorJsonString: String?) {
        if (errorJsonString.isNullOrBlank()) {
            binding.tvGeneralError.text = "Pendaftaran gagal. Periksa kembali isian formulir."
            binding.tvGeneralError.visibility = View.VISIBLE
            return
        }

        try {
            val json = JSONObject(errorJsonString)
            val message = json.optString("message", "Terjadi kesalahan validasi")
            val errorsObj = json.optJSONObject("errors")

            if (errorsObj != null) {
                var shownFieldError = false
                if (errorsObj.has("nama_toko")) {
                    val err = errorsObj.getJSONArray("nama_toko").getString(0)
                    showFieldError(binding.etNamaToko, null, err)
                    binding.tvGeneralError.text = err
                    binding.tvGeneralError.visibility = View.VISIBLE
                    shownFieldError = true
                }
                if (errorsObj.has("name")) {
                    val err = errorsObj.getJSONArray("name").getString(0)
                    showFieldError(binding.etNamaPemilik, null, err)
                    if (!shownFieldError) {
                        binding.tvGeneralError.text = err
                        binding.tvGeneralError.visibility = View.VISIBLE
                        shownFieldError = true
                    }
                }
                if (errorsObj.has("telepon")) {
                    val err = errorsObj.getJSONArray("telepon").getString(0)
                    showFieldError(binding.etTelepon, binding.tvErrorTelepon, err)
                    shownFieldError = true
                }
                if (errorsObj.has("email")) {
                    val err = errorsObj.getJSONArray("email").getString(0)
                    showFieldError(binding.etEmail, binding.tvErrorEmail, err)
                    shownFieldError = true
                }
                if (errorsObj.has("password")) {
                    val err = errorsObj.getJSONArray("password").getString(0)
                    showFieldError(binding.etPassword, binding.tvErrorPassword, err)
                    shownFieldError = true
                }
                if (errorsObj.has("alamat")) {
                    val err = errorsObj.getJSONArray("alamat").getString(0)
                    showFieldError(binding.etAlamat, null, err)
                    if (!shownFieldError) {
                        binding.tvGeneralError.text = err
                        binding.tvGeneralError.visibility = View.VISIBLE
                        shownFieldError = true
                    }
                }
                if (!shownFieldError) {
                    binding.tvGeneralError.text = message
                    binding.tvGeneralError.visibility = View.VISIBLE
                }
            } else {
                binding.tvGeneralError.text = message
                binding.tvGeneralError.visibility = View.VISIBLE
            }
        } catch (e: Exception) {
            binding.tvGeneralError.text = "Terjadi kesalahan saat memproses data pendaftaran."
            binding.tvGeneralError.visibility = View.VISIBLE
        }
    }

    private fun showFieldError(editText: EditText, errorTextView: TextView?, message: String) {
        editText.setBackgroundResource(R.drawable.bg_rounded_input_error)
        if (errorTextView != null) {
            errorTextView.text = message
            errorTextView.visibility = View.VISIBLE
        }
    }

    private fun clearErrors() {
        binding.tvGeneralError.visibility = View.GONE
        binding.tvErrorTelepon.visibility = View.GONE
        binding.tvErrorEmail.visibility = View.GONE
        binding.tvErrorPassword.visibility = View.GONE
        binding.tvErrorPasswordConfirmation.visibility = View.GONE

        binding.etNamaToko.setBackgroundResource(R.drawable.bg_rounded_input)
        binding.etNamaPemilik.setBackgroundResource(R.drawable.bg_rounded_input)
        binding.etTelepon.setBackgroundResource(R.drawable.bg_rounded_input)
        binding.etEmail.setBackgroundResource(R.drawable.bg_rounded_input)
        binding.etPassword.setBackgroundResource(R.drawable.bg_rounded_input)
        binding.etPasswordConfirmation.setBackgroundResource(R.drawable.bg_rounded_input)
        binding.etAlamat.setBackgroundResource(R.drawable.bg_rounded_input)
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressRegister.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSubmitRegister.isEnabled = !isLoading
        binding.btnSubmitRegister.text = if (isLoading) "" else "Daftar  ➔"
    }
}
