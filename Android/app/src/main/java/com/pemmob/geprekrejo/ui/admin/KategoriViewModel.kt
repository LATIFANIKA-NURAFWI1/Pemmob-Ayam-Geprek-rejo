package com.pemmob.geprekrejo.ui.admin

import androidx.lifecycle.ViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update

data class Kategori(
    val id: Int,
    val name: String,
    val isActive: Boolean
)

data class KategoriState(
    val isLoading: Boolean = false,
    val kategoriList: List<Kategori> = emptyList(),
    val error: String? = null
)

class KategoriViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(KategoriState())
    val uiState: StateFlow<KategoriState> = _uiState.asStateFlow()

    init {
        // Mock data fetch
        _uiState.update { 
            it.copy(
                isLoading = false,
                kategoriList = listOf(
                    Kategori(1, "Makanan", true),
                    Kategori(2, "Minuman", true)
                )
            )
        }
    }

    fun addKategori(name: String) {
        val currentList = _uiState.value.kategoriList.toMutableList()
        val newId = (currentList.maxOfOrNull { it.id } ?: 0) + 1
        currentList.add(Kategori(newId, name, true))
        _uiState.update { it.copy(kategoriList = currentList) }
    }

    fun updateKategori(id: Int, name: String) {
        val currentList = _uiState.value.kategoriList.map {
            if (it.id == id) it.copy(name = name) else it
        }
        _uiState.update { it.copy(kategoriList = currentList) }
    }

    fun deleteKategori(id: Int) {
        val currentList = _uiState.value.kategoriList.filter { it.id != id }
        _uiState.update { it.copy(kategoriList = currentList) }
    }
}
