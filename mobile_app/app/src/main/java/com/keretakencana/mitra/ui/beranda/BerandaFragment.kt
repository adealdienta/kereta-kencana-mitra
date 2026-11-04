package com.keretakencana.mitra.ui.beranda

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.gson.Gson
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.databinding.FragmentBerandaBinding
import com.keretakencana.mitra.ui.auth.LoginBottomSheetFragment
import com.keretakencana.mitra.ui.detail.DetailProdukActivity
import com.keretakencana.mitra.ui.main.MainActivity
import com.keretakencana.mitra.utils.CurrencyHelper
import com.keretakencana.mitra.utils.NetworkHelper
import kotlinx.coroutines.launch

class BerandaFragment : Fragment() {

    private var _binding: FragmentBerandaBinding? = null
    private val binding get() = _binding!!

    private lateinit var adapter: BarangAdapter
    private var productList = mutableListOf<Barang>()

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentBerandaBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupRecyclerView()
        setupListeners()
        loadCatalog()
    }

    override fun onResume() {
        super.onResume()
        updateCartSummaryBar()
        adapter.notifyDataSetChanged()
    }

    private fun setupRecyclerView() {
        adapter = BarangAdapter(
            items = productList,
            onItemClick = { barang ->
                val intent = Intent(requireContext(), DetailProdukActivity::class.java).apply {
                    putExtra(DetailProdukActivity.EXTRA_BARANG_ID, barang.id)
                    putExtra(DetailProdukActivity.EXTRA_BARANG_JSON, Gson().toJson(barang))
                }
                startActivity(intent)
            },
            onCartUpdated = {
                updateCartSummaryBar()
            }
        )

        binding.rvKatalogBarang.layoutManager = LinearLayoutManager(requireContext())
        binding.rvKatalogBarang.adapter = adapter
    }

    private fun setupListeners() {
        binding.swipeRefresh.setOnRefreshListener {
            loadCatalog()
        }

        // Klik Avatar Profil di Header -> Buka Login jika belum masuk
        binding.ivProfileHeader.setOnClickListener {
            val session = SessionManager.getInstance(requireContext())
            if (!session.isLoggedIn()) {
                val loginSheet = LoginBottomSheetFragment.newInstance()
                loginSheet.onLoginSuccessCallback = {
                    Toast.makeText(requireContext(), "Sesi aktif. Silakan memilih produk.", Toast.LENGTH_SHORT).show()
                }
                loginSheet.show(childFragmentManager, LoginBottomSheetFragment.TAG)
            } else {
                Toast.makeText(
                    requireContext(),
                    "Masuk sebagai: ${session.getUser()?.namaToko ?: session.getUser()?.name}",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        binding.ivNotification.setOnClickListener {
            Toast.makeText(requireContext(), "Semua notifikasi pengiriman pesanan Anda terpantau aman.", Toast.LENGTH_SHORT).show()
        }

        binding.btnLihatKeranjang.setOnClickListener {
            val intent = Intent(requireContext(), com.keretakencana.mitra.ui.cart.CartActivity::class.java)
            startActivity(intent)
        }
    }

    private fun loadCatalog() {
        if (!NetworkHelper.isNetworkAvailable(requireContext())) {
            binding.swipeRefresh.isRefreshing = false
            binding.progressBeranda.visibility = View.GONE
            binding.tvEmptyCatalog.visibility = View.VISIBLE
            binding.tvEmptyCatalog.text = "Koneksi internet tidak tersedia."
            return
        }

        binding.progressBeranda.visibility = View.VISIBLE
        binding.tvEmptyCatalog.visibility = View.GONE

        viewLifecycleOwner.lifecycleScope.launch {
            try {
                val api = ApiClient.getService(requireContext())
                val response = api.getBarang()

                binding.progressBeranda.visibility = View.GONE
                binding.swipeRefresh.isRefreshing = false

                if (response.isSuccessful && response.body()?.success == true) {
                    val data = response.body()?.data ?: emptyList()
                    productList.clear()
                    productList.addAll(data)
                    adapter.submitList(productList)

                    if (productList.isEmpty()) {
                        binding.tvEmptyCatalog.visibility = View.VISIBLE
                        binding.tvEmptyCatalog.text = "Belum ada produk aktif di katalog pabrik."
                    } else {
                        binding.tvEmptyCatalog.visibility = View.GONE
                    }
                } else {
                    binding.tvEmptyCatalog.visibility = View.VISIBLE
                    binding.tvEmptyCatalog.text = "Gagal memuat katalog: ${response.message()}"
                }
            } catch (e: Exception) {
                binding.progressBeranda.visibility = View.GONE
                binding.swipeRefresh.isRefreshing = false
                binding.tvEmptyCatalog.visibility = View.VISIBLE
                binding.tvEmptyCatalog.text = "Katalog pabrik sedang dimuat (${e.localizedMessage ?: "Menghubungkan server"})."
            }

            updateCartSummaryBar()
        }
    }

    private fun updateCartSummaryBar() {
        val cartManager = CartManager.getInstance(requireContext())
        val cartItems = cartManager.getItems()

        var totalSlop = 0
        var totalSubtotal = 0.0

        for (item in cartItems) {
            if (item.satuan.equals("Slop", ignoreCase = true)) {
                totalSlop += item.jumlah
            } else {
                totalSlop += (item.jumlah * item.barang.slopPerBal)
            }
            totalSubtotal += item.subtotal
        }

        binding.tvSlopTerpilihHeader.text = "$totalSlop Slop Terpilih"

        if (cartItems.isNotEmpty()) {
            binding.layoutBottomCartBar.visibility = View.VISIBLE
            binding.tvBottomCartTotalQty.text = "$totalSlop Slop Total"
            binding.tvBottomCartTotalPrice.text = CurrencyHelper.formatRupiah(totalSubtotal)
        } else {
            binding.layoutBottomCartBar.visibility = View.GONE
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
