package com.pemmob.geprekrejo.data.repository

import com.pemmob.geprekrejo.data.model.DashboardData
import com.pemmob.geprekrejo.data.model.DashboardStats
import com.pemmob.geprekrejo.network.ApiService
import com.pemmob.geprekrejo.util.Result

class DashboardRepository(private val apiService: ApiService) {

    suspend fun getDashboard(): Result<DashboardData> = safeApiCall {
        val response = apiService.getDashboard()
        if (response.isSuccessful) {
            response.body()?.data ?: throw Exception("Data dashboard kosong.")
        } else {
            throw Exception("Gagal memuat dashboard. (${response.code()})")
        }
    }

    suspend fun getDashboardStats(): Result<DashboardStats> = safeApiCall {
        val response = apiService.getDashboardStats()
        if (response.isSuccessful) {
            response.body()?.data ?: throw Exception("Data statistik kosong.")
        } else {
            throw Exception("Gagal memuat statistik. (${response.code()})")
        }
    }

    private suspend fun <T> safeApiCall(call: suspend () -> T): Result<T> {
        return try {
            Result.Success(call())
        } catch (e: Exception) {
            Result.Error(e.message ?: "Terjadi kesalahan jaringan.")
        }
    }
}
