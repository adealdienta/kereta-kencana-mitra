package com.keretakencana.mitra.data.api

import android.content.Context
import com.google.gson.GsonBuilder
import com.keretakencana.mitra.BuildConfig
import com.keretakencana.mitra.data.local.SessionManager
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object ApiClient {

    private val DEFAULT_BASE_URL = BuildConfig.API_BASE_URL
    private var currentBaseUrl: String = DEFAULT_BASE_URL

    private var retrofit: Retrofit? = null
    private var apiService: ApiService? = null
    private var unauthorizedHandler: (() -> Unit)? = null

    fun setCustomBaseUrl(newUrl: String) {
        val sanitized = if (newUrl.endsWith("/")) newUrl else "$newUrl/"
        if (currentBaseUrl != sanitized) {
            currentBaseUrl = sanitized
            retrofit = null
            apiService = null
        }
    }

    fun getBaseUrl(): String = currentBaseUrl

    fun getService(context: Context, onUnauthorized: (() -> Unit)? = null): ApiService {
        if (onUnauthorized != null) {
            unauthorizedHandler = onUnauthorized
        }
        return apiService ?: synchronized(this) {
            apiService ?: buildRetrofit(context.applicationContext).create(ApiService::class.java).also {
                apiService = it
            }
        }
    }

    private fun buildRetrofit(context: Context): Retrofit {
        val sessionManager = SessionManager.getInstance(context)

        val loggingInterceptor = HttpLoggingInterceptor().apply {
            level = if (BuildConfig.DEBUG) HttpLoggingInterceptor.Level.BODY else HttpLoggingInterceptor.Level.NONE
        }

        val authInterceptor = AuthInterceptor(sessionManager) {
            unauthorizedHandler?.invoke()
        }

        val okHttpClient = OkHttpClient.Builder()
            .addInterceptor(authInterceptor)
            .addInterceptor(loggingInterceptor)
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .writeTimeout(30, TimeUnit.SECONDS)
            .build()

        val gson = GsonBuilder()
            .setLenient()
            .serializeNulls()
            .create()

        return Retrofit.Builder()
            .baseUrl(currentBaseUrl)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create(gson))
            .build().also {
                retrofit = it
            }
    }
}
