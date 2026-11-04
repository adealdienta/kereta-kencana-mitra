package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class Pengaturan(
    @SerializedName("wa_pabrik")
    val waPabrik: String?,

    @SerializedName("nppbkc")
    val nppbkc: String?,

    @SerializedName("nama_pabrik")
    val namaPabrik: String?,

    @SerializedName("alamat_pabrik")
    val alamatPabrik: String?,

    @SerializedName("bank_nama")
    val bankNama: String? = null,

    @SerializedName("bank_rekening")
    val bankRekening: String? = null,

    @SerializedName("bank_atas_nama")
    val bankAtasNama: String? = null
)
