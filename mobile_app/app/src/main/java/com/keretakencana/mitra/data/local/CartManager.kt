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
        if (barang.stok <= 0) return false to "Stok ${barang.nama} saat ini sedang habis."

        val items = getItems()
        val totalCurrentSlop = items.sumOf { it.jumlah }
        if (totalCurrentSlop + qtyToAdd > Barang.MAX_SLOP_PER_ORDER) {
            return false to "Maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop gabungan per pesanan online. Untuk pembelian Bal (20+ Slop), silakan hubungi Admin via WhatsApp."
        }

        val existingIndex = items.indexOfFirst {
            it.barang.id == barang.id && it.satuan.equals("Slop", ignoreCase = true)
        }
        val currentForBarang = if (existingIndex >= 0) items[existingIndex].jumlah else 0
        val targetForBarang = currentForBarang + qtyToAdd
        val slopPerBal = if (barang.slopPerBal > 0) barang.slopPerBal else 20
        val balNeeded = kotlin.math.ceil(targetForBarang.toDouble() / slopPerBal).toInt()
        if (balNeeded > barang.stok) {
            return false to "Stok gudang ${barang.nama} tidak mencukupi (Tersedia perkiraan ${barang.perkiraanSlopTersedia} Slop)."
        }

        if (existingIndex >= 0) {
            items[existingIndex].jumlah = targetForBarang
        } else {
            val minOrder = if (barang.minOrderSlop > 0) barang.minOrderSlop else 1
            val actualQty = if (targetForBarang < minOrder) minOrder else targetForBarang
            if (totalCurrentSlop + actualQty > Barang.MAX_SLOP_PER_ORDER) {
                return false to "Maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop gabungan per pesanan online."
            }
            items.add(CartItem(barang = barang, satuan = "Slop", jumlah = actualQty))
        }

        saveItems(items)
        return true to null
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

        val otherSlops = items.filterIndexed { idx, _ -> idx != index }.sumOf { it.jumlah }
        if (otherSlops + newQty > Barang.MAX_SLOP_PER_ORDER) {
            return false to "Maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop gabungan per pesanan online. Untuk pembelian Bal, hubungi Admin via WhatsApp."
        }

        val slopPerBal = if (barang.slopPerBal > 0) barang.slopPerBal else 20
        val balNeeded = kotlin.math.ceil(newQty.toDouble() / slopPerBal).toInt()
        if (balNeeded > barang.stok) {
            return false to "Stok gudang ${barang.nama} tidak mencukupi."
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
            if (qty <= 0 || barang.stok <= 0) continue
            val remaining = Barang.MAX_SLOP_PER_ORDER - runningSlops
            if (remaining <= 0) break
            val capped = minOf(qty, remaining, barang.perkiraanSlopTersedia.coerceAtLeast(0))
            if (capped > 0) {
                list.add(CartItem(barang = barang, satuan = "Slop", jumlah = capped))
                runningSlops += capped
            }
        }
        saveItems(list)
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
