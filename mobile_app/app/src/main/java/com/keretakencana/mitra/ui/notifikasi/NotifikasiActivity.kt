package com.keretakencana.mitra.ui.notifikasi

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.data.model.NotifikasiItem
import com.keretakencana.mitra.data.model.Transaksi
import com.keretakencana.mitra.databinding.ActivityNotifikasiBinding
import com.keretakencana.mitra.ui.pesanan.DetailPesananActivity
import com.keretakencana.mitra.utils.CurrencyHelper
import com.keretakencana.mitra.utils.DateHelper
import kotlinx.coroutines.launch

class NotifikasiActivity : AppCompatActivity() {

    private lateinit var binding: ActivityNotifikasiBinding
    private lateinit var adapter: NotifikasiAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityNotifikasiBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setSupportActionBar(binding.toolbarNotifikasi)
        binding.toolbarNotifikasi.setNavigationOnClickListener { finish() }

        setupRecyclerView()

        binding.swipeRefreshNotif.setOnRefreshListener {
            loadNotifikasi()
        }

        loadNotifikasi()
    }

    private fun setupRecyclerView() {
        adapter = NotifikasiAdapter(onItemClick = { notif ->
            val trxId = notif.transaksiId
            if (trxId != null && trxId > 0) {
                val intent = Intent(this, DetailPesananActivity::class.java).apply {
                    putExtra(DetailPesananActivity.EXTRA_PESANAN_ID, trxId)
                }
                startActivity(intent)
            }
        })
        binding.rvNotifikasi.layoutManager = LinearLayoutManager(this)
        binding.rvNotifikasi.adapter = adapter
    }

    private fun loadNotifikasi() {
        val session = SessionManager.getInstance(this)
        if (!session.isLoggedIn()) {
            binding.swipeRefreshNotif.isRefreshing = false
            val guestNotif = listOf(
                NotifikasiItem(
                    id = "guest_welcome",
                    judul = "Selamat Datang di PR. KERETA KENCANA",
                    pesan = "Silakan masuk ke akun mitra Anda untuk memantau status pembayaran, verifikasi penerimaan, dan proses pengiriman muatan rokok.",
                    waktu = DateHelper.getCurrentWibDateTime(),
                    kategori = "info"
                )
            )
            renderList(guestNotif)
            return
        }

        binding.progressNotif.visibility = View.VISIBLE

        lifecycleScope.launch {
            try {
                val api = ApiClient.getService(this@NotifikasiActivity)
                val response = api.getPesanan()

                binding.progressNotif.visibility = View.GONE
                binding.swipeRefreshNotif.isRefreshing = false

                val listNotif = mutableListOf<NotifikasiItem>()

                // 1. Ambil Notifikasi Keamanan dari SessionManager (misal: Perubahan Sandi)
                val securityNotifs = session.getSecurityNotifications()
                listNotif.addAll(securityNotifs)

                // 2. Olah Status Pesanan Menjadi Notifikasi Transaksional Resmi
                if (response.isSuccessful && response.body()?.success == true) {
                    val pesananList: List<Transaksi> = response.body()?.data?.items ?: emptyList()

                    for (t in pesananList) {
                        // Kategori A: Pembayaran Disetujui Admin
                        val isTransfer = t.metodePembayaran?.contains("transfer", ignoreCase = true) == true ||
                                t.metodePembayaran?.contains("bca", ignoreCase = true) == true
                        val isLunas = t.statusPembayaran.equals("Lunas", ignoreCase = true)
                        val isDitolak = t.statusPembayaran?.contains("Ditolak", ignoreCase = true) == true ||
                                t.statusPembayaran?.contains("Tidak Valid", ignoreCase = true) == true ||
                                t.statusPembayaran?.contains("Belum Valid", ignoreCase = true) == true

                        if (isTransfer && isLunas) {
                            listNotif.add(
                                NotifikasiItem(
                                    id = "pay_${t.id}",
                                    judul = "Pembayaran Disetujui Admin (${t.kodeTransaksi})",
                                    pesan = "Pembayaran Transfer Bank untuk pesanan Anda sebesar ${CurrencyHelper.formatRupiah(t.totalHarga)} telah diverifikasi dan disetujui lunas oleh admin pabrik.",
                                    waktu = DateHelper.formatNotificationWib(t.ditransferPada ?: t.createdAt),
                                    kategori = "pembayaran",
                                    transaksiId = t.id
                                )
                            )
                        } else if (isTransfer && isDitolak) {
                            listNotif.add(
                                NotifikasiItem(
                                    id = "pay_reject_${t.id}",
                                    judul = "Bukti Transfer Ditolak (${t.kodeTransaksi})",
                                    pesan = "Bukti Transfer Bank untuk pesanan Anda dinyatakan tidak valid atau ditolak oleh bagian keuangan pabrik. Silakan periksa kembali mutasi rekening atau unggah ulang foto bukti transfer yang sah.",
                                    waktu = DateHelper.formatNotificationWib(t.ditransferPada ?: t.createdAt),
                                    kategori = "pembayaran_ditolak",
                                    transaksiId = t.id
                                )
                            )
                        }

                        // Kategori B: Foto Dikonfirmasi oleh Admin (Bukti Penerimaan / Selesai)
                        if (!t.buktiPenerimaanUrl.isNullOrBlank() || t.status.equals("Selesai", ignoreCase = true)) {
                            listNotif.add(
                                NotifikasiItem(
                                    id = "rcv_${t.id}",
                                    judul = "Penerimaan Pesanan Dikonfirmasi (${t.kodeTransaksi})",
                                    pesan = "Foto bukti serah terima muatan rokok telah dikonfirmasi dan disahkan oleh admin pabrik. Transaksi pesanan ini telah selesai. Terima kasih!",
                                    waktu = DateHelper.formatNotificationWib(t.diterimaPada ?: t.createdAt),
                                    kategori = "penerimaan",
                                    transaksiId = t.id
                                )
                            )
                        }

                        // Kategori C: Pesanan Sedang Dikirim Kurir
                        if (t.status.equals("Dikirim", ignoreCase = true)) {
                            listNotif.add(
                                NotifikasiItem(
                                    id = "ship_${t.id}",
                                    judul = "Pesanan Sedang Dikirim Kurir (${t.kodeTransaksi})",
                                    pesan = "Muatan rokok untuk pesanan Anda sedang dalam perjalanan diantar langsung oleh kurir resmi pabrik menuju lokasi toko mitra.",
                                    waktu = DateHelper.formatNotificationWib(t.createdAt),
                                    kategori = "pengiriman",
                                    transaksiId = t.id
                                )
                            )
                        }

                        // Kategori D: Pesanan Baru Masuk / Diproses
                        if (t.status.equals("Baru Masuk", ignoreCase = true) ||
                            t.status.equals("Menunggu Konfirmasi", ignoreCase = true) ||
                            t.status.equals("Pending", ignoreCase = true) ||
                            t.status.equals("Diproses", ignoreCase = true)
                        ) {
                            listNotif.add(
                                NotifikasiItem(
                                    id = "wait_${t.id}",
                                    judul = "Pesanan ${t.status} (${t.kodeTransaksi})",
                                    pesan = "Pesanan Anda telah dicatat oleh sistem pabrik (${t.metodePembayaran ?: "COD"}). Ketuk untuk melihat rincian pesanan atau konfirmasi pembayaran.",
                                    waktu = DateHelper.formatNotificationWib(t.createdAt),
                                    kategori = "menunggu",
                                    transaksiId = t.id
                                )
                            )
                        }
                    }
                }

                // Jika daftar masih kosong, beri notifikasi default akun aktif
                if (listNotif.isEmpty()) {
                    listNotif.add(
                        NotifikasiItem(
                            id = "default_info",
                            judul = "Keamanan Akun: Akun Aktif & Terlindungi",
                            pesan = "Sesi akun mitra Anda aktif dan aman. Pantau rincian mutasi pesanan rokok Anda di menu Pesanan.",
                            waktu = DateHelper.getCurrentWibDateTime(),
                            kategori = "keamanan"
                        )
                    )
                }

                renderList(listNotif)
            } catch (e: Exception) {
                binding.progressNotif.visibility = View.GONE
                binding.swipeRefreshNotif.isRefreshing = false

                // Fallback menampilkan notifikasi keamanan yang tersimpan secara lokal
                val localNotifs = session.getSecurityNotifications().toMutableList()
                if (localNotifs.isEmpty()) {
                    localNotifs.add(
                        NotifikasiItem(
                            id = "offline_info",
                            judul = "Pemberitahuan Sistem Offline",
                            pesan = "Tidak dapat memuat pembaruan transaksi dari server. Periksa koneksi Wi-Fi Anda.",
                            waktu = DateHelper.getCurrentWibDateTime(),
                            kategori = "info"
                        )
                    )
                }
                renderList(localNotifs)
            }
        }
    }

    private fun renderList(items: List<NotifikasiItem>) {
        if (items.isEmpty()) {
            binding.layoutNotifEmpty.visibility = View.VISIBLE
            binding.rvNotifikasi.visibility = View.GONE
            binding.tvFooterNotif.visibility = View.GONE
        } else {
            binding.layoutNotifEmpty.visibility = View.GONE
            binding.rvNotifikasi.visibility = View.VISIBLE
            binding.tvFooterNotif.visibility = View.VISIBLE
            adapter.submitList(items)
        }
    }
}
