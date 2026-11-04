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
            gson.fromJson(json, type) ?: mutableListOf()
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
     * Tambah barang ke keranjang dengan satuan dan jumlah tertentu.
     * Jika varian (barang + satuan) sudah ada, jumlah ditambahkan.
     */
    @Synchronized
    fun addItem(barang: Barang, satuan: String, qty: Int): CartItem {
        val items = getItems()
        val existingIndex = items.indexOfFirst {
            it.barang.id == barang.id && it.satuan.equals(satuan, ignoreCase = true)
        }

        val updatedItem: CartItem
        if (existingIndex >= 0) {
            val existing = items[existingIndex]
            existing.jumlah += qty
            updatedItem = existing
        } else {
            val minOrder = if (satuan.equals("Bal", ignoreCase = true)) barang.minOrderBal else barang.minOrderSlop
            val actualQty = if (qty < minOrder) minOrder else qty
            updatedItem = CartItem(barang = barang, satuan = satuan, jumlah = actualQty)
            items.add(updatedItem)
        }

        saveItems(items)
        return updatedItem
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
            } else {
                items[index].jumlah = newQty
            }
            saveItems(items)
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

    fun getEstimatedSubtotal(): Double {
        return getItems().sumOf { it.subtotal }
    }
}
