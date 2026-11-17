package com.keretakencana.mitra.ui.beranda

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.databinding.ItemBarangKatalogBinding
import com.keretakencana.mitra.utils.CurrencyHelper

class BarangAdapter(
    private var items: List<Barang>,
    private val onItemClick: (Barang) -> Unit,
    private val onCartUpdated: () -> Unit
) : RecyclerView.Adapter<BarangAdapter.ViewHolder>() {

    inner class ViewHolder(val binding: ItemBarangKatalogBinding) :
        RecyclerView.ViewHolder(binding.root) {

        fun bind(barang: Barang) {
            val context = binding.root.context
            val cartManager = CartManager.getInstance(context)

            binding.tvBarangNama.text = barang.nama
            binding.tvBarangHarga.text = CurrencyHelper.formatRupiah(barang.hargaPerSlop)
            binding.tvBarangSatuanSub.text = if (barang.stok > 0) {
                "per slop (${barang.bungkusPerSlop} bks)"
            } else {
                "Stok Gudang Habis • Hubungi Admin"
            }

            // Muat Gambar Produk (Glide dengan Fallback Aset Lokal)
            val localDrawableRes = when {
                barang.kodeBarang.contains("KK", ignoreCase = true) -> R.drawable.produk_kereta_kencana_12
                barang.kodeBarang.contains("SC", ignoreCase = true) -> R.drawable.produk_sembada_cethe_12
                barang.kodeBarang.contains("SM", ignoreCase = true) -> R.drawable.produk_sembada_12
                else -> R.drawable.logo_resmi
            }

            if (!barang.imageUrl.isNullOrBlank()) {
                Glide.with(context)
                    .load(barang.imageUrl)
                    .placeholder(localDrawableRes)
                    .error(localDrawableRes)
                    .into(binding.ivBarangGambar)
            } else {
                binding.ivBarangGambar.setImageResource(localDrawableRes)
            }

            // Cek Jumlah Varian Slop di Keranjang Belanja
            val currentCartItem = cartManager.getItems().find {
                it.barang.id == barang.id && it.satuan.equals("Slop", ignoreCase = true)
            }
            val currentQty = currentCartItem?.jumlah ?: 0

            if (currentQty > 0) {
                binding.btnTambahSlop.visibility = View.GONE
                binding.layoutCounter.visibility = View.VISIBLE
                binding.tvCounterQty.text = currentQty.toString()
            } else {
                binding.btnTambahSlop.visibility = View.VISIBLE
                binding.layoutCounter.visibility = View.GONE
            }

            // Event Klik Kartu Produk -> Buka Detail Produk (S3)
            binding.layoutItemRoot.setOnClickListener {
                onItemClick(barang)
            }

            // Event Tombol Tambah
            binding.btnTambahSlop.setOnClickListener {
                val qty = if (barang.minOrderSlop > 0) barang.minOrderSlop else 1
                val (ok, msg) = cartManager.tryAddSlop(barang, qty)
                if (ok) {
                    notifyItemChanged(adapterPosition)
                    onCartUpdated()
                } else if (!msg.isNullOrBlank()) {
                    Toast.makeText(context, msg, Toast.LENGTH_LONG).show()
                }
            }

            // Event Tombol Plus (+)
            binding.btnPlusQty.setOnClickListener {
                val (ok, msg) = cartManager.tryUpdateSlopQty(barang, currentQty + 1)
                if (ok) {
                    notifyItemChanged(adapterPosition)
                    onCartUpdated()
                } else if (!msg.isNullOrBlank()) {
                    Toast.makeText(context, msg, Toast.LENGTH_LONG).show()
                }
            }

            // Event Tombol Minus (-)
            binding.btnMinusQty.setOnClickListener {
                val newQty = currentQty - 1
                cartManager.tryUpdateSlopQty(barang, newQty)
                notifyItemChanged(adapterPosition)
                onCartUpdated()
            }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemBarangKatalogBinding.inflate(
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

    fun submitList(newItems: List<Barang>) {
        items = newItems
        notifyDataSetChanged()
    }
}
