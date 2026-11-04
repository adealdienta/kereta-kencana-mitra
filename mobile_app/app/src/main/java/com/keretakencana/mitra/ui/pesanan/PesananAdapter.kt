package com.keretakencana.mitra.ui.pesanan

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.model.Transaksi
import com.keretakencana.mitra.databinding.ItemPesananBinding
import com.keretakencana.mitra.utils.CurrencyHelper
import com.keretakencana.mitra.utils.DateHelper

class PesananAdapter(
    private var items: List<Transaksi>,
    private val onItemClick: (Transaksi) -> Unit,
    private val onPrimaryActionClick: (Transaksi) -> Unit,
    private val onSecondaryActionClick: (Transaksi) -> Unit
) : RecyclerView.Adapter<PesananAdapter.ViewHolder>() {

    inner class ViewHolder(val binding: ItemPesananBinding) :
        RecyclerView.ViewHolder(binding.root) {

        fun bind(item: Transaksi) {
            val context = binding.root.context

            binding.tvItemKodeTrx.text = item.kodeTransaksi
            binding.tvItemTanggal.text = DateHelper.formatIsoToIndonesianDateTime(item.createdAt)
            binding.tvItemTotalHarga.text = CurrencyHelper.formatRupiah(item.totalHarga)

            val isQris = item.metodePembayaran?.contains("qris", ignoreCase = true) == true
            val isLunas = item.statusPembayaran.equals("Lunas", ignoreCase = true)
            binding.tvItemLabelTotal.text = when {
                isQris && isLunas -> "QRIS (Lunas)"
                isQris -> "Total QRIS"
                isLunas -> "COD (Lunas)"
                else -> "Total COD"
            }

            // Setup Badge Status
            when (item.status) {
                "Dikirim" -> {
                    binding.tvItemStatusBadge.text = "Dikirim"
                    binding.tvItemStatusBadge.setTextColor(ContextCompat.getColor(context, R.color.brand_brown_dark))
                    binding.tvItemStatusBadge.setBackgroundResource(R.drawable.bg_rounded_card_selected)
                }
                "Selesai" -> {
                    binding.tvItemStatusBadge.text = "Selesai"
                    binding.tvItemStatusBadge.setTextColor(ContextCompat.getColor(context, R.color.status_green))
                    binding.tvItemStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
                }
                "Dibatalkan" -> {
                    binding.tvItemStatusBadge.text = "Dibatalkan"
                    binding.tvItemStatusBadge.setTextColor(ContextCompat.getColor(context, R.color.status_red))
                    binding.tvItemStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
                }
                "Diproses" -> {
                    binding.tvItemStatusBadge.text = "Diproses"
                    binding.tvItemStatusBadge.setTextColor(ContextCompat.getColor(context, R.color.brand_brown_dark))
                    binding.tvItemStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
                }
                else -> { // 'Baru Masuk'
                    binding.tvItemStatusBadge.text = "Baru Masuk"
                    binding.tvItemStatusBadge.setTextColor(ContextCompat.getColor(context, R.color.brand_brown_dark))
                    binding.tvItemStatusBadge.setBackgroundResource(R.drawable.bg_chip_gray)
                }
            }

            // Ringkasan Muatan Produk
            val firstDetail = item.items?.firstOrNull()
            val totalItemsCount = item.items?.size ?: 0

            if (firstDetail != null) {
                val productName = firstDetail.namaBarang
                binding.tvItemRingkasanProduk.text = if (totalItemsCount > 1) {
                    "$productName + ${totalItemsCount - 1} produk lainnya"
                } else {
                    productName
                }

                binding.tvItemMuatanDetail.text = "${firstDetail.jumlah} ${firstDetail.satuan.lowercase()} • ${item.alamat}"

                val localRes = when {
                    firstDetail.kodeBarang.contains("KK", ignoreCase = true) -> R.drawable.produk_kereta_kencana_12
                    firstDetail.kodeBarang.contains("SC", ignoreCase = true) -> R.drawable.produk_sembada_cethe_12
                    firstDetail.kodeBarang.contains("SM", ignoreCase = true) -> R.drawable.produk_sembada_12
                    else -> R.drawable.logo_resmi
                }

                if (!firstDetail.imageUrl.isNullOrBlank()) {
                    Glide.with(context)
                        .load(firstDetail.imageUrl)
                        .placeholder(localRes)
                        .error(localRes)
                        .into(binding.ivItemProduk)
                } else {
                    binding.ivItemProduk.setImageResource(localRes)
                }
            } else {
                binding.tvItemRingkasanProduk.text = "Pesanan Grosir Kretek"
                binding.tvItemMuatanDetail.text = item.alamat
                binding.ivItemProduk.setImageResource(R.drawable.logo_resmi)
            }

            // Tombol Aksi Bawah Dinamis
            when (item.status) {
                "Dikirim" -> {
                    binding.btnItemPrimary.visibility = View.VISIBLE
                    binding.btnItemPrimary.text = "Konfirmasi Terima"
                    binding.btnItemSecondary.text = "Lacak"
                }
                "Selesai" -> {
                    binding.btnItemPrimary.visibility = View.VISIBLE
                    binding.btnItemPrimary.text = "Lihat Faktur"
                    binding.btnItemSecondary.text = "Pesan Lagi"
                }
                "Baru Masuk" -> {
                    binding.btnItemPrimary.visibility = View.VISIBLE
                    binding.btnItemPrimary.text = "Detail"
                    binding.btnItemSecondary.text = "Batalkan"
                }
                else -> { // Diproses / Dibatalkan
                    binding.btnItemPrimary.visibility = View.GONE
                    binding.btnItemSecondary.text = "Lihat Detail"
                }
            }

            binding.btnItemPrimary.setOnClickListener {
                onPrimaryActionClick(item)
            }

            binding.btnItemSecondary.setOnClickListener {
                onSecondaryActionClick(item)
            }

            binding.root.setOnClickListener {
                onItemClick(item)
            }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemPesananBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(items[position])
    }

    override fun getItemCount(): Int = items.size

    fun submitList(newItems: List<Transaksi>) {
        items = newItems
        notifyDataSetChanged()
    }
}
