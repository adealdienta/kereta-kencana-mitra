package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class Transaksi(
    @SerializedName("id")
    val id: Long,

    @SerializedName("kode_transaksi")
    val kodeTransaksi: String,

    @SerializedName("user_id")
    val userId: Long,

    @SerializedName("status")
    val status: String, // 'Baru Masuk', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'

    @SerializedName("nama_mitra")
    val namaMitra: String,

    @SerializedName("telepon")
    val telepon: String,

    @SerializedName("alamat")
    val alamat: String,

    @SerializedName("total_harga")
    val totalHarga: Double,

    @SerializedName("formatted_total")
    val formattedTotal: String?,

    @SerializedName("catatan")
    val catatan: String? = null,

    @SerializedName("catatan_penerima")
    val catatanPenerima: String? = null,

    @SerializedName("nomor_do")
    val nomorDo: String? = null,

    @SerializedName("nomor_resi")
    val nomorResi: String? = null,

    @SerializedName("bukti_penerimaan_url")
    val buktiPenerimaanUrl: String? = null,

    @SerializedName("bukti_transfer_url")
    val buktiTransferUrl: String? = null,

    @SerializedName("ditransfer_pada")
    val ditransferPada: String? = null,

    @SerializedName("boleh_batal")
    val bolehBatal: Boolean = false,

    @SerializedName("boleh_konfirmasi")
    val bolehKonfirmasi: Boolean = false,

    @SerializedName("metode_pembayaran")
    val metodePembayaran: String? = null,

    @SerializedName("status_pembayaran")
    val statusPembayaran: String? = null,

    @SerializedName("created_at")
    val createdAt: String? = null,

    @SerializedName("diterima_pada")
    val diterimaPada: String? = null,

    @SerializedName("items")
    val items: List<TransaksiDetail>? = null
)
