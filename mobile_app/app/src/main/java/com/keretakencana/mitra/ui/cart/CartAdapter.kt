package com.keretakencana.mitra.ui.cart

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.model.CartItem
import com.keretakencana.mitra.databinding.ItemCartRowBinding
import com.keretakencana.mitra.utils.CurrencyHelper

class CartAdapter(
    private var items: MutableList<CartItem>,
    private val onCartChanged: () -> Unit,
    private val onRequestRemove: (CartItem) -> Unit
) : RecyclerView.Adapter<CartAdapter.ViewHolder>() {

    inner class ViewHolder(val binding: ItemCartRowBinding) :
        RecyclerView.ViewHolder(binding.root) {

        fun bind(item: CartItem) {
            val context = binding.root.context
            val barang = item.barang

            binding.tvCartItemName.text = barang.nama
            binding.tvCartItemPriceRow.text =
                "${item.jumlah} ${item.satuan.lowercase()} • ${CurrencyHelper.formatRupiah(item.subtotal)}"

            val isiDeskripsi = if (item.satuan.equals("Bal", ignoreCase = true)) {
                "Isi: ${barang.slopPerBal} slop (${barang.slopPerBal * barang.bungkusPerSlop} bks) / bal"
            } else {
                "Isi: ${barang.bungkusPerSlop} bungkus / slop"
            }
            binding.tvCartItemContentDesc.text = isiDeskripsi
            binding.tvCartQty.text = item.jumlah.toString()

            // Muat Gambar Produk
            val localRes = when {
                barang.kodeBarang.contains("KK", ignoreCase = true) -> R.drawable.produk_kereta_kencana_12
                barang.kodeBarang.contains("SC", ignoreCase = true) -> R.drawable.produk_sembada_cethe_12
                barang.kodeBarang.contains("SM", ignoreCase = true) -> R.drawable.produk_sembada_12
                else -> R.drawable.logo_resmi
            }

            if (!barang.imageUrl.isNullOrBlank()) {
                Glide.with(context)
                    .load(barang.imageUrl)
                    .placeholder(localRes)
                    .error(localRes)
                    .into(binding.ivCartItemImage)
            } else {
                binding.ivCartItemImage.setImageResource(localRes)
            }

            // Tombol Plus (+)
            binding.btnCartPlus.setOnClickListener {
                CartManager.getInstance(context).updateQty(barang.id, item.satuan, item.jumlah + 1)
                item.jumlah++
                notifyItemChanged(adapterPosition)
                onCartChanged()
            }

            // Tombol Minus (-)
            binding.btnCartMinus.setOnClickListener {
                if (item.jumlah > item.minOrder) {
                    CartManager.getInstance(context).updateQty(barang.id, item.satuan, item.jumlah - 1)
                    item.jumlah--
                    notifyItemChanged(adapterPosition)
                    onCartChanged()
                } else {
                    onRequestRemove(item)
                }
            }

            // Tombol Hapus (Tong Sampah)
            binding.btnCartDelete.setOnClickListener {
                onRequestRemove(item)
            }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemCartRowBinding.inflate(
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

    fun updateData(newItems: List<CartItem>) {
        items.clear()
        items.addAll(newItems)
        notifyDataSetChanged()
    }
}
