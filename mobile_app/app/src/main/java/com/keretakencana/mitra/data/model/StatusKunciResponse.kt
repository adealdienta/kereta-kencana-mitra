package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class StatusKunciResponse(
    @SerializedName("terkunci")
    val terkunci: Boolean,

    @SerializedName("pesanan")
    val pesanan: TransaksiSingkat? = null
)

data class TransaksiSingkat(
    @SerializedName("id")
    val id: Long,

    @SerializedName("kode_transaksi")
    val kodeTransaksi: String
)
