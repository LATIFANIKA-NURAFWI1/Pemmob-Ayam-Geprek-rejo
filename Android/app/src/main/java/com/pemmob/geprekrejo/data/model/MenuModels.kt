package com.pemmob.geprekrejo.data.model

import com.google.gson.annotations.SerializedName

/**
 * Model data untuk Menu Makanan (MenuItem) dan Kategori (CategoryItem).
 * Sinkron dengan database Laravel (tabel `menu_items` & `categories`).
 */

data class MenuItem(
    val id: Int,
    @SerializedName("category_id") val categoryId: Int? = null,
    val name: String,
    val slug: String? = null,
    val description: String? = null,
    val image: String? = null,
    val price: Double,
    @SerializedName("is_available") val isAvailable: Boolean = true,
    @SerializedName("sort_order") val sortOrder: Int? = 0,
    val category: CategoryItem? = null,
    val stock: Int? = null
)

data class CategoryItem(
    val id: Int,
    val name: String,
    val slug: String? = null,
    @SerializedName("is_active") val isActive: Boolean = true
)

enum class MenuStatusFilter(val label: String, val value: Boolean?) {
    ALL("Semua Status", null),
    AVAILABLE("Tersedia", true),
    UNAVAILABLE("Habis", false)
}

data class MenuRequest(
    val name: String,
    val description: String?,
    val price: Double,
    @SerializedName("category_id") val categoryId: Int,
    @SerializedName("is_available") val isAvailable: Boolean
)

data class MenuListResponse(
    val success: Boolean,
    val data: MenuListData
)

data class MenuListData(
    val categories: List<CategoryItem>,
    val items: List<MenuItem>
)
