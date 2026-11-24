package com.keretakencana.mitra.ui.beranda

import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
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
    private var allProducts = listOf<Barang>()
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

        binding.ivNotification.setOnClickListener {
            val intent = Intent(requireContext(), com.keretakencana.mitra.ui.notifikasi.NotifikasiActivity::class.java)
            startActivity(intent)
        }

        binding.btnLihatKeranjang.setOnClickListener {
            val intent = Intent(requireContext(), com.keretakencana.mitra.ui.cart.CartActivity::class.java)
            startActivity(intent)
        }

        binding.etSearchProduk.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: Editable?) {
                applyProductFilter(s?.toString() ?: "")
            }
        })
    }

    private fun applyProductFilter(query: String) {
        val q = query.trim().lowercase()
        val filtered = if (q.isEmpty()) {
            allProducts
        } else {
            allProducts.filter {
                it.nama.lowercase().contains(q) || it.kodeBarang.lowercase().contains(q)
            }
        }
        productList.clear()
        productList.addAll(filtered)
        adapter.submitList(productList)

        if (allProducts.isNotEmpty() && filtered.isEmpty()) {
            binding.tvEmptyCatalog.visibility = View.VISIBLE
            binding.tvEmptyCatalog.text = "Produk \"$query\" tidak ditemukan."
        } else if (allProducts.isNotEmpty()) {
            binding.tvEmptyCatalog.visibility = View.GONE
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
                // Sinkronkan konfigurasi resmi pabrik (WA Admin, NPPBKC, status rekening bank)
                try {
                    val cfgRes = api.getPengaturan()
                    val cfgData = cfgRes.body()?.data
                    if (cfgRes.isSuccessful && cfgRes.body()?.success == true && cfgData != null) {
                        SessionManager.getInstance(requireContext()).savePengaturan(cfgData)
                    }
                } catch (_: Exception) {}

                val response = api.getBarang()

                binding.progressBeranda.visibility = View.GONE
                binding.swipeRefresh.isRefreshing = false

                if (response.isSuccessful && response.body()?.success == true) {
                    val data = response.body()?.data ?: emptyList()
                    allProducts = data
                    applyProductFilter(binding.etSearchProduk.text?.toString() ?: "")

                    if (allProducts.isEmpty()) {
                        binding.tvEmptyCatalog.visibility = View.VISIBLE
                        binding.tvEmptyCatalog.text = "Belum ada produk aktif di katalog pabrik."
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
        val totalSlop = cartManager.getTotalSlopQty()
        val totalSubtotal = cartManager.getEstimatedSubtotal()

        binding.tvSlopTerpilihHeader.text = "$totalSlop / ${Barang.MAX_SLOP_PER_ORDER} Slop Terpilih"

        if (cartItems.isNotEmpty() && totalSlop > 0) {
            binding.layoutBottomCartBar.visibility = View.VISIBLE
            binding.tvBottomCartTotalQty.text = "$totalSlop Slop Total (${CurrencyHelper.formatRupiah(Barang.hargaSlopByQty(totalSlop))}/slop)"
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
