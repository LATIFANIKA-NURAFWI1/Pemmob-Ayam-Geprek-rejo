package com.pemmob.geprekrejo.util

import java.text.NumberFormat
import java.util.Locale

object CurrencyFormatter {
    private val localeId = Locale.forLanguageTag("id-ID")
    private val formatter: NumberFormat = NumberFormat.getNumberInstance(localeId)

    fun formatRupiah(amount: Double): String {
        return "Rp ${formatter.format(amount.toLong())}"
    }

    fun formatRupiah(amount: Long): String {
        return "Rp ${formatter.format(amount)}"
    }

    /**
     * Memfilter string input hanya digit angka untuk TextField harga
     */
    fun cleanDigits(input: String): String {
        return input.filter { it.isDigit() }
    }
}
