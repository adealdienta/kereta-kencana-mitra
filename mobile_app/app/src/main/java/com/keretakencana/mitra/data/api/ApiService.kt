package com.keretakencana.mitra.data.api

import com.keretakencana.mitra.data.model.ApiResponse
import com.keretakencana.mitra.data.model.AuthResponse
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.data.model.Pengaturan
import com.keretakencana.mitra.data.model.PesananListResponse
import com.keretakencana.mitra.data.model.StatusKunciResponse
import com.keretakencana.mitra.data.model.Transaksi
import com.keretakencana.mitra.data.model.User
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.Multipart
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Part
import retrofit2.http.Path
import retrofit2.http.Query

interface ApiService {

    // ==========================================
    // 1. ENDPOINT PUBLIK
    // ==========================================

    @GET("ping")
    suspend fun ping(): Response<ApiResponse<Map<String, Any>>>

    @POST("register")
    suspend fun register(
        @Body payload: Map<String, @JvmSuppressWildcards Any?>
    ): Response<ApiResponse<AuthResponse>>

    @POST("login")
    suspend fun login(
        @Body payload: Map<String, @JvmSuppressWildcards Any?>
    ): Response<ApiResponse<AuthResponse>>

    @POST("forgot-password")
    suspend fun forgotPassword(
        @Body payload: Map<String, @JvmSuppressWildcards Any?>
    ): Response<ApiResponse<Map<String, Any>>>

    @POST("verify-reset-token")
    suspend fun verifyResetToken(
        @Body payload: Map<String, @JvmSuppressWildcards Any?>
    ): Response<ApiResponse<Map<String, Any>>>

    @POST("reset-password")
    suspend fun resetPassword(
        @Body payload: Map<String, @JvmSuppressWildcards Any?>
    ): Response<ApiResponse<Map<String, Any>>>

    // ==========================================
    // 2. ENDPOINT TERAUTENTIKASI (SANCTUM)
    // ==========================================

    @POST("logout")
    suspend fun logout(): Response<ApiResponse<Any?>>

    @GET("profile")
    suspend fun getProfile(): Response<ApiResponse<User>>

    @PUT("profile")
    suspend fun updateProfile(
        @Body payload: Map<String, @JvmSuppressWildcards Any?>
    ): Response<ApiResponse<User>>

    @Multipart
    @POST("profile/foto")
    suspend fun uploadFotoProfil(
        @Part foto: MultipartBody.Part
    ): Response<ApiResponse<User>>

    @GET("pengaturan")
    suspend fun getPengaturan(): Response<ApiResponse<Pengaturan>>

    @GET("barang")
    suspend fun getBarang(
        @Query("kategori") kategori: String? = null
    ): Response<ApiResponse<List<Barang>>>

    @GET("barang/{id}")
    suspend fun getBarangDetail(
        @Path("id") id: Long
    ): Response<ApiResponse<Barang>>

    @GET("pesanan")
    suspend fun getPesanan(
        @Query("status") status: String? = null,
        @Query("page") page: Int? = null
    ): Response<ApiResponse<PesananListResponse>>

    @GET("pesanan/{id}")
    suspend fun getPesananDetail(
        @Path("id") id: Long
    ): Response<ApiResponse<Transaksi>>

    @GET("pesanan/status-kunci")
    suspend fun getStatusKunci(): Response<ApiResponse<StatusKunciResponse>>

    @POST("pesanan")
    suspend fun createPesanan(
        @Header("Idempotency-Key") idempotencyKey: String?,
        @Body payload: Map<String, @JvmSuppressWildcards Any?>
    ): Response<ApiResponse<Transaksi>>

    @POST("pesanan/{id}/batal")
    suspend fun batalkanPesanan(
        @Path("id") id: Long,
        @Body payload: Map<String, @JvmSuppressWildcards Any?> = emptyMap()
    ): Response<ApiResponse<Transaksi>>

    @Multipart
    @POST("pesanan/{id}/konfirmasi-terima")
    suspend fun konfirmasiTerima(
        @Path("id") id: Long,
        @Part fotoBukti: MultipartBody.Part,
        @Part("catatan_penerima") catatanPenerima: RequestBody? = null
    ): Response<ApiResponse<Transaksi>>

    @POST("pesanan/{id}/simulasi-qris")
    suspend fun simulasiBayarQris(
        @Path("id") id: Long
    ): Response<ApiResponse<Transaksi>>
}
