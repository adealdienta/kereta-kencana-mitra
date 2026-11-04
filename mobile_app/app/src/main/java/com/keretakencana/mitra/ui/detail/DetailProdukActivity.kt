package com.keretakencana.mitra.ui.detail

import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.google.gson.Gson
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.CartManager
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.databinding.ActivityDetailProdukBinding
import com.keretakencana.mitra.utils.CurrencyHelper
import kotlinx.coroutines.launch

class DetailProdukActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetailProdukBinding

    private var currentBarang: Barang? = null
    private var selectedSatuan: String = "Slop"
    private var selectedQty: Int = 1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDetailProdukBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val barangJson = intent.getStringExtra(EXTRA_BARANG_JSON)
        val barangId = intent.getLongExtra(EXTRA_BARANG_ID, -1L)

        if (!barangJson.isNullOrBlank()) {
            currentBarang = Gson().fromJson(barangJson, Barang::class.java)
            populateData(currentBarang!!)
        } else if (barangId > 0) {
            loadBarangFromApi(barangId)
        } else {
            Toast.makeText(this, "Produk tidak ditemukan.", Toast.LENGTH_SHORT).show()
            finish()
        }

        setupListeners()
    }

    private fun loadBarangFromApi(id: Long) {
        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@DetailProdukActivity)
                val response = api.getBarangDetail(id)
                if (response.isSuccessful && response.body()?.success == true) {
                    val barang = response.body()?.data
                    if (barang != null) {
                        currentBarang = barang
                        populateData(barang)
                    }
                }
            } catch (e: Exception) {
                Toast.makeText(this@DetailProdukActivity, "Gagal memuat produk", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun populateData(barang: Barang) {
        binding.tvDetailNama.text = barang.nama
        binding.tvDetailSku.text = barang.kodeBarang

        // Deskripsi
        if (!barang.deskripsi.isNullOrBlank()) {
            binding.tvDetailDeskripsi.text = barang.deskripsi
        }

        // Spesifikasi Tar, Nikotin
        val tar = barang.kadarTar ?: "30 mg"
        val nikotin = barang.kadarNikotin ?: "2 mg"
        val batang = barang.isiBatang ?: "${barang.bungkusPerSlop + 2} batang"
        binding.tvDetailSpesifikasi.text = "Tar $tar • Nikotin $nikotin • $batang per bungkus"

        // Gambar
        val localRes = when {
            barang.kodeBarang.contains("KK", ignoreCase = true) -> R.drawable.produk_kereta_kencana_12
            barang.kodeBarang.contains("SC", ignoreCase = true) -> R.drawable.produk_sembada_cethe_12
            barang.kodeBarang.contains("SM", ignoreCase = true) -> R.drawable.produk_sembada_12
            else -> R.drawable.logo_resmi
        }

        if (!barang.imageUrl.isNullOrBlank()) {
            Glide.with(this)
                .load(barang.imageUrl)
                .placeholder(localRes)
                .error(localRes)
                .into(binding.ivDetailFoto)
        } else {
            binding.ivDetailFoto.setImageResource(localRes)
        }

        // Harga Satuan
        binding.tvSatuanSlopHarga.text = CurrencyHelper.formatRupiah(barang.hargaPerSlop)
        binding.tvSatuanBalHarga.text = CurrencyHelper.formatRupiah(barang.hargaPerBal)
        binding.tvSatuanBalIsi.text = "${barang.slopPerBal} slop (${barang.slopPerBal * barang.bungkusPerSlop} bks)"

        // Keterangan Perkiraan Stok Slop Tersedia (Aturan B2)
        binding.tvDetailStokTersedia.text = "Tersedia ${barang.perkiraanSlopTersedia} Slop (Perkiraan)"

        // Default Satuan Slop
        selectUnit("Slop")
    }

    private fun setupListeners() {
        binding.btnBackDetail.setOnClickListener { finish() }

        binding.cardSatuanSlop.setOnClickListener {
            selectUnit("Slop")
        }

        binding.cardSatuanBal.setOnClickListener {
            selectUnit("Bal")
        }

        // Counter Plus
        binding.btnDetailPlus.setOnClickListener {
            selectedQty++
            updateCounterAndSubtotal()
        }

        // Counter Minus (Aturan B3: Patuhi minimum order)
        binding.btnDetailMinus.setOnClickListener {
            val minOrder = if (selectedSatuan == "Bal") currentBarang?.minOrderBal ?: 1 else currentBarang?.minOrderSlop ?: 1
            if (selectedQty > minOrder) {
                selectedQty--
                updateCounterAndSubtotal()
            } else {
                Toast.makeText(
                    this,
                    "Minimal pemesanan untuk satuan $selectedSatuan adalah $minOrder.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        // Tombol Tambah ke Keranjang
        binding.btnTambahKeKeranjangBottom.setOnClickListener {
            val barang = currentBarang ?: return@setOnClickListener
            CartManager.getInstance(this).addItem(barang, selectedSatuan, selectedQty)
            Toast.makeText(
                this,
                "✓ Berhasil menambahkan $selectedQty $selectedSatuan ${barang.nama} ke keranjang",
                Toast.LENGTH_SHORT
            ).show()
            finish()
        }
    }

    private fun selectUnit(satuan: String) {
        val barang = currentBarang ?: return
        selectedSatuan = satuan

        if (satuan == "Slop") {
            binding.cardSatuanSlop.setBackgroundResource(R.drawable.bg_rounded_card_selected)
            binding.ivCheckSlop.visibility = View.VISIBLE
            binding.tvSatuanSlopHarga.setTextColor(getColor(R.color.price_red))

            binding.cardSatuanBal.setBackgroundResource(R.drawable.bg_rounded_card)
            binding.ivCheckBal.visibility = View.GONE
            binding.tvSatuanBalHarga.setTextColor(getColor(R.color.text_primary))

            binding.tvDetailHarga.text = "${CurrencyHelper.formatRupiah(barang.hargaPerSlop)} / slop"
            if (selectedQty < barang.minOrderSlop) selectedQty = barang.minOrderSlop
        } else {
            binding.cardSatuanBal.setBackgroundResource(R.drawable.bg_rounded_card_selected)
            binding.ivCheckBal.visibility = View.VISIBLE
            binding.tvSatuanBalHarga.setTextColor(getColor(R.color.price_red))

            binding.cardSatuanSlop.setBackgroundResource(R.drawable.bg_rounded_card)
            binding.ivCheckSlop.visibility = View.GONE
            binding.tvSatuanSlopHarga.setTextColor(getColor(R.color.text_primary))

            binding.tvDetailHarga.text = "${CurrencyHelper.formatRupiah(barang.hargaPerBal)} / bal"
            if (selectedQty < barang.minOrderBal) selectedQty = barang.minOrderBal
        }

        updateCounterAndSubtotal()
    }

    private fun updateCounterAndSubtotal() {
        val barang = currentBarang ?: return
        binding.tvDetailQty.text = selectedQty.toString()

        val unitPrice = if (selectedSatuan == "Bal") barang.hargaPerBal else barang.hargaPerSlop
        val subtotal = selectedQty * unitPrice

        binding.btnTambahKeKeranjangBottom.text =
            "Tambah ke Keranjang • ${CurrencyHelper.formatRupiah(subtotal)}"
    }

    companion object {
        const val EXTRA_BARANG_ID = "extra_barang_id"
        const val EXTRA_BARANG_JSON = "extra_barang_json"
    }
}
