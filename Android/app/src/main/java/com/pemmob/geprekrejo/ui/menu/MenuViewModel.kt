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
        val updated = _uiState.value.items.map { item ->
            if (item.id == menuItem.id) item.copy(isAvailable = !item.isAvailable) else item
        }
        val statusText = if (!menuItem.isAvailable) "tersedia" else "habis"
        _uiState.update {
            it.copy(
                items = updated,
                userMessage = "Status menu '${menuItem.name}' diubah menjadi $statusText."
            )
        }
    }

    fun saveMenu(item: MenuItem) {
        val currentList = _uiState.value.items.toMutableList()
        val existingIndex = currentList.indexOfFirst { it.id == item.id }

        if (existingIndex != -1 && item.id != 0) {
            // Mode Edit: Perbarui item yang sudah ada
            currentList[existingIndex] = item
            _uiState.update {
                it.copy(
                    items = currentList,
                    userMessage = "Menu '${item.name}' berhasil diperbarui."
                )
            }
        } else {
            // Mode Tambah Baru: Buat ID baru & letakkan di baris paling atas
            val newId = (currentList.maxOfOrNull { it.id } ?: 0) + 1
            val newItem = item.copy(id = newId)
            currentList.add(0, newItem)
            _uiState.update {
                it.copy(
                    items = currentList,
                    userMessage = "Menu '${newItem.name}' berhasil ditambahkan."
                )
            }
        }
    }

    fun deleteMenu(menuItem: MenuItem) {
        val updated = _uiState.value.items.filter { it.id != menuItem.id }
        _uiState.update {
            it.copy(
                items = updated,
                userMessage = "Menu '${menuItem.name}' berhasil dihapus."
            )
        }
    }

    fun clearUserMessage() {
        _uiState.update { it.copy(userMessage = null) }
    }
}
