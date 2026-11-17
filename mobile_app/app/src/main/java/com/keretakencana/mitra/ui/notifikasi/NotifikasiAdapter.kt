package com.keretakencana.mitra.ui.notifikasi

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.keretakencana.mitra.data.model.NotifikasiItem
import com.keretakencana.mitra.databinding.ItemNotifikasiBinding

class NotifikasiAdapter(
    private var items: List<NotifikasiItem> = emptyList(),
    private val onItemClick: (NotifikasiItem) -> Unit = {}
) : RecyclerView.Adapter<NotifikasiAdapter.NotifikasiViewHolder>() {

    fun submitList(newItems: List<NotifikasiItem>) {
        items = newItems
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): NotifikasiViewHolder {
        val binding = ItemNotifikasiBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return NotifikasiViewHolder(binding)
    }

    override fun onBindViewHolder(holder: NotifikasiViewHolder, position: Int) {
        val item = items[position]
        holder.bind(item)
        holder.itemView.setOnClickListener {
            onItemClick(item)
        }
    }

    override fun getItemCount(): Int = items.size

    class NotifikasiViewHolder(
        private val binding: ItemNotifikasiBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(item: NotifikasiItem) {
            binding.tvNotifTitle.text = item.judul
            binding.tvNotifMessage.text = item.pesan
            binding.tvNotifTime.text = item.waktu
        }
    }
}
