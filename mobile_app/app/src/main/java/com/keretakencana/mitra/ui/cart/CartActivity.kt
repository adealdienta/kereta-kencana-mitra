package com.keretakencana.mitra.ui.cart

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.CartItem
import com.keretakencana.mitra.databinding.ActivityCartBinding
import com.keretakencana.mitra.ui.auth.LoginBottomSheetFragment
import com.keretakencana.mitra.utils.CurrencyHelper
import kotlinx.coroutines.launch

class CartActivity : AppCompatActivity() {

    private lateinit var binding: ActivityCartBinding
    private lateinit var cartAdapter: CartAdapter

    private var isOrderLocked = false
    private var lockedOrderCode = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCartBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupRecyclerView()
        setupListeners()
    }

    override fun onResume() {
        super.onResume()
        refreshCartView()
        checkUserSessionAndOrderLock()
    }

    private fun setupRecyclerView() {
        val cartManager = CartManager.getInstance(this)
        val items = cartManager.getItems()

        cartAdapter = CartAdapter(
            items = items,
            onCartChanged = {
                refreshCartView()
            },
            onRequestRemove = { itemToRemove ->
                showDeleteConfirmationDialog(itemToRemove)
            }
        )

        binding.rvCartItems.layoutManager = LinearLayoutManager(this)
        binding.rvCartItems.adapter = cartAdapter
    }

    private fun showDeleteConfirmationDialog(item: CartItem) {
        AlertDialog.Builder(this)
            .setTitle("Hapus Item")
            .setMessage("Hapus ${item.barang.nama} (${item.satuan}) dari keranjang?")
            .setPositiveButton("Hapus") { _, _ ->
                CartManager.getInstance(this).removeItem(item.barang.id, item.satuan)
                refreshCartView()
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun refreshCartView() {
        val cartManager = CartManager.getInstance(this)
        val items = cartManager.getItems()

        if (items.isEmpty()) {
            binding.layoutEmptyCart.visibility = View.VISIBLE
            binding.rvCartItems.visibility = View.GONE
            binding.layoutCatatanKurir.visibility = View.GONE
            binding.cardRingkasanBiaya.visibility = View.GONE
            binding.layoutBottomCheckoutBar.visibility = View.GONE
        } else {
            binding.layoutEmptyCart.visibility = View.GONE
            binding.rvCartItems.visibility = View.VISIBLE
            binding.layoutCatatanKurir.visibility = View.VISIBLE
            binding.cardRingkasanBiaya.visibility = View.VISIBLE
            binding.layoutBottomCheckoutBar.visibility = View.VISIBLE

            cartAdapter.updateData(items)

            var totalQtySlopEquivalent = 0
            var subtotal = 0.0

            for (it in items) {
                if (it.satuan.equals("Slop", ignoreCase = true)) {
                    totalQtySlopEquivalent += it.jumlah
                } else {
                    totalQtySlopEquivalent += (it.jumlah * it.barang.slopPerBal)
                }
                subtotal += it.subtotal
            }

            binding.tvRingkasanSubtotalLabel.text = "Subtotal Produk ($totalQtySlopEquivalent slop)"
            binding.tvRingkasanSubtotalValue.text = CurrencyHelper.formatRupiah(subtotal)
            binding.tvCartBottomTotal.text = CurrencyHelper.formatRupiah(subtotal)
        }
    }

    private fun checkUserSessionAndOrderLock() {
        val session = SessionManager.getInstance(this)
        val isLoggedIn = session.isLoggedIn()

        binding.cardGuestBanner.visibility = if (isLoggedIn) View.GONE else View.VISIBLE

        if (isLoggedIn) {
            // Cek Order Lock (Aturan B4)
            lifecycleScope.launch {
                try {
                    val api = ApiClient.getService(this@CartActivity)
                    val response = api.getStatusKunci()

                    if (response.isSuccessful && response.body()?.success == true) {
                        val lockData = response.body()?.data
                        if (lockData?.terkunci == true) {
                            isOrderLocked = true
                            lockedOrderCode = lockData.pesanan?.kodeTransaksi ?: ""
                            binding.cardOrderLockBanner.visibility = View.VISIBLE
                            binding.tvOrderLockDetail.text =
                                "Pesanan #$lockedOrderCode saat ini dalam status Dikirim. Konfirmasi penerimaan terlebih dahulu sebelum membuat pesanan baru."
                            binding.btnProceedToCheckout.isEnabled = false
                            binding.btnProceedToCheckout.alpha = 0.5f
                        } else {
                            isOrderLocked = false
                            binding.cardOrderLockBanner.visibility = View.GONE
                            binding.btnProceedToCheckout.isEnabled = true
                            binding.btnProceedToCheckout.alpha = 1.0f
                        }
                    }
                } catch (_: Exception) {
                    // Jaringan offline atau fallback
                }
            }
        } else {
            binding.cardOrderLockBanner.visibility = View.GONE
            binding.btnProceedToCheckout.isEnabled = true
            binding.btnProceedToCheckout.alpha = 1.0f
        }
    }

    private fun setupListeners() {
        binding.btnBackCart.setOnClickListener { finish() }

        binding.tvBtnGuestLogin.setOnClickListener {
            showLoginSheet()
        }

        binding.btnGoToCatalog.setOnClickListener { finish() }

        binding.btnProceedToCheckout.setOnClickListener {
            val session = SessionManager.getInstance(this)
            if (!session.isLoggedIn()) {
                showLoginSheet()
                return@setOnClickListener
            }

            if (isOrderLocked) {
                Toast.makeText(
                    this,
                    "Pesanan baru ditolak karena pesanan #$lockedOrderCode masih berjalan.",
                    Toast.LENGTH_LONG
                ).show()
                return@setOnClickListener
            }

            val catatan = binding.etCatatanKurir.text.toString().trim()
            val intent = Intent(this, CheckoutActivity::class.java).apply {
                putExtra(CheckoutActivity.EXTRA_CATATAN, catatan)
            }
            startActivity(intent)
        }
    }

    private fun showLoginSheet() {
        val sheet = LoginBottomSheetFragment.newInstance()
        sheet.onLoginSuccessCallback = {
            checkUserSessionAndOrderLock()
        }
        sheet.show(supportFragmentManager, LoginBottomSheetFragment.TAG)
    }
}
