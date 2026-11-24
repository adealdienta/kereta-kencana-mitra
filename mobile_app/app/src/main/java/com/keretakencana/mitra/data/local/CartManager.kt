package com.keretakencana.mitra.data.local

import android.content.Context
import android.content.SharedPreferences
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.data.model.CartItem

/**
 * Manajer Keranjang Belanja Mitra (Lokal).
 * Mendukung Aturan Bisnis B1: Multi-satuan independen (Slop dan Bal).
 */
class CartManager(context: Context) {

    private val prefs: SharedPreferences = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE)
    private val gson = Gson()

    companion object {
        private const val PREF_NAME = "kereta_kencana_cart"
        private const val KEY_CART_ITEMS = "key_cart_items"

        @Volatile
        private var instance: CartManager? = null

        fun getInstance(context: Context): CartManager {
            return instance ?: synchronized(this) {
                instance ?: CartManager(context.applicationContext).also { instance = it }
            }
        }
    }

    @Synchronized
    fun getItems(): MutableList<CartItem> {
        val json = prefs.getString(KEY_CART_ITEMS, null) ?: return mutableListOf()
        val type = object : TypeToken<MutableList<CartItem>>() {}.type
        return try {
            val parsed: MutableList<CartItem> = gson.fromJson(json, type) ?: mutableListOf()
            // Pastikan keranjang online hanya menyimpan satuan Slop sesuai aturan Web
            parsed.filter { it.satuan.equals("Slop", ignoreCase = true) && it.jumlah > 0 }.toMutableList()
        } catch (e: Exception) {
            mutableListOf()
        }
    }

    @Synchronized
    private fun saveItems(items: List<CartItem>) {
        val json = gson.toJson(items)
        prefs.edit().putString(KEY_CART_ITEMS, json).apply()
    }

    /**
     * Validasi dan tambah barang satuan Slop ke keranjang (maksimal 19 Slop gabungan seluruh varian).
     */
    @Synchronized
    fun tryAddSlop(barang: Barang, qtyToAdd: Int): Pair<Boolean, String?> {
        if (qtyToAdd <= 0) return false to "Jumlah minimal 1 Slop."
        if (!barang.hasAvailableStock) return false to "Stok ${barang.nama} saat ini sedang habis."

        val items = getItems()
        val totalCurrentSlop = items.sumOf { it.jumlah }
        if (totalCurrentSlop + qtyToAdd > Barang.MAX_SLOP_PER_ORDER) {
            return false to "Maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop gabungan per pesanan online. Untuk pembelian Bal (20+ Slop), silakan hubungi Admin via WhatsApp."
        }

        val existingIndex = items.indexOfFirst {
            it.barang.id == barang.id && it.satuan.equals("Slop", ignoreCase = true)
        }
        val currentForBarang = if (existingIndex >= 0) items[existingIndex].jumlah else 0
        val minOrder = if (barang.minOrderSlop > 0) barang.minOrderSlop else 1
        val targetForBarang = if (existingIndex >= 0) currentForBarang + qtyToAdd else maxOf(qtyToAdd, minOrder)

        if (otherSlopTotal(items, existingIndex) + targetForBarang > Barang.MAX_SLOP_PER_ORDER) {
            return false to "Maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop gabungan per pesanan online."
        }
        if (targetForBarang > barang.stokTersediaSlop) {
            return false to "Stok gudang ${barang.nama} tidak mencukupi (Tersedia ${barang.stokTersediaSlop} Slop)."
        }

        if (existingIndex >= 0) {
            items[existingIndex].jumlah = targetForBarang
        } else {
            items.add(CartItem(barang = barang, satuan = "Slop", jumlah = targetForBarang))
        }

        saveItems(items)
        return true to null
    }

    private fun otherSlopTotal(items: List<CartItem>, excludeIndex: Int): Int {
        return items.filterIndexed { idx, _ -> idx != excludeIndex }.sumOf { it.jumlah }
    }

    /**
     * Validasi dan ubah jumlah satuan Slop di keranjang.
     */
    @Synchronized
    fun tryUpdateSlopQty(barang: Barang, newQty: Int): Pair<Boolean, String?> {
        val items = getItems()
        val index = items.indexOfFirst {
            it.barang.id == barang.id && it.satuan.equals("Slop", ignoreCase = true)
        }

        if (newQty <= 0) {
            if (index >= 0) {
                items.removeAt(index)
                saveItems(items)
            }
            return true to null
        }

        val otherSlops = otherSlopTotal(items, index)
        if (otherSlops + newQty > Barang.MAX_SLOP_PER_ORDER) {
            return false to "Maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop gabungan per pesanan online. Untuk pembelian Bal, hubungi Admin via WhatsApp."
        }

        if (!barang.hasAvailableStock || newQty > barang.stokTersediaSlop) {
            return false to "Stok gudang ${barang.nama} tidak mencukupi (Tersedia ${barang.stokTersediaSlop} Slop)."
        }

        if (index >= 0) {
            items[index].jumlah = newQty
        } else {
            items.add(CartItem(barang = barang, satuan = "Slop", jumlah = newQty))
        }
        saveItems(items)
        return true to null
    }

    /**
     * Tambah barang ke keranjang dengan validasi Slop maksimal 19.
     */
    @Synchronized
    fun addItem(barang: Barang, satuan: String, qty: Int): CartItem? {
        val (ok, _) = tryAddSlop(barang, qty)
        if (!ok) return null
        return getItems().find { it.barang.id == barang.id && it.satuan.equals("Slop", ignoreCase = true) }
    }

    /**
     * Ubah jumlah produk pada baris keranjang.
     * Jika qty <= 0, item dihapus dari keranjang.
     */
    @Synchronized
    fun updateQty(barangId: Long, satuan: String, newQty: Int) {
        val items = getItems()
        val index = items.indexOfFirst {
            it.barang.id == barangId && it.satuan.equals(satuan, ignoreCase = true)
        }

        if (index >= 0) {
            if (newQty <= 0) {
                items.removeAt(index)
                saveItems(items)
            } else {
                tryUpdateSlopQty(items[index].barang, newQty)
            }
        }
    }

    @Synchronized
    fun replaceWithSlopItems(newItems: List<Pair<Barang, Int>>) {
        val list = mutableListOf<CartItem>()
        var runningSlops = 0
        for ((barang, qty) in newItems) {
            if (qty <= 0 || !barang.hasAvailableStock) continue
            val remaining = Barang.MAX_SLOP_PER_ORDER - runningSlops
            if (remaining <= 0) break
            val capped = minOf(qty, remaining, barang.stokTersediaSlop)
            if (capped > 0) {
                list.add(CartItem(barang = barang, satuan = "Slop", jumlah = capped))
                runningSlops += capped
            }
        }
        saveItems(list)
    }

    /**
     * Sinkronkan isi keranjang lokal terhadap data katalog & stok terbaru dari API.
     * Mengembalikan pesan peringatan bila ada produk yang dihapus/disesuaikan karena stok atau status.
     */
    @Synchronized
    fun syncWithCatalog(liveCatalog: List<Barang>): String? {
        val currentItems = getItems()
        if (currentItems.isEmpty()) return null

        val catalogMap = liveCatalog.associateBy { it.id }
        val updatedItems = mutableListOf<CartItem>()
        val notices = mutableListOf<String>()
        var runningTotalSlop = 0

        for (item in currentItems) {
            val liveBarang = catalogMap[item.barang.id]
            if (liveBarang == null || !liveBarang.hasAvailableStock) {
                notices.add("${item.barang.nama} dihapus (stok habis/tidak aktif)")
                continue
            }

            val remainingOrderCap = (Barang.MAX_SLOP_PER_ORDER - runningTotalSlop).coerceAtLeast(0)
            val stockCap = liveBarang.stokTersediaSlop
            val allowedQty = minOf(item.jumlah, remainingOrderCap, stockCap)
            if (allowedQty <= 0) {
                notices.add("${liveBarang.nama} dihapus (melebihi kuota/stok)")
                continue
            }
            if (allowedQty < item.jumlah) {
                notices.add("${liveBarang.nama} disesuaikan ke $allowedQty slop")
            }

            updatedItems.add(CartItem(barang = liveBarang, satuan = "Slop", jumlah = allowedQty))
            runningTotalSlop += allowedQty
        }

        saveItems(updatedItems)
        return if (notices.isNotEmpty()) {
            "Keranjang diperbarui: ${notices.joinToString(", ")}."
        } else {
            null
        }
    }

    @Synchronized
    fun removeItem(barangId: Long, satuan: String) {
        val items = getItems()
        val removed = items.removeAll {
            it.barang.id == barangId && it.satuan.equals(satuan, ignoreCase = true)
        }
        if (removed) {
            saveItems(items)
        }
    }

    @Synchronized
    fun clearCart() {
        prefs.edit().remove(KEY_CART_ITEMS).apply()
    }

    fun getTotalQuantity(): Int {
        return getItems().sumOf { it.jumlah }
    }

    fun getTotalSlopQty(): Int {
        return getItems().sumOf { it.jumlah }
    }

    fun getTieredUnitPrice(): Double {
        return Barang.hargaSlopByQty(getTotalSlopQty())
    }

    fun getEstimatedSubtotal(): Double {
        val totalSlop = getTotalSlopQty()
        if (totalSlop <= 0) return 0.0
        val unitPrice = Barang.hargaSlopByQty(totalSlop)
        return getItems().sumOf { it.jumlah * unitPrice }
    }
}
