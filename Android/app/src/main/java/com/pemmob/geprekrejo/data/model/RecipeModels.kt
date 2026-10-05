package com.pemmob.geprekrejo.data.model

import com.google.gson.annotations.SerializedName

data class RecipeResponse(
    val success: Boolean,
    val data: RecipeData
)

data class RecipeData(
    val menu: MenuInfo,
    val recipes: List<RecipeItem>
)

data class MenuInfo(
    val id: Int,
    val name: String
)

data class RecipeItem(
    val id: Int? = null,
    @SerializedName("stock_ingredient_id") val stockIngredientId: Int,
    @SerializedName("ingredient_name") val ingredientName: String? = null,
    val unit: String? = null,
    @SerializedName("unit_cost") val unitCost: Double? = null,
    @SerializedName("qty_used") val qtyUsed: Double,
    @SerializedName("hpp_contribution") val hppContribution: Double? = null
)

data class RecipeSyncRequest(
    val ingredients: List<RecipeItem>
)
