package com.pemmob.geprekrejo.ui.stock

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.StockItem
import com.pemmob.geprekrejo.data.model.StockListData
import com.pemmob.geprekrejo.network.ApiService
import com.pemmob.geprekrejo.util.Result
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

class StockViewModel(private val apiService: ApiService) : ViewModel() {

    private val _uiState = MutableStateFlow(StockUiState())
    val uiState: StateFlow<StockUiState> = _uiState.asStateFlow()

    init { loadStock() }

    fun loadStock() {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, error = null) }
            try {
                val response = apiService.getStockList()
                if (response.isSuccessful) {
                    val data = response.body()?.data
                    _uiState.update {
                        it.copy(
                            isLoading = false,
                            items = data?.items ?: emptyList(),
                            criticalCount = data?.criticalCount ?: 0
                        )
                    }
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Gagal memuat stok. (${response.code()})") }
                }
            } catch (e: Exception) {
                _uiState.update { it.copy(isLoading = false, error = e.message ?: "Kesalahan jaringan.") }
            }
        }
    }

    fun clearError() = _uiState.update { it.copy(error = null) }
}

data class StockUiState(
    val isLoading: Boolean        = false,
    val items: List<StockItem>    = emptyList(),
    val criticalCount: Int        = 0,
    val error: String?            = null
)
