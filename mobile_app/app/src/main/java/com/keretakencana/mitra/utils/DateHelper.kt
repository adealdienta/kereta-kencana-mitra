package com.keretakencana.mitra.utils

import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale
import java.util.TimeZone

object DateHelper {

    private val wibTimeZone: TimeZone = TimeZone.getTimeZone("Asia/Jakarta")
    private val indonesiaLocale: Locale = Locale("id", "ID")

    /**
     * Mem-parse string ISO-8601 dari Laravel API secara akurat dengan menghormati offset zona waktu asli
     * (baik akhiran 'Z' UTC maupun '+07:00' WIB) agar tidak menggeser jam +7 dua kali.
     */
    private fun parseIsoTimestamp(isoString: String): Date? {
        val trimmed = isoString.trim()
        if (trimmed.isEmpty()) return null

        // Normalisasi: hapus milidetik ('.000000'), ubah 'Z' menjadi '+0000', dan hapus ':' di offset '+07:00' -> '+0700'
        var normalized = trimmed
            .replace(Regex("\\.\\d+"), "")
            .replace(Regex("Z$"), "+0000")
        normalized = normalized.replace(Regex("([+-]\\d{2}):(\\d{2})$"), "$1$2")

        val offsetPatterns = listOf(
            "yyyy-MM-dd'T'HH:mm:ssZ",
            "yyyy-MM-dd HH:mm:ssZ"
        )
        for (pattern in offsetPatterns) {
            try {
                val parser = SimpleDateFormat(pattern, Locale.US)
                parser.isLenient = false
                return parser.parse(normalized)
            } catch (_: Exception) {
            }
        }

        val localPatterns = listOf(
            "yyyy-MM-dd'T'HH:mm:ss",
            "yyyy-MM-dd HH:mm:ss",
            "yyyy-MM-dd"
        )
        for (pattern in localPatterns) {
            try {
                val parser = SimpleDateFormat(pattern, Locale.US).apply {
                    timeZone = wibTimeZone
                    isLenient = false
                }
                val datePart = if (pattern == "yyyy-MM-dd" && normalized.length >= 10) {
                    normalized.substring(0, 10)
                } else {
                    normalized
                }
                return parser.parse(datePart)
            } catch (_: Exception) {
            }
        }

        return null
    }

    /**
     * Memformat tanggal ISO-8601 (dari Laravel API) ke format tampilan WIB:
     * Contoh: "2026-11-23T09:45:00+07:00" -> "23 Nov 2026 • 09.45 WIB"
     */
    fun formatDateTimeWib(isoString: String?): String {
        if (isoString.isNullOrBlank()) return "-"

        val date = parseIsoTimestamp(isoString) ?: return isoString
        val formatter = SimpleDateFormat("dd MMM yyyy • HH.mm 'WIB'", indonesiaLocale).apply {
            timeZone = wibTimeZone
        }
        return formatter.format(date)
    }

    fun formatIsoToIndonesianDateTime(isoString: String?): String {
        return formatDateTimeWib(isoString)
    }

    fun formatShortDateWib(isoString: String?): String {
        if (isoString.isNullOrBlank()) return "-"

        val date = parseIsoTimestamp(isoString) ?: return isoString
        val formatter = SimpleDateFormat("dd MMM", indonesiaLocale).apply {
            timeZone = wibTimeZone
        }
        return formatter.format(date)
    }

    fun formatNotificationWib(isoString: String?): String {
        if (isoString.isNullOrBlank()) return getCurrentWibDateTime()

        val date = parseIsoTimestamp(isoString) ?: return isoString
        val formatter = SimpleDateFormat("dd-MM-yyyy HH:mm 'WIB'", indonesiaLocale).apply {
            timeZone = wibTimeZone
        }
        return formatter.format(date)
    }

    fun getCurrentWibDateTime(): String {
        val formatter = SimpleDateFormat("dd-MM-yyyy HH:mm 'WIB'", indonesiaLocale).apply {
            timeZone = wibTimeZone
        }
        return formatter.format(Date())
    }
}
