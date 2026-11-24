package com.keretakencana.mitra.ui.main

import android.content.Intent
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.app.AppCompatDelegate
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import com.keretakencana.mitra.R
import com.keretakencana.mitra.data.api.ApiClient
import com.keretakencana.mitra.data.local.SessionManager
import com.keretakencana.mitra.databinding.ActivityMainBinding
import com.keretakencana.mitra.ui.akun.AkunFragment
import com.keretakencana.mitra.ui.auth.LoginBottomSheetFragment
import com.keretakencana.mitra.ui.beranda.BerandaFragment
import com.keretakencana.mitra.ui.pesanan.PesananFragment
import kotlinx.coroutines.launch

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding

    private val berandaFragment = BerandaFragment()
    private val pesananFragment = PesananFragment()
    private val akunFragment = AkunFragment()

    override fun onCreate(savedInstanceState: Bundle?) {
        AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_NO)
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupNavigation()
        handleIncomingIntent(intent)
        refreshProfileSession()
    }

    private fun refreshProfileSession() {
        val session = SessionManager.getInstance(this)
        if (!session.isLoggedIn()) return

        lifecycleScope.launch {
            try {
                val res = ApiClient.getService(this@MainActivity).getProfile()
                val user = res.body()?.data
                if (res.isSuccessful && res.body()?.success == true && user != null) {
                    session.saveUser(user)
                } else if (res.code() == 401) {
                    session.clearSession()
                    val sheet = LoginBottomSheetFragment.newInstance()
                    sheet.show(supportFragmentManager, LoginBottomSheetFragment.TAG)
                }
            } catch (_: Exception) {
            }
        }
    }

    override fun onNewIntent(intent: Intent?) {
        super.onNewIntent(intent)
        handleIncomingIntent(intent)
    }

    private fun handleIncomingIntent(intent: Intent?) {
        val targetTab = intent?.getIntExtra(EXTRA_NAV_TAB, -1) ?: -1
        if (targetTab != -1) {
            binding.bottomNavigation.selectedItemId = targetTab
        } else {
            setCurrentFragment(berandaFragment)
        }
    }

    private fun setupNavigation() {
        binding.bottomNavigation.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.menu_beranda -> {
                    setCurrentFragment(berandaFragment)
                    true
                }
                R.id.menu_pesanan -> {
                    setCurrentFragment(pesananFragment)
                    true
                }
                R.id.menu_akun -> {
                    setCurrentFragment(akunFragment)
                    true
                }
                else -> false
            }
        }
    }

    private fun setCurrentFragment(fragment: Fragment) {
        supportFragmentManager.beginTransaction()
            .replace(R.id.fragment_container, fragment)
            .commit()
    }

    companion object {
        const val EXTRA_NAV_TAB = "extra_nav_tab"
    }
}
