package com.pemmob.geprekrejo.data.repository

import com.google.gson.Gson
import com.google.gson.JsonObject
import com.pemmob.geprekrejo.data.model.*
import com.pemmob.geprekrejo.network.ApiService
import com.pemmob.geprekrejo.util.Result

class FinanceRepository(private val apiService: ApiService) {

    // ── Laporan Laba / Rugi ───────────────────────────────────────────────────

    suspend fun getProfitLoss(
        preset: String? = null,
        from: String? = null,
        to: String? = null
    ): Result<ProfitLossWrapper> = safeApiCall {
        val response = apiService.getProfitLossReport(preset = preset, from = from, to = to)
        if (response.isSuccessful) {
            response.body()?.data ?: throw Exception("Data laporan kosong.")
        } else {
            throw Exception("Gagal memuat laporan laba rugi. (${response.code()})")
        }
    }

    // ── Pengeluaran Operasional ───────────────────────────────────────────────

    suspend fun getExpenses(
        month: String? = null,
        search: String? = null,
        category: String? = null,
        page: Int = 1
    ): Result<ExpenseListResponse> = safeApiCall {
        val response = apiService.getExpenseList(month = month, search = search, category = category, page = page)
        if (response.isSuccessful) {
            response.body() ?: throw Exception("Data pengeluaran kosong.")
        } else {
            throw Exception("Gagal memuat daftar pengeluaran. (${response.code()})")
        }
    }

    suspend fun createExpense(request: ExpenseRequest): Result<ExpenseItem> = safeApiCall {
        val r = apiService.createExpense(request)
        if (r.isSuccessful) {
            r.body()?.data ?: throw Exception("Data pengeluaran kosong.")
        } else {
            throw Exception(parseValidationError(r.errorBody()?.string(), "Gagal mencatat pengeluaran."))
        }
    }

    suspend fun updateExpense(id: Int, request: ExpenseRequest): Result<ExpenseItem> = safeApiCall {
        val r = apiService.updateExpense(id, request)
        if (r.isSuccessful) {
            r.body()?.data ?: throw Exception("Data pengeluaran kosong.")
        } else {
            throw Exception(parseValidationError(r.errorBody()?.string(), "Gagal memperbarui pengeluaran."))
        }
    }

    suspend fun deleteExpense(id: Int): Result<Unit> = safeApiCall {
        val r = apiService.deleteExpense(id)
        if (!r.isSuccessful) {
            throw Exception("Gagal menghapus pengeluaran. (${r.code()})")
        }
    }

    suspend fun getExpenseSummary(month: String? = null): Result<ExpenseSummaryData> = safeApiCall {
        val r = apiService.getExpenseSummary(month)
        if (r.isSuccessful) {
            r.body()?.data ?: throw Exception("Data ringkasan kosong.")
        } else {
            throw Exception("Gagal memuat ringkasan pengeluaran.")
        }
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private suspend fun <T> safeApiCall(call: suspend () -> T): Result<T> = try {
        Result.Success(call())
    } catch (e: Exception) {
        Result.Error(e.message ?: "Terjadi kesalahan jaringan.")
    }

    private fun parseValidationError(errorBody: String?, fallback: String): String {
        if (errorBody.isNullOrBlank()) return fallback
        return try {
            val json = Gson().fromJson(errorBody, JsonObject::class.java)
            val errors = json.getAsJsonObject("errors")
            if (errors != null && errors.size() > 0) {
                val firstField = errors.keySet().first()
                errors.getAsJsonArray(firstField)?.get(0)?.asString ?: fallback
            } else {
                json.get("message")?.asString ?: fallback
            }
        } catch (_: Exception) {
            fallback
        }
    }
}
