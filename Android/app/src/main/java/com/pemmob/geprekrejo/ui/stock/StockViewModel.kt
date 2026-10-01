package com.pemmob.geprekrejo.ui.stock

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.StockItem
import com.pemmob.geprekrejo.network.ApiService
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

/**
 * Filter Status Ketersediaan Stok
 */
enum class StockStatusFilter(val label: String) {
    ALL("Semua Status"),
    SAFE("Stok Aman"),
    LOW("Stok Rendah")
}

/**
 * UI State untuk Manajemen Stok Bahan Baku
 */
data class StockUiState(
    val isLoading: Boolean = false,
    val items: List<StockItem> = emptyList(),
    val searchQuery: String = "",
    val selectedStatusFilter: StockStatusFilter = StockStatusFilter.ALL,
    val error: String? = null,
    val userMessage: String? = null,

    // Dialog Tambah / Edit
    val isFormOpen: Boolean = false,
    val editingItem: StockItem? = null,

    // Dialog Restock
    val isRestockOpen: Boolean = false,
    val restockTarget: StockItem? = null
) {
    // ── Perhitungan Statistik Ringkasan (Cards) ──────────────────────────────
    val totalCount: Int get() = items.size
    val lowStockCount: Int get() = items.count { it.currentStock < it.minimumStock }
    val safeStockCount: Int get() = totalCount - lowStockCount

    // ── Filter Dinamis Berdasarkan Pencarian & Status ────────────────────────
    val filteredItems: List<StockItem>
        get() = items.filter { item ->
            val matchQuery = searchQuery.isBlank() || item.name.contains(searchQuery, ignoreCase = true)
            val isLow = item.currentStock < item.minimumStock
            val matchStatus = when (selectedStatusFilter) {
                StockStatusFilter.ALL -> true
                StockStatusFilter.SAFE -> !isLow
                StockStatusFilter.LOW -> isLow
            }
            matchQuery && matchStatus
        }
}

/**
 * ViewModel untuk mengelola daftar stok, filter pencarian, mutasi restock,
 * penambahan bahan baru, dan pengeditan bahan.
 */
class StockViewModel(private val apiService: ApiService? = null) : ViewModel() {

    private val _uiState = MutableStateFlow(StockUiState())
    val uiState: StateFlow<StockUiState> = _uiState.asStateFlow()

    init {
        loadStock()
    }

    fun loadStock() {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, error = null) }

            // Coba panggil API backend jika apiService tersedia
            if (apiService != null) {
                try {
                    val response = apiService.getStockList()
                    if (response.isSuccessful && response.body()?.data != null) {
                        val backendItems = response.body()!!.data!!.items
                        if (backendItems.isNotEmpty()) {
                            _uiState.update {
                                it.copy(isLoading = false, items = backendItems)
                            }
                            return@launch
                        }
                    }
                } catch (_: Exception) {
                    // Jika jaringan offline, fallback ke data lokal/mock
                }
            }

            // Jika data di memori sudah ada, pertahankan agar tidak tertimpa
            if (_uiState.value.items.isNotEmpty()) {
                _uiState.update { it.copy(isLoading = false) }
                return@launch
            }

            // Inisialisasi data bahan baku sesuai database & screenshot web
            val initialStock = listOf(
                StockItem(1, "Air Mineral Botol", "botol", 100.0, 20.0, 2500.0, false),
                StockItem(2, "Ayam Potong", "gram", 46800.0, 5000.0, 38.0, false),
                StockItem(3, "Bawang Putih", "gram", 2880.0, 300.0, 40.0, false),
                StockItem(4, "Beras", "gram", 46550.0, 5000.0, 13.0, false),
                StockItem(5, "Cabai Rawit Merah", "gram", 5000.0, 500.0, 80.0, false),
                StockItem(6, "Garam", "gram", 1875.0, 200.0, 10.0, false),
                StockItem(7, "Minyak Goreng", "ml", 28500.0, 3000.0, 22.0, false),
                StockItem(8, "Tepung Terigu", "gram", 19200.0, 2000.0, 13.0, false),
                StockItem(9, "Tepung Bumbu Crispy", "gram", 9500.0, 1000.0, 25.0, false),
                StockItem(10, "Selada", "kg", 8.0, 10.0, 15000.0, true), // Contoh stok rendah
                StockItem(11, "Timun", "kg", 12.0, 5.0, 8000.0, false)
            )

            _uiState.update {
                it.copy(
                    isLoading = false,
                    items = initialStock
                )
            }
        }
    }

    fun onSearchChange(query: String) {
        _uiState.update { it.copy(searchQuery = query) }
    }

    fun onStatusFilterChange(filter: StockStatusFilter) {
        _uiState.update { it.copy(selectedStatusFilter = filter) }
    }

    // ── Logika Modal Tambah / Edit Bahan ─────────────────────────────────────
    fun openCreateDialog() {
        _uiState.update { it.copy(isFormOpen = true, editingItem = null) }
    }

    fun openEditDialog(item: StockItem) {
        _uiState.update { it.copy(isFormOpen = true, editingItem = item) }
    }

    fun closeFormDialog() {
        _uiState.update { it.copy(isFormOpen = false, editingItem = null) }
    }

    /**
     * Menyimpan bahan baru atau memperbarui bahan yang diedit
     */
    fun saveIngredient(
        name: String,
        unit: String,
        unitCost: Double,
        currentStock: Double,
        minimumStock: Double
    ) {
        val currentList = _uiState.value.items.toMutableList()
        val editing = _uiState.value.editingItem
        val isLow = currentStock < minimumStock

        if (editing != null) {
            // Mode Edit
            val index = currentList.indexOfFirst { it.id == editing.id }
            if (index != -1) {
                currentList[index] = editing.copy(
                    name = name.trim(),
                    unit = unit.trim(),
                    unitCost = unitCost,
                    currentStock = currentStock,
                    minimumStock = minimumStock,
                    isCritical = isLow
                )
                _uiState.update {
                    it.copy(
                        items = currentList,
                        isFormOpen = false,
                        editingItem = null,
                        userMessage = "Bahan '${name.trim()}' berhasil diperbarui."
                    )
                }
            }
        } else {
            // Mode Tambah Bahan Baru
            val newId = (currentList.maxOfOrNull { it.id } ?: 0) + 1
            val newItem = StockItem(
                id = newId,
                name = name.trim(),
                unit = unit.trim(),
                unitCost = unitCost,
                currentStock = currentStock,
                minimumStock = minimumStock,
                isCritical = isLow
            )
            currentList.add(0, newItem)
            _uiState.update {
                it.copy(
                    items = currentList,
                    isFormOpen = false,
                    editingItem = null,
                    userMessage = "Bahan baru '${newItem.name}' berhasil ditambahkan."
                )
            }
        }
    }

    // ── Logika Modal Restock ─────────────────────────────────────────────────
    fun openRestockDialog(item: StockItem) {
        _uiState.update { it.copy(isRestockOpen = true, restockTarget = item) }
    }

    fun closeRestockDialog() {
        _uiState.update { it.copy(isRestockOpen = false, restockTarget = null) }
    }

    /**
     * Menambahkan stok bahan baku secara otomatis
     * dan memperbarui status kritis/aman secara instan
     */
    fun applyRestock(addedQty: Double) {
        val target = _uiState.value.restockTarget ?: return
        val currentList = _uiState.value.items.toMutableList()
        val index = currentList.indexOfFirst { it.id == target.id }

        if (index != -1) {
            val updatedStock = (target.currentStock + addedQty).coerceAtLeast(0.0)
            val isLow = updatedStock < target.minimumStock
            currentList[index] = target.copy(
                currentStock = updatedStock,
                isCritical = isLow
            )

            _uiState.update {
                it.copy(
                    items = currentList,
                    isRestockOpen = false,
                    restockTarget = null,
                    userMessage = "Stok '${target.name}' bertambah $addedQty ${target.unit}. Total sekarang: $updatedStock ${target.unit}."
                )
            }
        }
    }

    // ── Logika Hapus Bahan ───────────────────────────────────────────────────
    fun deleteIngredient(item: StockItem) {
        val currentList = _uiState.value.items.filter { it.id != item.id }
        _uiState.update {
            it.copy(
                items = currentList,
                userMessage = "Bahan '${item.name}' berhasil dihapus."
            )
        }
    }

    fun clearUserMessage() {
        _uiState.update { it.copy(userMessage = null) }
    }

    fun clearError() {
        _uiState.update { it.copy(error = null) }
    }
}
