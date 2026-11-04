package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class Kategori(
    @SerializedName("id")
    val id: Long,

    @SerializedName("nama_kategori")
    val namaKategori: String,

    @SerializedName("keterangan")
    val keterangan: String? = null
)
