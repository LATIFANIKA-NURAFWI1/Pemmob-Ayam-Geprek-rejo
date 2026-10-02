package com.pemmob.geprekrejo.ui.menu

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.CategoryItem
import com.pemmob.geprekrejo.data.model.MenuItem
import com.pemmob.geprekrejo.data.model.MenuStatusFilter
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

            // Contoh data inisial sesuai screenshot & database
            val mockCategories = listOf(
                CategoryItem(1, "Paket Nasi"),
                CategoryItem(2, "Ayam"),
                CategoryItem(3, "Minuman"),
                CategoryItem(4, "Ekstra")
            )

            val mockMenus = listOf(
                MenuItem(
                    id = 1,
                    categoryId = 1,
                    name = "Paket Nasi Geprek Dada",
                    description = "Ayam geprek bagian dada renyah dengan nasi pulen hangat dan sambal khas Rejo.",
                    price = 12000.0,
                    isAvailable = true,
                    category = mockCategories[0],
                    image = "https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600&q=80"
                ),
                MenuItem(
                    id = 2,
                    categoryId = 1,
                    name = "Paket Nasi Geprek Paha Atas",
                    description = "Ayam geprek paha atas juicy dipadukan dengan nasi putih dan lalapan segar.",
                    price = 12000.0,
                    isAvailable = true,
                    category = mockCategories[0],
                    image = "https://images.unsplash.com/photo-1562967914-608f82629710?w=600&q=80"
                ),
                MenuItem(
                    id = 3,
                    categoryId = 1,
                    name = "Paket Nasi Geprek Paha Bawah",
                    description = "Paket hemat paha bawah krispi dengan sambal korek pilihan.",
                    price = 10000.0,
                    isAvailable = true,
                    category = mockCategories[0],
                    image = "https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=600&q=80"
                ),
                MenuItem(
                    id = 4,
                    categoryId = 1,
                    name = "Paket Nasi Geprek Sayap",
                    description = "Paket renyah sayap ayam berselimut tepung rempah istimewa.",
                    price = 10000.0,
                    isAvailable = false,
                    category = mockCategories[0],
                    image = "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&q=80"
                ),
                MenuItem(
                    id = 5,
                    categoryId = 4,
                    name = "Ati Ampela Crispy",
                    description = "Ati ampela goreng bumbu gurih yang dipadukan dengan sambal geprek pedas nikmat.",
                    price = 5000.0,
                    isAvailable = true,
                    category = mockCategories[3],
                    image = "https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600&q=80"
                )
            )

            if (_uiState.value.items.isNotEmpty()) {
                _uiState.update { it.copy(isLoading = false) }
                return@launch
            }

            _uiState.update {
                it.copy(
                    isLoading = false,
                    items = mockMenus,
                    categories = mockCategories
                )
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
