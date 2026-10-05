package com.pemmob.geprekrejo.ui.menu

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.RecipeItem
import com.pemmob.geprekrejo.data.model.RecipeSyncRequest
import com.pemmob.geprekrejo.data.model.StockItem
import com.pemmob.geprekrejo.network.RetrofitClient
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

data class RecipeUiState(
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val menuId: Int? = null,
    val menuName: String = "",
    val recipes: List<RecipeItem> = emptyList(),
    val availableIngredients: List<StockItem> = emptyList(),
    val error: String? = null,
    val successMessage: String? = null
) {
    val totalHpp: Double
        get() = recipes.sumOf { it.qtyUsed * (it.unitCost ?: 0.0) }
}

class RecipeViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(RecipeUiState())
    val uiState: StateFlow<RecipeUiState> = _uiState.asStateFlow()

    fun loadData(menuId: Int) {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, menuId = menuId, error = null) }
            try {
                // Fetch stock ingredients
                val stockResponse = RetrofitClient.apiService.getStockList()
                val ingredients = if (stockResponse.isSuccessful) {
                    stockResponse.body()?.data?.items ?: emptyList()
                } else {
                    emptyList()
                }

                // Fetch current recipes
                val recipeResponse = RetrofitClient.apiService.getMenuRecipes(menuId)
                if (recipeResponse.isSuccessful && recipeResponse.body()?.success == true) {
                    val data = recipeResponse.body()!!.data
                    _uiState.update {
                        it.copy(
                            isLoading = false,
                            menuName = data.menu.name,
                            recipes = data.recipes,
                            availableIngredients = ingredients
                        )
                    }
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Gagal memuat resep") }
                }
            } catch (e: Exception) {
                _uiState.update { it.copy(isLoading = false, error = e.localizedMessage ?: "Terjadi kesalahan koneksi") }
            }
        }
    }

    fun addIngredient(ingredient: StockItem) {
        val currentRecipes = _uiState.value.recipes.toMutableList()
        if (currentRecipes.none { it.stockIngredientId == ingredient.id }) {
            currentRecipes.add(
                RecipeItem(
                    stockIngredientId = ingredient.id,
                    ingredientName = ingredient.name,
                    unit = ingredient.unit,
                    unitCost = ingredient.unitCost,
                    qtyUsed = 0.0,
                    hppContribution = 0.0
                )
            )
            _uiState.update { it.copy(recipes = currentRecipes) }
        }
    }

    fun updateQuantity(ingredientId: Int, qty: Double) {
        val currentRecipes = _uiState.value.recipes.map {
            if (it.stockIngredientId == ingredientId) {
                it.copy(
                    qtyUsed = qty,
                    hppContribution = qty * (it.unitCost ?: 0.0)
                )
            } else it
        }
        _uiState.update { it.copy(recipes = currentRecipes) }
    }

    fun removeIngredient(ingredientId: Int) {
        val currentRecipes = _uiState.value.recipes.filter { it.stockIngredientId != ingredientId }
        _uiState.update { it.copy(recipes = currentRecipes) }
    }

    fun saveRecipes() {
        val menuId = _uiState.value.menuId ?: return
        viewModelScope.launch {
            _uiState.update { it.copy(isSaving = true, error = null) }
            try {
                val request = RecipeSyncRequest(ingredients = _uiState.value.recipes)
                val response = RetrofitClient.apiService.syncMenuRecipes(menuId, request)
                if (response.isSuccessful && response.body()?.success == true) {
                    _uiState.update {
                        it.copy(isSaving = false, successMessage = "Resep berhasil disimpan!")
                    }
                } else {
                    _uiState.update { it.copy(isSaving = false, error = "Gagal menyimpan resep") }
                }
            } catch (e: Exception) {
                _uiState.update { it.copy(isSaving = false, error = e.localizedMessage ?: "Terjadi kesalahan koneksi") }
            }
        }
    }

    fun clearMessages() {
        _uiState.update { it.copy(error = null, successMessage = null) }
    }
}
