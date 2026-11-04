package com.keretakencana.mitra.ui.pesanan

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.Transaksi
import com.keretakencana.mitra.databinding.FragmentPesananBinding
import com.keretakencana.mitra.ui.auth.LoginBottomSheetFragment
import com.keretakencana.mitra.ui.cart.CartActivity
import com.keretakencana.mitra.ui.main.MainActivity
import kotlinx.coroutines.launch

class PesananFragment : Fragment() {

    private var _binding: FragmentPesananBinding? = null
    private val binding get() = _binding!!

    private lateinit var pesananAdapter: PesananAdapter
    private var currentFilter: String = "semua" // "semua", "berjalan", "selesai"

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentPesananBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupRecyclerView()
        setupListeners()
    }

    override fun onResume() {
        super.onResume()
        checkSessionAndLoadOrders()
    }

    private fun setupRecyclerView() {
        pesananAdapter = PesananAdapter(
            items = emptyList(),
            onItemClick = { transaksi ->
                openDetailPesanan(transaksi.id)
            },
            onPrimaryActionClick = { transaksi ->
                when (transaksi.status) {
                    "Dikirim" -> {
                        val intent = Intent(requireContext(), KonfirmasiPenerimaanActivity::class.java).apply {
                            putExtra(KonfirmasiPenerimaanActivity.EXTRA_PESANAN_ID, transaksi.id)
                            putExtra(KonfirmasiPenerimaanActivity.EXTRA_KODE_TRX, transaksi.kodeTransaksi)
                        }
                        startActivity(intent)
                    }
                    "Selesai" -> {
                        val intent = Intent(requireContext(), FakturActivity::class.java).apply {
                            putExtra(FakturActivity.EXTRA_PESANAN_ID, transaksi.id)
                        }
                        startActivity(intent)
                    }
                    else -> {
                        openDetailPesanan(transaksi.id)
                    }
                }
            },
            onSecondaryActionClick = { transaksi ->
                when (transaksi.status) {
                    "Baru Masuk" -> {
                        showDialogBatalPesanan(transaksi.id)
                    }
                    "Selesai" -> {
                        val intent = Intent(requireContext(), CartActivity::class.java)
                        startActivity(intent)
                    }
                    else -> {
                        openDetailPesanan(transaksi.id)
                    }
                }
            }
        )

        binding.rvPesanan.layoutManager = LinearLayoutManager(requireContext())
        binding.rvPesanan.adapter = pesananAdapter
    }

    private fun setupListeners() {
        binding.swipeRefreshPesanan.setOnRefreshListener {
            checkSessionAndLoadOrders()
        }

        binding.btnFilterSemua.setOnClickListener {
            setFilter("semua")
        }

        binding.btnFilterBerjalan.setOnClickListener {
            setFilter("berjalan")
        }

        binding.btnFilterSelesai.setOnClickListener {
            setFilter("selesai")
        }

        binding.btnFilterDibatalkan.setOnClickListener {
            setFilter("dibatalkan")
        }

        binding.btnGuestLoginPesanan.setOnClickListener {
            val loginSheet = LoginBottomSheetFragment.newInstance()
            loginSheet.onLoginSuccessCallback = {
                checkSessionAndLoadOrders()
            }
            loginSheet.show(childFragmentManager, LoginBottomSheetFragment.TAG)
        }

        binding.btnPesanSekarang.setOnClickListener {
            // Pindah ke tab Beranda
            (activity as? MainActivity)?.let {
                val intent = Intent(requireContext(), MainActivity::class.java).apply {
                    putExtra(MainActivity.EXTRA_NAV_TAB, R.id.menu_beranda)
                }
                startActivity(intent)
            }
        }

        binding.ivPesananProfile.setOnClickListener {
            val session = SessionManager.getInstance(requireContext())
            if (!session.isLoggedIn()) {
                val loginSheet = LoginBottomSheetFragment.newInstance()
                loginSheet.onLoginSuccessCallback = { checkSessionAndLoadOrders() }
                loginSheet.show(childFragmentManager, LoginBottomSheetFragment.TAG)
            } else {
                (activity as? MainActivity)?.let {
                    val intent = Intent(requireContext(), MainActivity::class.java).apply {
                        putExtra(MainActivity.EXTRA_NAV_TAB, R.id.menu_akun)
                    }
                    startActivity(intent)
                }
            }
        }
    }

    private fun setFilter(filter: String) {
        currentFilter = filter

        val selectedBg = R.drawable.bg_rounded_card_selected
        val unselectedBg = R.drawable.bg_rounded_card
        val selectedText = ContextCompat.getColor(requireContext(), R.color.brand_brown_dark)
        val unselectedText = ContextCompat.getColor(requireContext(), R.color.text_secondary)

        binding.btnFilterSemua.setBackgroundResource(if (filter == "semua") selectedBg else unselectedBg)
        binding.btnFilterSemua.setTextColor(if (filter == "semua") selectedText else unselectedText)

        binding.btnFilterBerjalan.setBackgroundResource(if (filter == "berjalan") selectedBg else unselectedBg)
        binding.btnFilterBerjalan.setTextColor(if (filter == "berjalan") selectedText else unselectedText)

        binding.btnFilterSelesai.setBackgroundResource(if (filter == "selesai") selectedBg else unselectedBg)
        binding.btnFilterSelesai.setTextColor(if (filter == "selesai") selectedText else unselectedText)

        binding.btnFilterDibatalkan.setBackgroundResource(if (filter == "dibatalkan") selectedBg else unselectedBg)
        binding.btnFilterDibatalkan.setTextColor(if (filter == "dibatalkan") selectedText else unselectedText)

        loadOrders()
    }

    private fun checkSessionAndLoadOrders() {
        val session = SessionManager.getInstance(requireContext())
        if (!session.isLoggedIn()) {
            binding.swipeRefreshPesanan.isRefreshing = false
            binding.layoutGuestPesanan.visibility = View.VISIBLE
            binding.rvPesanan.visibility = View.GONE
            binding.layoutEmptyPesanan.visibility = View.GONE
            binding.progressPesanan.visibility = View.GONE
        } else {
            binding.layoutGuestPesanan.visibility = View.GONE
            loadOrders()
        }
    }

    private fun loadOrders() {
        binding.progressPesanan.visibility = View.VISIBLE
        binding.layoutEmptyPesanan.visibility = View.GONE

        viewLifecycleOwner.lifecycleScope.launch {
            try {
                val api = ApiClient.getService(requireContext())
                val response = api.getPesanan(status = currentFilter)

                binding.progressPesanan.visibility = View.GONE
                binding.swipeRefreshPesanan.isRefreshing = false

                if (response.isSuccessful && response.body()?.success == true) {
                    val listResponse = response.body()?.data
                    val items = listResponse?.items ?: emptyList()

                    pesananAdapter.submitList(items)

                    if (items.isEmpty()) {
                        binding.rvPesanan.visibility = View.GONE
                        binding.layoutEmptyPesanan.visibility = View.VISIBLE

                        val emptyText = when (currentFilter) {
                            "berjalan" -> "Belum ada pesanan yang sedang berjalan."
                            "selesai" -> "Belum ada riwayat pesanan yang selesai."
                            "dibatalkan" -> "Belum ada riwayat pesanan yang dibatalkan."
                            else -> "Belum ada pesanan rokok grosir."
                        }
                        binding.tvEmptyPesananSubtitle.text = emptyText
                    } else {
                        binding.rvPesanan.visibility = View.VISIBLE
                        binding.layoutEmptyPesanan.visibility = View.GONE
                    }
                } else {
                    binding.rvPesanan.visibility = View.GONE
                    binding.layoutEmptyPesanan.visibility = View.VISIBLE
                    binding.tvEmptyPesananSubtitle.text = "Gagal memuat pesanan: ${response.message()}"
                }
            } catch (e: Exception) {
                binding.progressPesanan.visibility = View.GONE
                binding.swipeRefreshPesanan.isRefreshing = false
                binding.rvPesanan.visibility = View.GONE
                binding.layoutEmptyPesanan.visibility = View.VISIBLE
                binding.tvEmptyPesananSubtitle.text = "Tidak dapat terhubung ke server: ${e.localizedMessage}"
            }
        }
    }

    private fun openDetailPesanan(id: Long) {
        val intent = Intent(requireContext(), DetailPesananActivity::class.java).apply {
            putExtra(DetailPesananActivity.EXTRA_PESANAN_ID, id)
        }
        startActivity(intent)
    }

    private fun showDialogBatalPesanan(id: Long) {
        AlertDialog.Builder(requireContext())
            .setTitle("Batalkan Pesanan?")
            .setMessage("Apakah Anda yakin ingin membatalkan pesanan ini? Stok barang akan dikembalikan ke gudang pabrik.")
            .setPositiveButton("Ya, Batalkan") { _, _ ->
                eksekusiBatal(id)
            }
            .setNegativeButton("Tidak", null)
            .show()
    }

    private fun eksekusiBatal(id: Long) {
        viewLifecycleOwner.lifecycleScope.launch {
            try {
                val api = ApiClient.getService(requireContext())
                val response = api.batalkanPesanan(id)

                if (response.isSuccessful && response.body()?.success == true) {
                    Toast.makeText(requireContext(), "Pesanan berhasil dibatalkan.", Toast.LENGTH_SHORT).show()
                    loadOrders()
                } else {
                    Toast.makeText(requireContext(), "Gagal membatalkan pesanan.", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                Toast.makeText(requireContext(), "Koneksi terganggu: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
