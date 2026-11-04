package com.keretakencana.mitra.ui.auth

import android.os.Bundle
import android.os.CountDownTimer
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import android.widget.EditText
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.databinding.ActivityForgotPasswordBinding
import kotlinx.coroutines.launch
import org.json.JSONObject

class ForgotPasswordActivity : AppCompatActivity() {

    private lateinit var binding: ActivityForgotPasswordBinding

    private var currentIdentifier: String = ""
    private var resendTimer: CountDownTimer? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityForgotPasswordBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupStep1()
        setupStep2()
        setupStep3()
        setupStep4()

        binding.btnBackForgot.setOnClickListener {
            handleBackPress()
        }
    }

    private fun handleBackPress() {
        val currentStep = binding.viewFlipperForgot.displayedChild
        if (currentStep > 0 && currentStep < 3) {
            hideError()
            binding.viewFlipperForgot.displayedChild = currentStep - 1
        } else {
            finish()
        }
    }

    // ==========================================
    // STEP 1: Minta Kode (S12)
    // ==========================================
    private fun setupStep1() {
        binding.tvBackToLogin1.setOnClickListener { finish() }

        binding.btnSendCode.setOnClickListener {
            val identifier = binding.etForgotIdentifier.text.toString().trim()
            if (identifier.isEmpty()) {
                showError("Masukkan email atau nomor WhatsApp terdaftar.")
                return@setOnClickListener
            }

            hideError()
            setLoadingStep1(true)
            currentIdentifier = identifier

            lifecycleScope.launch {
                try {
                    val api = ApiClient.getService(this@ForgotPasswordActivity)
                    val response = api.forgotPassword(mapOf("login" to identifier))

                    setLoadingStep1(false)
                    if (response.isSuccessful && response.body()?.success == true) {
                        binding.tvOtpTargetInfo.text = "Kode dikirim ke $currentIdentifier"
                        startResendTimer()
                        binding.viewFlipperForgot.displayedChild = 1
                        binding.etOtp1.requestFocus()
                    } else {
                        showError(parseErrorMessage(response.errorBody()?.string()) ?: "Nomor WhatsApp atau Email tidak ditemukan.")
                    }
                } catch (e: Exception) {
                    setLoadingStep1(false)
                    showError("Gagal menghubungi server: ${e.localizedMessage}")
                }
            }
        }
    }

    // ==========================================
    // STEP 2: Masukkan Kode OTP 6 Digit (S13)
    // ==========================================
    private fun setupStep2() {
        setupOtpAutoAdvance(binding.etOtp1, binding.etOtp2, null)
        setupOtpAutoAdvance(binding.etOtp2, binding.etOtp3, binding.etOtp1)
        setupOtpAutoAdvance(binding.etOtp3, binding.etOtp4, binding.etOtp2)
        setupOtpAutoAdvance(binding.etOtp4, binding.etOtp5, binding.etOtp3)
        setupOtpAutoAdvance(binding.etOtp5, binding.etOtp6, binding.etOtp4)
        setupOtpAutoAdvance(binding.etOtp6, null, binding.etOtp5)

        binding.btnVerifyOtp.setOnClickListener {
            val otp = getOtpCode()
            if (otp.length < 6) {
                showError("Masukkan seluruh 6-digit kode verifikasi.")
                return@setOnClickListener
            }

            hideError()
            setLoadingStep2(true)

            lifecycleScope.launch {
                try {
                    val api = ApiClient.getService(this@ForgotPasswordActivity)
                    val response = api.verifyResetToken(mapOf(
                        "login" to currentIdentifier,
                        "token" to otp
                    ))

                    setLoadingStep2(false)
                    if (response.isSuccessful && response.body()?.success == true) {
                        resendTimer?.cancel()
                        binding.viewFlipperForgot.displayedChild = 2
                        binding.etNewPassword.requestFocus()
                    } else {
                        showError(parseErrorMessage(response.errorBody()?.string()) ?: "Kode verifikasi salah atau telah kedaluwarsa.")
                    }
                } catch (e: Exception) {
                    setLoadingStep2(false)
                    showError("Gagal menghubungi server: ${e.localizedMessage}")
                }
            }
        }
    }

    private fun setupOtpAutoAdvance(current: EditText, next: EditText?, prev: EditText?) {
        current.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun afterTextChanged(s: Editable?) {
                if (s?.length == 1) {
                    next?.requestFocus()
                } else if (s?.isEmpty() == true) {
                    prev?.requestFocus()
                }
            }
        })
    }

    private fun getOtpCode(): String {
        return binding.etOtp1.text.toString() +
                binding.etOtp2.text.toString() +
                binding.etOtp3.text.toString() +
                binding.etOtp4.text.toString() +
                binding.etOtp5.text.toString() +
                binding.etOtp6.text.toString()
    }

    private fun startResendTimer() {
        resendTimer?.cancel()
        resendTimer = object : CountDownTimer(45000, 1000) {
            override fun onTick(millisUntilFinished: Long) {
                val sec = millisUntilFinished / 1000
                binding.tvResendOtp.text = String.format("Kirim ulang dalam 00:%02d", sec)
            }

            override fun onFinish() {
                binding.tvResendOtp.text = "Kirim ulang kode"
                binding.tvResendOtp.setOnClickListener {
                    resendOtp()
                }
            }
        }.start()
    }

    private fun resendOtp() {
        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@ForgotPasswordActivity)
                api.forgotPassword(mapOf("login" to currentIdentifier))
                startResendTimer()
            } catch (_: Exception) {}
        }
    }

    // ==========================================
    // STEP 3: Buat Kata Sandi Baru (S14)
    // ==========================================
    private fun setupStep3() {
        binding.btnSaveNewPassword.setOnClickListener {
            val newPass = binding.etNewPassword.text.toString().trim()
            val confirmPass = binding.etConfirmNewPassword.text.toString().trim()

            if (newPass.length < 6) {
                showError("Kata sandi baru minimal 6 karakter.")
                return@setOnClickListener
            }

            if (newPass != confirmPass) {
                showError("Konfirmasi kata sandi tidak cocok.")
                return@setOnClickListener
            }

            hideError()
            setLoadingStep3(true)

            lifecycleScope.launch {
                try {
                    val api = ApiClient.getService(this@ForgotPasswordActivity)
                    val response = api.resetPassword(mapOf(
                        "login" to currentIdentifier,
                        "token" to getOtpCode(),
                        "password" to newPass,
                        "password_confirmation" to confirmPass
                    ))

                    setLoadingStep3(false)
                    if (response.isSuccessful && response.body()?.success == true) {
                        binding.viewFlipperForgot.displayedChild = 3
                    } else {
                        showError(parseErrorMessage(response.errorBody()?.string()) ?: "Gagal memperbarui kata sandi.")
                    }
                } catch (e: Exception) {
                    setLoadingStep3(false)
                    showError("Gagal menghubungi server: ${e.localizedMessage}")
                }
            }
        }
    }

    // ==========================================
    // STEP 4: Sukses (S15)
    // ==========================================
    private fun setupStep4() {
        binding.btnLoginNow.setOnClickListener {
            finish()
        }
    }

    private fun showError(msg: String) {
        binding.tvForgotError.text = msg
        binding.tvForgotError.visibility = View.VISIBLE
    }

    private fun hideError() {
        binding.tvForgotError.visibility = View.GONE
    }

    private fun setLoadingStep1(isLoading: Boolean) {
        binding.progressStep1.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSendCode.isEnabled = !isLoading
        binding.btnSendCode.text = if (isLoading) "" else "Kirim Kode"
    }

    private fun setLoadingStep2(isLoading: Boolean) {
        binding.progressStep2.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnVerifyOtp.isEnabled = !isLoading
        binding.btnVerifyOtp.text = if (isLoading) "" else "Verifikasi"
    }

    private fun setLoadingStep3(isLoading: Boolean) {
        binding.progressStep3.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSaveNewPassword.isEnabled = !isLoading
        binding.btnSaveNewPassword.text = if (isLoading) "" else "Simpan Kata Sandi"
    }

    private fun parseErrorMessage(errorJson: String?): String? {
        if (errorJson.isNullOrBlank()) return null
        return try {
            JSONObject(errorJson).optString("message", null)
        } catch (_: Exception) {
            null
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        resendTimer?.cancel()
    }
}
