package com.keretakencana.mitra.data.model

data class NotifikasiItem(
    val id: String,
    val judul: String,
    val pesan: String,
    val waktu: String,
    val kategori: String = "pesanan",
    val transaksiId: Long? = null
)
