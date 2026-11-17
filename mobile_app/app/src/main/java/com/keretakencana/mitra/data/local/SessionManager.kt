package com.keretakencana.mitra.data.local

import android.content.Context
import android.content.SharedPreferences
import com.google.gson.Gson
import com.keretakencana.mitra.data.model.Pengaturan
import com.keretakencana.mitra.data.model.User

class SessionManager(context: Context) {

    private val prefs: SharedPreferences = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE)
    private val gson = Gson()

    companion object {
        private const val PREF_NAME = "kereta_kencana_session"
        private const val KEY_AUTH_TOKEN = "key_auth_token"
        private const val KEY_USER = "key_user"
        private const val KEY_IS_LOGGED_IN = "key_is_logged_in"
        private const val KEY_SECURITY_NOTIFS = "key_security_notifs"
        private const val KEY_PENGATURAN = "key_pengaturan"

        const val DEFAULT_WA_PABRIK = "6285790627104"
        const val DEFAULT_NPPBKC = "750******-070700-221221*******"

        @Volatile
        private var instance: SessionManager? = null

        fun getInstance(context: Context): SessionManager {
            return instance ?: synchronized(this) {
                instance ?: SessionManager(context.applicationContext).also { instance = it }
            }
        }
    }

    fun saveAuth(token: String, user: User) {
        prefs.edit()
            .putString(KEY_AUTH_TOKEN, token)
            .putString(KEY_USER, gson.toJson(user))
            .putBoolean(KEY_IS_LOGGED_IN, true)
            .apply()
    }

    fun saveUser(user: User) {
        prefs.edit()
            .putString(KEY_USER, gson.toJson(user))
            .apply()
    }

    fun getAuthToken(): String? {
        return prefs.getString(KEY_AUTH_TOKEN, null)
    }

    fun getUser(): User? {
        val userJson = prefs.getString(KEY_USER, null) ?: return null
        return try {
            gson.fromJson(userJson, User::class.java)
        } catch (e: Exception) {
            null
        }
    }

    fun isLoggedIn(): Boolean {
        return prefs.getBoolean(KEY_IS_LOGGED_IN, false) && !getAuthToken().isNullOrBlank()
    }

    fun clearSession() {
        prefs.edit()
            .remove(KEY_AUTH_TOKEN)
            .remove(KEY_USER)
            .putBoolean(KEY_IS_LOGGED_IN, false)
            .apply()
    }

    fun addSecurityNotification(notif: com.keretakencana.mitra.data.model.NotifikasiItem) {
        val currentList = getSecurityNotifications().toMutableList()
        currentList.add(0, notif) // simpan di paling atas
        if (currentList.size > 20) {
            currentList.removeAt(currentList.lastIndex)
        }
        prefs.edit().putString(KEY_SECURITY_NOTIFS, gson.toJson(currentList)).apply()
    }

    fun getSecurityNotifications(): List<com.keretakencana.mitra.data.model.NotifikasiItem> {
        val json = prefs.getString(KEY_SECURITY_NOTIFS, null) ?: return emptyList()
        return try {
            val type = object : com.google.gson.reflect.TypeToken<List<com.keretakencana.mitra.data.model.NotifikasiItem>>() {}.type
            gson.fromJson(json, type) ?: emptyList()
        } catch (_: Exception) {
            emptyList()
        }
    }

    fun savePengaturan(pengaturan: Pengaturan) {
        prefs.edit().putString(KEY_PENGATURAN, gson.toJson(pengaturan)).apply()
    }

    fun getPengaturan(): Pengaturan {
        val json = prefs.getString(KEY_PENGATURAN, null)
        if (!json.isNullOrBlank()) {
            try {
                val parsed = gson.fromJson(json, Pengaturan::class.java)
                if (parsed != null) {
                    return parsed.copy(
                        waPabrik = parsed.waPabrik?.takeIf { it.isNotBlank() } ?: DEFAULT_WA_PABRIK,
                        nppbkc = parsed.nppbkc?.takeIf { it.isNotBlank() } ?: DEFAULT_NPPBKC
                    )
                }
            } catch (_: Exception) {}
        }
        return Pengaturan(
            waPabrik = DEFAULT_WA_PABRIK,
            nppbkc = DEFAULT_NPPBKC,
            namaPabrik = "PR. KERETA KENCANA",
            alamatPabrik = "Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Ponggok, Blitar",
            bankNama = null,
            bankRekening = null,
            bankAtasNama = null
        )
    }
}
