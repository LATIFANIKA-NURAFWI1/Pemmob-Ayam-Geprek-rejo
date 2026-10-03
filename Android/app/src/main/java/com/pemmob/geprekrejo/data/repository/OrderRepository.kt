package com.pemmob.geprekrejo.data.repository

import com.pemmob.geprekrejo.data.model.OrderHistoryResponse
import com.pemmob.geprekrejo.network.ApiService
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class OrderRepository(private val apiService: ApiService) {
    suspend fun getOrderHistory(
        mode: String,
        search: String? = null,
        tanggal: String? = null,
        bulan: String? = null,
        page: Int = 1
    ): Result<OrderHistoryResponse> = withContext(Dispatchers.IO) {
        try {
            val response = apiService.getOrderHistory(mode, search, tanggal, bulan, page)
            if (response.isSuccessful && response.body() != null) {
                Result.success(response.body()!!)
            } else {
                Result.failure(Exception("Failed to load order history: ${response.message()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
