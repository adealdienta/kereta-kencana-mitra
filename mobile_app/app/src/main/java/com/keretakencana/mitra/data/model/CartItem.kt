package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

/**
 * Item dalam keranjang belanja.
 * Mengikuti Aturan Bisnis B1: Multi-satuan independen.
 * Kunci pembeda baris keranjang adalah kombinasi barang_id dan satuan (Slop vs Bal).
 */
data class CartItem(
    @SerializedName("barang")
    val barang: Barang,

    @SerializedName("satuan")
    val satuan: String, // "Slop" atau "Bal"

    @SerializedName("jumlah")
    var jumlah: Int
) {
    val uniqueKey: String
        get() = "${barang.id}_$satuan"

    val hargaSatuan: Double
        get() = if (satuan.equals("Bal", ignoreCase = true)) barang.hargaPerBal else barang.hargaPerSlop

    val subtotal: Double
        get() = jumlah * hargaSatuan

    val minOrder: Int
        get() = if (satuan.equals("Bal", ignoreCase = true)) barang.minOrderBal else barang.minOrderSlop
}
