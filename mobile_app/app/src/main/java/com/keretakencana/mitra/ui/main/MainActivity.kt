package com.keretakencana.mitra.ui.main

import android.content.Intent
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import com.keretakencana.mitra.R
import com.keretakencana.mitra.databinding.ActivityMainBinding
import com.keretakencana.mitra.ui.akun.AkunFragment
import com.keretakencana.mitra.ui.beranda.BerandaFragment
import com.keretakencana.mitra.ui.pesanan.PesananFragment

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding

    private val berandaFragment = BerandaFragment()
    private val pesananFragment = PesananFragment()
    private val akunFragment = AkunFragment()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupNavigation()
        handleIncomingIntent(intent)
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
