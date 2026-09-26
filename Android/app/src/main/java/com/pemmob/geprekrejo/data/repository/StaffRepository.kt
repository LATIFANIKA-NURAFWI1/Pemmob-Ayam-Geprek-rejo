package com.pemmob.geprekrejo.data.repository

import com.google.gson.Gson
import com.google.gson.JsonObject
import com.pemmob.geprekrejo.data.model.*
import com.pemmob.geprekrejo.network.ApiService
import com.pemmob.geprekrejo.util.PaginatedResult
import com.pemmob.geprekrejo.util.Result

class StaffRepository(private val apiService: ApiService) {

    // ── Staff ─────────────────────────────────────────────────────────────────

    suspend fun getStaffList(search: String? = null, page: Int = 1): Result<PaginatedResult<StaffItem>> =
        safeApiCall {
            val response = apiService.getStaffList(search = search, page = page)
            if (response.isSuccessful) {
                val body = response.body()!!
                PaginatedResult(body.data, body.meta.currentPage, body.meta.lastPage, body.meta.total)
            } else throw Exception("Gagal memuat staf. (${response.code()})")
        }

    suspend fun getActiveStaffList(): Result<List<ActiveStaffItem>> = safeApiCall {
        val r = apiService.getActiveStaffList()
        if (r.isSuccessful) r.body()?.data ?: emptyList()
        else throw Exception("Gagal memuat staf aktif.")
    }

    suspend fun createStaff(request: StaffRequest): Result<StaffItem> = safeApiCall {
        val r = apiService.createStaff(request)
        if (r.isSuccessful) r.body()?.data ?: throw Exception("Data staf kosong.")
        else throw Exception(parseValidationError(r.errorBody()?.string(), "Gagal membuat staf."))
    }

    suspend fun updateStaff(id: Int, request: StaffRequest): Result<StaffItem> = safeApiCall {
        val r = apiService.updateStaff(id, request)
        if (r.isSuccessful) r.body()?.data ?: throw Exception("Data staf kosong.")
        else throw Exception(parseValidationError(r.errorBody()?.string(), "Gagal memperbarui staf."))
    }

    suspend fun deleteStaff(id: Int): Result<Unit> = safeApiCall {
        val r = apiService.deleteStaff(id)
        if (!r.isSuccessful) throw Exception("Gagal menghapus staf. (${r.code()})")
    }

    suspend fun toggleStaffActive(id: Int): Result<Boolean> = safeApiCall {
        val r = apiService.toggleStaffActive(id)
        if (r.isSuccessful) r.body()?.isActive ?: throw Exception("Response tidak valid.")
        else throw Exception("Gagal mengubah status staf.")
    }

    // ── Shift ─────────────────────────────────────────────────────────────────

    suspend fun getShiftList(shiftDate: String? = null, page: Int = 1): Result<PaginatedResult<ShiftItem>> =
        safeApiCall {
            val response = apiService.getShiftList(shiftDate = shiftDate, page = page)
            if (response.isSuccessful) {
                val body = response.body()!!
                PaginatedResult(body.data, body.meta.currentPage, body.meta.lastPage, body.meta.total)
            } else throw Exception("Gagal memuat shift. (${response.code()})")
        }

    suspend fun createShift(request: ShiftRequest): Result<ShiftItem> = safeApiCall {
        val r = apiService.createShift(request)
        if (r.isSuccessful) r.body()?.data ?: throw Exception("Data shift kosong.")
        else throw Exception(parseValidationError(r.errorBody()?.string(), "Gagal membuat shift."))
    }

    suspend fun updateShift(id: Int, request: ShiftRequest): Result<ShiftItem> = safeApiCall {
        val r = apiService.updateShift(id, request)
        if (r.isSuccessful) r.body()?.data ?: throw Exception("Data shift kosong.")
        else throw Exception(parseValidationError(r.errorBody()?.string(), "Gagal memperbarui shift."))
    }

    suspend fun deleteShift(id: Int): Result<Unit> = safeApiCall {
        val r = apiService.deleteShift(id)
        if (!r.isSuccessful) throw Exception("Gagal menghapus shift. (${r.code()})")
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private suspend fun <T> safeApiCall(call: suspend () -> T): Result<T> = try {
        Result.Success(call())
    } catch (e: Exception) {
        Result.Error(e.message ?: "Kesalahan tidak terduga.")
    }

    /**
     * Parse Laravel 422 validation error JSON response.
     * Expected format: {"message":"...","errors":{"field":["error message"]}}
     * Returns the first validation error message, or the fallback string.
     */
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

