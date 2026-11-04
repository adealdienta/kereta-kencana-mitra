package com.keretakencana.mitra.utils

import java.text.DecimalFormat
import java.text.DecimalFormatSymbols
import java.util.Locale

object CurrencyHelper {

    /**
     * Memformat nominal angka ke format Rupiah standar Indonesia:
     * Contoh: 162500 -> "Rp 162.500"
     */
    fun formatRupiah(amount: Number?): String {
        if (amount == null) return "Rp 0"

        val symbols = DecimalFormatSymbols(Locale("id", "ID")).apply {
            groupingSeparator = '.'
            decimalSeparator = ','
        }

        val formatter = DecimalFormat("#,###", symbols)
        return "Rp " + formatter.format(amount.toLong())
    }
}
