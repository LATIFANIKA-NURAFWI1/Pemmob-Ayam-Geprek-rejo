package com.pemmob.geprekrejo.ui.order

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.OrderHistoryData
import com.pemmob.geprekrejo.data.repository.OrderRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

data class OrderUiState(
    val isLoading: Boolean = false,
    val mode: String = "harian", // "harian" or "bulanan"
    val search: String = "",
    val tanggal: String = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date()),
    val bulan: String = SimpleDateFormat("yyyy-MM", Locale.getDefault()).format(Date()),
    val data: OrderHistoryData? = null,
    val errorMessage: String? = null
)

class OrderViewModel(private val repository: OrderRepository) : ViewModel() {
    private val _uiState = MutableStateFlow(OrderUiState())
    val uiState: StateFlow<OrderUiState> = _uiState.asStateFlow()

    init {
        loadData()
    }

    fun setMode(mode: String) {
        _uiState.update { it.copy(mode = mode, search = "", data = null) }
        loadData()
    }

    fun setSearch(search: String) {
        _uiState.update { it.copy(search = search) }
        if (_uiState.value.mode == "harian") {
            loadData()
        }
    }

    fun setTanggal(tanggal: String) {
        _uiState.update { it.copy(tanggal = tanggal) }
        if (_uiState.value.mode == "harian") {
            loadData()
        }
    }

    fun setBulan(bulan: String) {
        _uiState.update { it.copy(bulan = bulan) }
        if (_uiState.value.mode == "bulanan") {
            loadData()
        }
    }

    fun loadData(page: Int = 1) {
        val state = _uiState.value
        _uiState.update { it.copy(isLoading = true, errorMessage = null) }
        
        viewModelScope.launch {
            val result = repository.getOrderHistory(
                mode = state.mode,
                search = if (state.search.isNotBlank()) state.search else null,
                tanggal = if (state.mode == "harian") state.tanggal else null,
                bulan = if (state.mode == "bulanan") state.bulan else null,
                page = page
            )
            
            result.onSuccess { response ->
                if (response.success) {
                    _uiState.update { it.copy(isLoading = false, data = response.data) }
                } else {
                    _uiState.update { it.copy(isLoading = false, errorMessage = "Failed to load data") }
                }
            }.onFailure { e ->
                _uiState.update { it.copy(isLoading = false, errorMessage = e.message) }
            }
        }
    }

    fun clearError() {
        _uiState.update { it.copy(errorMessage = null) }
    }
}
