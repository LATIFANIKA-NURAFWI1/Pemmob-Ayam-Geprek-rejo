package com.pemmob.geprekrejo.ui.dashboard

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.DashboardData
import com.pemmob.geprekrejo.data.repository.DashboardRepository
import com.pemmob.geprekrejo.util.Result
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

class DashboardViewModel(private val repository: DashboardRepository) : ViewModel() {

    private val _uiState = MutableStateFlow(DashboardUiState())
    val uiState: StateFlow<DashboardUiState> = _uiState.asStateFlow()

    init { loadDashboard() }

    fun loadDashboard() {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, error = null) }
            when (val result = repository.getDashboard()) {
                is Result.Success -> _uiState.update { it.copy(isLoading = false, data = result.data) }
                is Result.Error   -> _uiState.update { it.copy(isLoading = false, error = result.message) }
                else -> {}
            }
        }
    }

    fun clearError() = _uiState.update { it.copy(error = null) }
}

data class DashboardUiState(
    val isLoading: Boolean      = false,
    val data: DashboardData?    = null,
    val error: String?          = null
)
