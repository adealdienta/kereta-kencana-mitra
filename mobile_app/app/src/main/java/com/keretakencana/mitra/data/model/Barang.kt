package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class Barang(
    @SerializedName("id")
    val id: Long,

    @SerializedName("kategori_id")
    val kategoriId: Long? = null,

    @SerializedName("kategori_nama")
    val kategoriNama: String? = null,

    @SerializedName("kode_barang")
    val kodeBarang: String,

    @SerializedName("nama")
    val nama: String,

    @SerializedName("slug")
    val slug: String? = null,

    @SerializedName("harga_per_slop")
    val hargaPerSlop: Double,

    @SerializedName("harga_per_bal")
    val hargaPerBal: Double,

    @SerializedName("slop_per_bal")
    val slopPerBal: Int = 20,

    @SerializedName("bungkus_per_slop")
    val bungkusPerSlop: Int = 10,

    @SerializedName("batang_per_bungkus")
    val batangPerBungkus: Int = 12,

    @SerializedName("min_order_slop")
    val minOrderSlop: Int = 1,

    @SerializedName("min_order_bal")
    val minOrderBal: Int = 1,

    @SerializedName("stok")
    val stok: Int = 0, // Stok gudang presisi dalam satuan Slop

    @SerializedName("stok_bal")
    val stokBal: Int? = null,

    @SerializedName("stok_slop_estimasi")
    val stokSlopEstimasi: Int? = null,

    @SerializedName("status_stok")
    val statusStok: String = "Ready Stock",

    @SerializedName("image_url")
    val imageUrl: String? = null,

    @SerializedName("deskripsi")
    val deskripsi: String? = null,

    @SerializedName("profil_rasa")
    val profilRasa: String? = null,

    @SerializedName("tar_mg")
    val tarMg: Double? = null,

    @SerializedName("nikotin_mg")
    val nikotinMg: Double? = null,

    @SerializedName("aktif")
    val aktif: Boolean? = true,

    @SerializedName("best_seller")
    val bestSeller: Boolean = false,

    @SerializedName("kategori")
    val kategori: Kategori? = null
) {
    val isAktif: Boolean
        get() = aktif != false

    /**
     * Jumlah stok tersedia presisi dalam satuan Slop.
     */
    val stokTersediaSlop: Int
        get() = (stokSlopEstimasi ?: stok).coerceAtLeast(0)

    val perkiraanSlopTersedia: Int
        get() = stokTersediaSlop

    val hasAvailableStock: Boolean
        get() = isAktif && stokTersediaSlop > 0 && !statusStok.equals("Habis", ignoreCase = true)

    val stokBalUtuh: Int
        get() {
            val divisor = if (slopPerBal > 0) slopPerBal else 20
            return stokBal ?: (stokTersediaSlop / divisor)
        }

    companion object {
        const val MAX_SLOP_PER_ORDER = 19

        fun hargaSlopByQty(totalSlop: Int): Double {
            return when {
                totalSlop > 10 -> 60000.0
                totalSlop >= 6 -> 65000.0
                else -> 70000.0
            }
        }
    }
}
