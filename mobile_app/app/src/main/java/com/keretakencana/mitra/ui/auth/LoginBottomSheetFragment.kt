package com.keretakencana.mitra.ui.auth

import android.content.Intent
import android.os.Bundle
import android.text.InputType
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.lifecycle.lifecycleScope
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.databinding.BottomSheetLoginBinding
import kotlinx.coroutines.launch
import org.json.JSONObject

class LoginBottomSheetFragment : BottomSheetDialogFragment() {

    private var _binding: BottomSheetLoginBinding? = null
    private val binding get() = _binding!!

    private var isPasswordVisible = false
    var onLoginSuccessCallback: (() -> Unit)? = null

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = BottomSheetLoginBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupPasswordToggle()
        setupListeners()
    }

    private fun setupPasswordToggle() {
        binding.ivTogglePassword.setOnClickListener {
            isPasswordVisible = !isPasswordVisible
            if (isPasswordVisible) {
                binding.etLoginPassword.inputType =
                    InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_VISIBLE_PASSWORD
                binding.ivTogglePassword.setImageResource(R.drawable.ic_visibility_off)
            } else {
                binding.etLoginPassword.inputType =
                    InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
                binding.ivTogglePassword.setImageResource(R.drawable.ic_visibility)
            }
            binding.etLoginPassword.setSelection(binding.etLoginPassword.text.length)
        }
    }

    private fun setupListeners() {
        binding.btnSubmitLogin.setOnClickListener {
            attemptLogin()
        }

        binding.tvForgotPassword.setOnClickListener {
            dismiss()
            val intent = Intent(requireContext(), ForgotPasswordActivity::class.java)
            startActivity(intent)
        }

        binding.tvGoToRegister.setOnClickListener {
            dismiss()
            val intent = Intent(requireContext(), RegisterActivity::class.java)
            startActivity(intent)
        }
    }

    private fun attemptLogin() {
        val identifier = binding.etLoginIdentifier.text.toString().trim()
        val password = binding.etLoginPassword.text.toString().trim()

        if (identifier.isEmpty()) {
            showError("Silakan masukkan email atau nomor WhatsApp.")
            return
        }

        if (password.isEmpty()) {
            showError("Silakan masukkan kata sandi.")
            return
        }

        setLoading(true)
        hideError()

        lifecycleScope.launch {
            try {
                val apiService = ApiClient.getService(requireContext())
                val payload = mapOf(
                    "identifier" to identifier,
                    "login" to identifier,
                    "password" to password
                )
                val response = apiService.login(payload)

                setLoading(false)
                if (response.isSuccessful && response.body()?.success == true) {
                    val authData = response.body()?.data
                    val token = authData?.token
                    val user = authData?.user

                    if (token != null && user != null) {
                        SessionManager.getInstance(requireContext()).saveAuth(token, user)
                        Toast.makeText(
                            requireContext(),
                            "Selamat datang, ${user.namaToko ?: user.name}!",
                            Toast.LENGTH_SHORT
                        ).show()
                        onLoginSuccessCallback?.invoke()
                        dismiss()
                    } else {
                        showError("Data respon login tidak lengkap dari server.")
                    }
                } else {
                    val errorMsg = parseErrorMessage(response.errorBody()?.string())
                        ?: response.body()?.message
                        ?: "Gagal masuk. Periksa kembali email/nomor WhatsApp dan kata sandi Anda."
                    showError(errorMsg)
                }
            } catch (e: Exception) {
                setLoading(false)
                showError("Gagal menghubungi server: ${e.localizedMessage ?: "Koneksi bermasalah"}")
            }
        }
    }

    private fun parseErrorMessage(errorBodyString: String?): String? {
        if (errorBodyString.isNullOrBlank()) return null
        return try {
            val json = JSONObject(errorBodyString)
            if (json.has("message")) json.getString("message") else null
        } catch (e: Exception) {
            null
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.progressLogin.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.btnSubmitLogin.isEnabled = !isLoading
        binding.btnSubmitLogin.text = if (isLoading) "" else "Masuk"
    }

    private fun showError(msg: String) {
        binding.tvLoginError.text = msg
        binding.tvLoginError.visibility = View.VISIBLE
    }

    private fun hideError() {
        binding.tvLoginError.visibility = View.GONE
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    companion object {
        const val TAG = "LoginBottomSheet"
        fun newInstance(): LoginBottomSheetFragment = LoginBottomSheetFragment()
    }
}
