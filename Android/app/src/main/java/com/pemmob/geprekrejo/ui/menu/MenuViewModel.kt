package com.pemmob.geprekrejo.ui.menu

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.CategoryItem
import com.pemmob.geprekrejo.data.model.MenuItem
import com.pemmob.geprekrejo.data.model.MenuStatusFilter
import com.pemmob.geprekrejo.network.RetrofitClient
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

/**
 * UI State untuk Halaman Daftar Menu
 */
data class MenuListUiState(
    val isLoading: Boolean = false,
    val items: List<MenuItem> = emptyList(),
    val categories: List<CategoryItem> = emptyList(),
    val searchQuery: String = "",
    val selectedCategoryId: Int? = null,
    val selectedStatus: MenuStatusFilter = MenuStatusFilter.ALL,
    val error: String? = null,
    val userMessage: String? = null
) {
    /**
     * Memfilter daftar menu berdasarkan query nama, ID kategori, dan status ketersediaan.
     */
    val filteredItems: List<MenuItem>
        get() = items.filter { item ->
            val matchQuery = searchQuery.isBlank() || item.name.contains(searchQuery, ignoreCase = true)
            val matchCategory = selectedCategoryId == null || item.categoryId == selectedCategoryId
            val matchStatus = when (selectedStatus) {
                MenuStatusFilter.ALL -> true
                MenuStatusFilter.AVAILABLE -> item.isAvailable
                MenuStatusFilter.UNAVAILABLE -> !item.isAvailable
            }
            matchQuery && matchCategory && matchStatus
        }
}

/**
 * ViewModel untuk mengelola daftar menu, pencarian, filter, dan mutasi data (toggle & delete).
 */
class MenuViewModel : ViewModel() {

    private val _uiState = MutableStateFlow(MenuListUiState())
    val uiState: StateFlow<MenuListUiState> = _uiState.asStateFlow()

    init {
        loadData()
    }

    fun loadData() {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, error = null) }

            try {
                val response = RetrofitClient.apiService.getMenuList()
                if (response.isSuccessful && response.body()?.success == true) {
                    val body = response.body()!!
                    _uiState.update {
                        it.copy(
                            isLoading = false,
                            items = body.data.items,
                            categories = body.data.categories
                        )
                    }
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Gagal memuat daftar menu") }
                }
            } catch (e: Exception) {
                _uiState.update { it.copy(isLoading = false, error = e.localizedMessage ?: "Terjadi kesalahan koneksi") }
            }
        }
    }

    fun onSearchQueryChange(query: String) {
        _uiState.update { it.copy(searchQuery = query) }
    }

    fun onCategorySelect(categoryId: Int?) {
        _uiState.update { it.copy(selectedCategoryId = categoryId) }
    }

    fun onStatusSelect(status: MenuStatusFilter) {
        _uiState.update { it.copy(selectedStatus = status) }
    }

    fun toggleAvailability(menuItem: MenuItem) {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.apiService.toggleMenuStatus(menuItem.id)
                if (response.isSuccessful && response.body()?.success == true) {
                    val updatedMenu = response.body()?.data
                    if (updatedMenu != null) {
                        val updated = _uiState.value.items.map { item ->
                            if (item.id == updatedMenu.id) updatedMenu else item
                        }
                        val statusText = if (updatedMenu.isAvailable) "tersedia" else "habis"
                        _uiState.update {
                            it.copy(
                                items = updated,
                                userMessage = "Status menu '${menuItem.name}' diubah menjadi $statusText."
                            )
                        }
                    }
                } else {
                    _uiState.update { it.copy(userMessage = "Gagal mengubah status menu.") }
                }
            } catch (e: Exception) {
                _uiState.update { it.copy(userMessage = "Terjadi kesalahan koneksi.") }
            }
        }
    }

    fun saveMenu(item: MenuItem) {
        // Karena proses API dilakukan di MenuFormViewModel,
        // Di sini kita tinggal memuat ulang data dari server.
        loadData()
        _uiState.update { it.copy(userMessage = "Menu '${item.name}' berhasil disimpan.") }
    }

    fun deleteMenu(menuItem: MenuItem) {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.apiService.deleteMenu(menuItem.id)
                if (response.isSuccessful) {
                    val updated = _uiState.value.items.filter { it.id != menuItem.id }
                    _uiState.update {
                        it.copy(
                            items = updated,
                            userMessage = "Menu '${menuItem.name}' berhasil dihapus."
                        )
                    }
                } else {
                    _uiState.update { it.copy(userMessage = "Gagal menghapus menu.") }
                }
            } catch (e: Exception) {
                _uiState.update { it.copy(userMessage = "Terjadi kesalahan koneksi.") }
            }
        }
    }

    fun clearUserMessage() {
        _uiState.update { it.copy(userMessage = null) }
    }
}
