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
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.data.model.CartItem
import com.keretakencana.mitra.databinding.ActivityCartBinding
import com.keretakencana.mitra.ui.auth.LoginBottomSheetFragment
import com.keretakencana.mitra.utils.CurrencyHelper
import kotlinx.coroutines.launch

class CartActivity : AppCompatActivity() {

    private lateinit var binding: ActivityCartBinding
    private lateinit var cartAdapter: CartAdapter

    private var isOrderLocked = false
    private var isOrderLockChecking = false
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
        syncCartWithLiveCatalog()
        checkUserSessionAndOrderLock()
    }

    private fun syncCartWithLiveCatalog() {
        val cartManager = CartManager.getInstance(this)
        if (cartManager.getItems().isEmpty()) return

        lifecycleScope.launch {
            try {
                val res = ApiClient.getService(this@CartActivity).getBarang()
                val liveCatalog = res.body()?.data
                if (res.isSuccessful && res.body()?.success == true && liveCatalog != null) {
                    val notice = cartManager.syncWithCatalog(liveCatalog)
                    refreshCartView()
                    if (!notice.isNullOrBlank()) {
                        Toast.makeText(this@CartActivity, notice, Toast.LENGTH_LONG).show()
                    }
                }
            } catch (_: Exception) {}
        }
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

            val totalSlop = cartManager.getTotalSlopQty()
            val subtotal = cartManager.getEstimatedSubtotal()
            val tierPrice = Barang.hargaSlopByQty(totalSlop)

            binding.tvRingkasanSubtotalLabel.text = "Subtotal ($totalSlop/${Barang.MAX_SLOP_PER_ORDER} slop • ${CurrencyHelper.formatRupiah(tierPrice)}/slop)"
            binding.tvRingkasanSubtotalValue.text = CurrencyHelper.formatRupiah(subtotal)
            binding.tvCartBottomTotal.text = CurrencyHelper.formatRupiah(subtotal)

            binding.tvCartTierHint.text = when {
                totalSlop <= 5 -> {
                    val need = 6 - totalSlop
                    "💡 Tambah $need slop lagi agar tarif turun jadi Rp 65.000/slop!"
                }
                totalSlop in 6..10 -> {
                    val need = 11 - totalSlop
                    "✓ Tarif Rp 65.000/slop aktif! Tambah $need slop lagi untuk tarif Rp 60.000/slop."
                }
                else -> {
                    "✓ Tarif grosir terbaik Rp 60.000/slop aktif (maks. ${Barang.MAX_SLOP_PER_ORDER} slop/pesanan)."
                }
            }
        }
    }

    private fun checkUserSessionAndOrderLock() {
        val session = SessionManager.getInstance(this)
        val isLoggedIn = session.isLoggedIn()

        binding.cardGuestBanner.visibility = if (isLoggedIn) View.GONE else View.VISIBLE

        if (isLoggedIn) {
            // Cek Order Lock (Aturan B4)
            isOrderLockChecking = true
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
                } finally {
                    isOrderLockChecking = false
                }
            }
        } else {
            isOrderLockChecking = false
            isOrderLocked = false
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

            if (isOrderLockChecking) {
                Toast.makeText(this, "Sedang memeriksa status pesanan berjalan...", Toast.LENGTH_SHORT).show()
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
