package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class User(
    @SerializedName("id")
    val id: Long,

    @SerializedName("name")
    val name: String,

    @SerializedName("nama_toko")
    val namaToko: String?,

    @SerializedName("email")
    val email: String,

    @SerializedName("telepon")
    val telepon: String?,

    @SerializedName("alamat")
    val alamat: String?,

    @SerializedName("role")
    val role: String,

    @SerializedName("created_at")
    val createdAt: String? = null,

    @SerializedName("updated_at")
    val updatedAt: String? = null,

    @SerializedName("foto_profil_url")
    val fotoProfilUrl: String? = null
)
