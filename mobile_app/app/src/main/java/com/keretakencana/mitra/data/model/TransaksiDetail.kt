package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class TransaksiDetail(
    @SerializedName("id")
    val id: Long,

    @SerializedName("barang_id")
    val barangId: Long,

    @SerializedName("nama_barang")
    val namaBarang: String,

    @SerializedName("kode_barang")
    val kodeBarang: String,

    @SerializedName("satuan")
    val satuan: String, // 'Slop' atau 'Bal'

    @SerializedName("jumlah")
    val jumlah: Int,

    @SerializedName("harga_satuan")
    val hargaSatuan: Double,

    @SerializedName("subtotal")
    val subtotal: Double,

    @SerializedName("image_url")
    val imageUrl: String? = null
)
