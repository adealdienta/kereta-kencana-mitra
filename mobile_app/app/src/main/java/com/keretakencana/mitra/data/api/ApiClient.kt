package com.keretakencana.mitra.data.api

import android.content.Context
import com.google.gson.GsonBuilder
import com.keretakencana.mitra.data.local.SessionManager
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object ApiClient {


    private const val DEFAULT_BASE_URL = "http://192.168.68.159:8000/api/v1/"
    private var currentBaseUrl: String = DEFAULT_BASE_URL

    private var retrofit: Retrofit? = null
    private var apiService: ApiService? = null

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
        return apiService ?: synchronized(this) {
            apiService ?: buildRetrofit(context.applicationContext, onUnauthorized).create(ApiService::class.java).also {
                apiService = it
            }
        }
    }

    private fun buildRetrofit(context: Context, onUnauthorized: (() -> Unit)?): Retrofit {
        val sessionManager = SessionManager.getInstance(context)

        val loggingInterceptor = HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.BODY
        }

        val authInterceptor = AuthInterceptor(sessionManager, onUnauthorized)

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
