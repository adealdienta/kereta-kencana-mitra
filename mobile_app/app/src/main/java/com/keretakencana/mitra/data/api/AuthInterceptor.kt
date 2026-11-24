package com.keretakencana.mitra.data.api

import com.keretakencana.mitra.data.local.SessionManager
import okhttp3.Interceptor
import okhttp3.Response

/**
 * Interceptor untuk menyisipkan header Authorization Bearer token
 * dan menangani response HTTP 401 (Unauthenticated / sesi kedaluwarsa).
 */
class AuthInterceptor(
    private val sessionManager: SessionManager,
    private val onUnauthorized: (() -> Unit)? = null
) : Interceptor {

    override fun intercept(chain: Interceptor.Chain): Response {
        val originalRequest = chain.request()
        val builder = originalRequest.newBuilder()
            .header("Accept", "application/json")

        val token = sessionManager.getAuthToken()
        if (!token.isNullOrBlank()) {
            builder.header("Authorization", "Bearer $token")
        }

        val response = chain.proceed(builder.build())

        // Deteksi respons 401 Unauthorized dari Sanctum (abaikan endpoint login agar tidak menghapus sesi saat salah sandi)
        val path = originalRequest.url.encodedPath
        if (response.code == 401 && !path.endsWith("/login")) {
            sessionManager.clearSession()
            onUnauthorized?.invoke()
        }

        return response
    }
}
