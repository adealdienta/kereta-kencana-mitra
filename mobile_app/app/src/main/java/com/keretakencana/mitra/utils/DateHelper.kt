package com.keretakencana.mitra.utils

import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale
import java.util.TimeZone

object DateHelper {

    private val wibTimeZone: TimeZone = TimeZone.getTimeZone("Asia/Jakarta")

    /**
     * Memformat tanggal ISO-8601 (dari Laravel API) ke format tampilan WIB:
     * Contoh: "2026-09-30T09:45:00.000000Z" -> "30 Sep 2026 • 09.45 WIB"
     */
    fun formatDateTimeWib(isoString: String?): String {
        if (isoString.isNullOrBlank()) return "-"

        return try {
            val parser = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss", Locale("id", "ID")).apply {
                timeZone = TimeZone.getTimeZone("UTC")
            }
            // Bersihkan timezone suffix jika ada
            val cleanString = isoString.substringBefore(".").substringBefore("Z").substringBefore("+")
            val date = parser.parse(cleanString) ?: return isoString

            val formatter = SimpleDateFormat("dd MMM yyyy • HH.mm 'WIB'", Locale("id", "ID")).apply {
                timeZone = wibTimeZone
            }
            formatter.format(date)
        } catch (e: Exception) {
            isoString
        }
    }

    /**
     * Alias method untuk memformat ISO datetime ke format Indonesia WIB
     */
    fun formatIsoToIndonesianDateTime(isoString: String?): String {
        return formatDateTimeWib(isoString)
    }

    /**
     * Format tanggal ringkas (untuk stepper timeline):
     * Contoh: "30 Sep"
     */
    fun formatShortDateWib(isoString: String?): String {
        if (isoString.isNullOrBlank()) return "-"

        return try {
            val parser = SimpleDateFormat("yyyy-MM-dd", Locale("id", "ID")).apply {
                timeZone = TimeZone.getTimeZone("UTC")
            }
            val cleanString = isoString.substring(0, 10)
            val date = parser.parse(cleanString) ?: return isoString

            val formatter = SimpleDateFormat("dd MMM", Locale("id", "ID")).apply {
                timeZone = wibTimeZone
            }
            formatter.format(date)
        } catch (e: Exception) {
            isoString
        }
    }

    /**
     * Format notifikasi gaya Shopee dengan zona waktu GMT+7 / WIB:
     * Contoh: "06-11-2026 10:45 WIB"
     */
    fun formatNotificationWib(isoString: String?): String {
        if (isoString.isNullOrBlank()) return getCurrentWibDateTime()

        return try {
            val parser = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss", Locale("id", "ID")).apply {
                timeZone = TimeZone.getTimeZone("UTC")
            }
            val cleanString = isoString.substringBefore(".").substringBefore("Z").substringBefore("+")
            val date = parser.parse(cleanString) ?: return isoString

            val formatter = SimpleDateFormat("dd-MM-yyyy HH:mm 'WIB'", Locale("id", "ID")).apply {
                timeZone = wibTimeZone
            }
            formatter.format(date)
        } catch (_: Exception) {
            isoString
        }
    }

    /**
     * Mendapatkan waktu saat ini dalam format GMT+7 / WIB:
     * Contoh: "06-11-2026 10:45 WIB"
     */
    fun getCurrentWibDateTime(): String {
        val formatter = SimpleDateFormat("dd-MM-yyyy HH:mm 'WIB'", Locale("id", "ID")).apply {
            timeZone = wibTimeZone
        }
        return formatter.format(Date())
    }
}
