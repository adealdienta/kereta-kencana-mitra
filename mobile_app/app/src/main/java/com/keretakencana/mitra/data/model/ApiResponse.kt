package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

/**
 * Standard API Response wrapper matching Laravel ApiController format:
 * {
 *   "success": true/false,
 *   "message": "...",
 *   "data": ...,
 *   "errors": { ... }
 * }
 */
data class ApiResponse<T>(
    @SerializedName("success")
    val success: Boolean,

    @SerializedName("message")
    val message: String?,

    @SerializedName("data")
    val data: T?,

    @SerializedName("errors")
    val errors: Map<String, List<String>>? = null
)
