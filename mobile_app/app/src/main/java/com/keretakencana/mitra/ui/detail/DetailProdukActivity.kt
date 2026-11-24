package com.keretakencana.mitra.ui.detail

import android.content.Intent
import android.net.Uri
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
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.Barang
import com.keretakencana.mitra.databinding.ActivityDetailProdukBinding
import com.keretakencana.mitra.utils.CurrencyHelper
import kotlinx.coroutines.launch
import java.net.URLEncoder

class DetailProdukActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetailProdukBinding

    private var currentBarang: Barang? = null
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
            val targetId = currentBarang?.id ?: barangId
            if (targetId > 0) {
                loadBarangFromApi(targetId, silent = true)
            }
        } else if (barangId > 0) {
            loadBarangFromApi(barangId, silent = false)
        } else {
            Toast.makeText(this, "Produk tidak ditemukan.", Toast.LENGTH_SHORT).show()
            finish()
        }

        setupListeners()
    }

    private fun loadBarangFromApi(id: Long, silent: Boolean = false) {
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
                if (!silent) {
                    Toast.makeText(this@DetailProdukActivity, "Gagal memuat produk", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun populateData(barang: Barang) {
        binding.tvDetailNama.text = barang.nama
        binding.tvDetailBestSeller.visibility = if (barang.bestSeller) View.VISIBLE else View.GONE
        binding.tvDetailSku.text = barang.kodeBarang

        // Deskripsi, Profil Rasa, & Info Tarif Grosir Slop
        val descParts = mutableListOf<String>()
        if (!barang.deskripsi.isNullOrBlank()) descParts.add(barang.deskripsi)
        if (!barang.profilRasa.isNullOrBlank()) descParts.add("Karakter rasa: ${barang.profilRasa}")
        descParts.add(
            "Informasi Harga Grosir Slop:\n" +
                "• 1–5 Slop: Rp 70.000 / slop\n" +
                "• 6–10 Slop: Rp 65.000 / slop\n" +
                "• Di atas 10 Slop (11–19): Rp 60.000 / slop"
        )
        binding.tvDetailDeskripsi.text = descParts.joinToString("\n\n")

        // Spesifikasi Resmi SKT (tanpa Tar/Nikotin dummy)
        val specParts = mutableListOf<String>()
        if (barang.tarMg != null && barang.nikotinMg != null) {
            specParts.add("Tar ${barang.tarMg} mg • Nikotin ${barang.nikotinMg} mg")
        }
        val kategoriLabel = barang.kategoriNama ?: barang.kategori?.namaKategori ?: "SKT"
        specParts.add("$kategoriLabel • ${barang.batangPerBungkus} batang/bks • ${barang.bungkusPerSlop} bks/slop")
        binding.tvDetailSpesifikasi.text = specParts.joinToString(" • ")

        // Gambar
        val localRes = when {
            barang.kodeBarang.contains("KK", ignoreCase = true) -> R.drawable.produk_kereta_kencana_12
            barang.kodeBarang.contains("SC", ignoreCase = true) -> R.drawable.produk_sembada_cethe_12
            barang.kodeBarang.contains("SM", ignoreCase = true) -> R.drawable.produk_sembada_12
            else -> R.drawable.ic_logo_kereta_kencana
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

        // Harga Satuan & Info Grosir
        binding.tvSatuanSlopHarga.text = CurrencyHelper.formatRupiah(barang.hargaPerSlop)
        binding.tvSatuanBalHarga.text = "Hubungi Admin"
        binding.tvSatuanBalIsi.text = "${barang.slopPerBal} slop (${barang.slopPerBal * barang.bungkusPerSlop} bks) • Order Bal via WA"

        val stokSlop = barang.stokTersediaSlop
        val hasStock = barang.hasAvailableStock
        binding.tvDetailStokTersedia.text = if (hasStock) {
            "Tersedia $stokSlop Slop (Maks. ${Barang.MAX_SLOP_PER_ORDER} Slop/order)"
        } else {
            "Stok gudang kosong"
        }

        val cartManager = CartManager.getInstance(this)
        val maxOrderAllowed = (Barang.MAX_SLOP_PER_ORDER - cartManager.getTotalSlopQty()).coerceAtLeast(0)
        val existingForBarang = cartManager.getItems()
            .find { it.barang.id == barang.id }?.jumlah ?: 0
        val maxStockAllowed = (stokSlop - existingForBarang).coerceAtLeast(0)
        val maxAdd = minOf(maxOrderAllowed, maxStockAllowed)
        val minOrder = barang.minOrderSlop.coerceAtLeast(1)

        val canOrderNow = hasStock && maxAdd > 0
        binding.btnDetailPlus.isEnabled = canOrderNow
        binding.btnDetailMinus.isEnabled = canOrderNow
        binding.btnTambahKeKeranjangBottom.isEnabled = canOrderNow
        binding.btnTambahKeKeranjangBottom.alpha = if (canOrderNow) 1f else 0.5f

        selectedQty = when {
            !canOrderNow -> 0
            maxAdd < minOrder -> maxAdd
            else -> minOrder
        }
        selectSlopUnit()
    }

    private fun setupListeners() {
        binding.btnBackDetail.setOnClickListener { finish() }

        binding.ivProfileDetail.setOnClickListener {
            val intent = Intent(this, com.keretakencana.mitra.ui.main.MainActivity::class.java).apply {
                flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
                putExtra(com.keretakencana.mitra.ui.main.MainActivity.EXTRA_NAV_TAB, R.id.menu_akun)
            }
            startActivity(intent)
        }

        binding.cardSatuanSlop.setOnClickListener {
            selectSlopUnit()
        }

        binding.cardSatuanBal.setOnClickListener {
            openWhatsAppBulkBal()
        }

        // Counter Plus dengan batas 19 Slop & ketersediaan stok
        binding.btnDetailPlus.setOnClickListener {
            val barang = currentBarang ?: return@setOnClickListener
            val cartManager = CartManager.getInstance(this)
            val totalInCart = cartManager.getTotalSlopQty()
            val maxOrderAllowed = (Barang.MAX_SLOP_PER_ORDER - totalInCart).coerceAtLeast(0)
            val existingForBarang = cartManager.getItems()
                .find { it.barang.id == barang.id }?.jumlah ?: 0
            val maxStockAllowed = (barang.stokTersediaSlop - existingForBarang).coerceAtLeast(0)
            val maxAdd = minOf(maxOrderAllowed, maxStockAllowed)

            if (selectedQty < maxAdd) {
                selectedQty++
                updateCounterAndSubtotal()
            } else if (totalInCart + selectedQty >= Barang.MAX_SLOP_PER_ORDER) {
                Toast.makeText(
                    this,
                    "Maksimal ${Barang.MAX_SLOP_PER_ORDER} Slop gabungan per pesanan online. Pembelian skala Bal (20+ Slop) via WhatsApp Admin.",
                    Toast.LENGTH_LONG
                ).show()
            } else {
                Toast.makeText(this, "Stok gudang untuk ${barang.nama} tidak mencukupi.", Toast.LENGTH_SHORT).show()
            }
        }

        // Counter Minus (Patuhi minimum order)
        binding.btnDetailMinus.setOnClickListener {
            val minOrder = currentBarang?.minOrderSlop?.coerceAtLeast(1) ?: 1
            if (selectedQty > minOrder) {
                selectedQty--
                updateCounterAndSubtotal()
            } else {
                Toast.makeText(
                    this,
                    "Minimal pemesanan untuk satuan Slop adalah $minOrder.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        // Tombol Tambah ke Keranjang (khusus Slop)
        binding.btnTambahKeKeranjangBottom.setOnClickListener {
            val barang = currentBarang ?: return@setOnClickListener
            val (ok, msg) = CartManager.getInstance(this).tryAddSlop(barang, selectedQty)
            if (ok) {
                Toast.makeText(
                    this,
                    "✓ Berhasil menambahkan $selectedQty Slop ${barang.nama} ke keranjang",
                    Toast.LENGTH_SHORT
                ).show()
                finish()
            } else {
                Toast.makeText(this, msg ?: "Gagal menambahkan ke keranjang.", Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun openWhatsAppBulkBal() {
        val barang = currentBarang ?: return
        val waRaw = SessionManager.getInstance(this).getPengaturan().waPabrik
            ?: SessionManager.DEFAULT_WA_PABRIK
        val cleanPhone = waRaw.replace("+", "").replace("-", "").replace(" ", "").trim()
        val msg = "Halo Admin PR. Kereta Kencana, saya berminat memesan produk *${barang.nama}* (${barang.kodeBarang}) dalam satuan *Bal* (${barang.slopPerBal} Slop/Bal). Mohon info stok & penawarannya."
        try {
            val url = "https://api.whatsapp.com/send?phone=$cleanPhone&text=${URLEncoder.encode(msg, "UTF-8")}"
            startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
        } catch (e: Exception) {
            Toast.makeText(this, "Tidak dapat membuka WhatsApp: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
        }
    }

    private fun selectSlopUnit() {
        val barang = currentBarang ?: return
        binding.cardSatuanSlop.setBackgroundResource(R.drawable.bg_rounded_card_selected)
        binding.ivCheckSlop.visibility = View.VISIBLE
        binding.tvSatuanSlopHarga.setTextColor(getColor(R.color.price_red))

        binding.cardSatuanBal.setBackgroundResource(R.drawable.bg_rounded_card)
        binding.ivCheckBal.visibility = View.GONE
        binding.tvSatuanBalHarga.setTextColor(getColor(R.color.brand_brown_dark))

        val minOrder = barang.minOrderSlop.coerceAtLeast(1)
        if (selectedQty in 1 until minOrder) {
            selectedQty = minOrder
        }
        updateCounterAndSubtotal()
    }

    private fun updateCounterAndSubtotal() {
        val barang = currentBarang ?: return
        binding.tvDetailQty.text = selectedQty.toString()

        if (!barang.hasAvailableStock) {
            binding.tvDetailHarga.text = "${CurrencyHelper.formatRupiah(barang.hargaPerSlop)} / slop"
            binding.btnTambahKeKeranjangBottom.isEnabled = false
            binding.btnTambahKeKeranjangBottom.alpha = 0.5f
            binding.btnTambahKeKeranjangBottom.text = "Stok Gudang Habis"
            return
        }

        if (selectedQty <= 0) {
            binding.tvDetailHarga.text = "${CurrencyHelper.formatRupiah(barang.hargaPerSlop)} / slop"
            binding.btnTambahKeKeranjangBottom.isEnabled = false
            binding.btnTambahKeKeranjangBottom.alpha = 0.5f
            binding.btnTambahKeKeranjangBottom.text = "Batas Maksimal Keranjang Tercapai"
            return
        }

        val currentCartSlop = CartManager.getInstance(this).getTotalSlopQty()
        val projectedTotalSlop = currentCartSlop + selectedQty
        val effectivePrice = Barang.hargaSlopByQty(projectedTotalSlop)
        val subtotal = selectedQty * effectivePrice

        binding.tvDetailHarga.text = "${CurrencyHelper.formatRupiah(effectivePrice)} / slop"
        binding.btnTambahKeKeranjangBottom.text =
            "Tambah $selectedQty Slop • ${CurrencyHelper.formatRupiah(subtotal)}"
    }

    companion object {
        const val EXTRA_BARANG_ID = "extra_barang_id"
        const val EXTRA_BARANG_JSON = "extra_barang_json"
    }
}
