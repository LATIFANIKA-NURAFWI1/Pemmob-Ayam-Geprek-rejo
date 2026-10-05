package com.pemmob.geprekrejo.ui.menu

import android.net.Uri
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.CategoryItem
import com.pemmob.geprekrejo.data.model.MenuItem
import com.pemmob.geprekrejo.util.CurrencyFormatter
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

/**
 * State untuk formulir Tambah / Edit Menu
 */
data class MenuFormState(
    val menuId: Int? = null,
    val name: String = "",
    val nameError: String? = null,

    val categoryId: Int? = null,
    val categoryError: String? = null,

    val price: String = "",
    val priceError: String? = null,

    val description: String = "",
    val isAvailable: Boolean = true,

    val imageUri: Uri? = null,
    val existingImageUrl: String? = null,

    val categories: List<CategoryItem> = emptyList(),

    val isSaving: Boolean = false,
    val isSuccess: Boolean = false,
    val generalError: String? = null
) {
    val isEditMode: Boolean get() = menuId != null
}

/**
 * ViewModel untuk menangani logika formulir input Menu:
 * - Validasi input wajib (Nama, Kategori, Harga)
 * - Pembersihan input harga (hanya digit angka)
 * - Pemilihan foto lokal & penghapusan foto
 * - Mode tambah (Create) vs mode edit (Update)
 */
class MenuFormViewModel : ViewModel() {

    private val _formState = MutableStateFlow(MenuFormState())
    val formState: StateFlow<MenuFormState> = _formState.asStateFlow()

    init {
        loadCategories()
    }

    private fun loadCategories() {
        viewModelScope.launch {
            try {
                val response = com.pemmob.geprekrejo.network.RetrofitClient.apiService.getMenuList()
                if (response.isSuccessful && response.body()?.success == true) {
                    val apiCategories = response.body()!!.data.categories
                    _formState.update { it.copy(categories = apiCategories) }
                } else {
                    // Fallback
                    val defaultCategories = listOf(
                        CategoryItem(1, "Paket Nasi"),
                        CategoryItem(2, "Ayam"),
                        CategoryItem(3, "Minuman"),
                        CategoryItem(4, "Camilan"),
                        CategoryItem(5, "Ekstra")
                    )
                    _formState.update { it.copy(categories = defaultCategories) }
                }
            } catch (e: Exception) {
                // Fallback
                val defaultCategories = listOf(
                    CategoryItem(1, "Paket Nasi"),
                    CategoryItem(2, "Ayam"),
                    CategoryItem(3, "Minuman"),
                    CategoryItem(4, "Camilan"),
                    CategoryItem(5, "Ekstra")
                )
                _formState.update { it.copy(categories = defaultCategories) }
            }
        }
    }

    /**
     * Mengisi form dengan data menu yang sudah ada (Mode Edit)
     */
    fun initForEdit(menuItem: MenuItem) {
        _formState.update {
            it.copy(
                menuId = menuItem.id,
                name = menuItem.name,
                nameError = null,
                categoryId = menuItem.categoryId,
                categoryError = null,
                price = menuItem.price.toLong().toString(),
                priceError = null,
                description = menuItem.description ?: "",
                isAvailable = menuItem.isAvailable,
                imageUri = null,
                existingImageUrl = menuItem.image,
                generalError = null
            )
        }
    }

    /**
     * Mereset form untuk penambahan menu baru (Mode Tambah)
     */
    fun resetForCreate() {
        _formState.update {
            MenuFormState(categories = it.categories)
        }
    }

    fun onNameChange(value: String) {
        _formState.update {
            it.copy(
                name = value,
                nameError = if (value.isBlank()) "Nama menu wajib diisi" else null
            )
        }
    }

    fun onCategorySelect(categoryId: Int) {
        _formState.update {
            it.copy(
                categoryId = categoryId,
                categoryError = null
            )
        }
    }

    fun onPriceChange(value: String) {
        val cleanDigits = CurrencyFormatter.cleanDigits(value)
        _formState.update {
            it.copy(
                price = cleanDigits,
                priceError = when {
                    cleanDigits.isBlank() -> "Harga wajib diisi"
                    cleanDigits.toLongOrNull() == null || cleanDigits.toLong() <= 0 -> "Harga harus lebih dari 0"
                    else -> null
                }
            )
        }
    }

    fun onDescriptionChange(value: String) {
        _formState.update { it.copy(description = value) }
    }

    fun onAvailabilityChange(value: Boolean) {
        _formState.update { it.copy(isAvailable = value) }
    }

    fun onImageSelected(uri: Uri?) {
        _formState.update { it.copy(imageUri = uri) }
    }

    fun onDeletePhoto() {
        _formState.update {
            it.copy(
                imageUri = null,
                existingImageUrl = null
            )
        }
    }

    /**
     * Validasi kelengkapan form sebelum submit
     */
    fun validate(): Boolean {
        val currentState = _formState.value
        var isValid = true

        var nameErr: String? = null
        if (currentState.name.isBlank()) {
            nameErr = "Nama menu tidak boleh kosong"
            isValid = false
        }

        var catErr: String? = null
        if (currentState.categoryId == null) {
            catErr = "Silakan pilih salah satu kategori"
            isValid = false
        }

        var priceErr: String? = null
        val priceLong = currentState.price.toLongOrNull()
        if (currentState.price.isBlank()) {
            priceErr = "Harga tidak boleh kosong"
            isValid = false
        } else if (priceLong == null || priceLong <= 0) {
            priceErr = "Harga harus bernilai positif"
            isValid = false
        }

        _formState.update {
            it.copy(
                nameError = nameErr,
                categoryError = catErr,
                priceError = priceErr
            )
        }

        return isValid
    }

    /**
     * Menyimpan data menu baru atau perubahan menu
     */
    fun submit(context: android.content.Context, onSuccess: (MenuItem) -> Unit) {
        if (!validate()) return

        viewModelScope.launch {
            _formState.update { it.copy(isSaving = true, generalError = null) }

            try {
                val current = _formState.value
                var base64Image: String? = null
                if (current.imageUri != null) {
                    try {
                        val inputStream = context.contentResolver.openInputStream(current.imageUri)
                        val bytes = inputStream?.readBytes()
                        inputStream?.close()
                        if (bytes != null) {
                            base64Image = android.util.Base64.encodeToString(bytes, android.util.Base64.NO_WRAP)
                        }
                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                }

                val request = com.pemmob.geprekrejo.data.model.MenuRequest(
                    name = current.name.trim(),
                    description = current.description.ifBlank { null },
                    price = current.price.toDoubleOrNull() ?: 0.0,
                    categoryId = current.categoryId ?: 0,
                    isAvailable = current.isAvailable,
                    image = base64Image
                )

                val response = if (current.isEditMode) {
                    com.pemmob.geprekrejo.network.RetrofitClient.apiService.updateMenu(current.menuId!!, request)
                } else {
                    com.pemmob.geprekrejo.network.RetrofitClient.apiService.createMenu(request)
                }

                if (response.isSuccessful && response.body()?.success == true) {
                    _formState.update { it.copy(isSaving = false, isSuccess = true) }
                    response.body()?.data?.let { onSuccess(it) }
                } else {
                    var errorMsg = "Gagal menyimpan menu"
                    try {
                        val errorBody = response.errorBody()?.string()
                        if (errorBody != null) {
                            val jsonObject = org.json.JSONObject(errorBody)
                            if (jsonObject.has("message")) {
                                errorMsg = jsonObject.getString("message")
                            }
                        }
                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                    _formState.update {
                        it.copy(
                            isSaving = false,
                            generalError = errorMsg
                        )
                    }
                }
            } catch (e: Exception) {
                _formState.update {
                    it.copy(
                        isSaving = false,
                        generalError = "Gagal menyimpan menu: ${e.localizedMessage ?: "Terjadi kesalahan"}"
                    )
                }
            }
        }
    }
}
