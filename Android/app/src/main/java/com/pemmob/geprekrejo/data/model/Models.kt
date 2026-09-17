package com.pemmob.geprekrejo.data.model

import com.google.gson.annotations.SerializedName

// ─────────────────────────────────────────────────────────────────────────────
// Semua data class Kotlin yang memetakan JSON response dari Laravel API.
// SerializedName digunakan karena konvensi snake_case Laravel ≠ camelCase Kotlin.
// ─────────────────────────────────────────────────────────────────────────────

// ── Auth ──────────────────────────────────────────────────────────────────────

data class LoginRequest(val email: String, val password: String)

data class LoginResponse(
    val success: Boolean,
    val message: String,
    val data: LoginData?
)

data class LoginData(
    val token: String,
    val user: UserInfo
)

// ── Generic Response Wrapper ──────────────────────────────────────────────────

data class ApiResponse<T>(
    val success: Boolean,
    val message: String? = null,
    val data: T? = null
)

data class PaginationMeta(
    @SerializedName("current_page") val currentPage: Int,
    @SerializedName("last_page")    val lastPage: Int,
    @SerializedName("per_page")     val perPage: Int,
    val total: Int
)

// ── Dashboard ─────────────────────────────────────────────────────────────────

data class DashboardStats(
    @SerializedName("total_pesanan") val totalPesanan: Int,
    @SerializedName("paid_count")    val paidCount: Int,
    @SerializedName("pending_count") val pendingCount: Int,
    val omset: Double,
    @SerializedName("gross_profit")  val grossProfit: Double,
    @SerializedName("menu_aktif")    val menuAktif: Int,
    @SerializedName("stok_kritis")   val stokKritis: Int
)

data class RecentOrderItem(
    val id: Int,
    @SerializedName("order_number") val orderNumber: String,
    @SerializedName("queue_number") val queueNumber: Int,
    val status: String,
    val type: String,
    @SerializedName("total_amount") val totalAmount: Double,
    @SerializedName("created_at")   val createdAt: String,
    val items: List<OrderItemDetail>
)

data class OrderItemDetail(
    val name: String,
    val quantity: Int,
    val subtotal: Double
)

data class TopMenuItem(
    @SerializedName("menu_item_id")  val menuItemId: Int,
    val name: String,
    @SerializedName("total_terjual") val totalTerjual: Int
)

/** Agregat satu response untuk seluruh halaman dashboard */
data class DashboardData(
    val stats: DashboardStats,
    @SerializedName("recent_orders") val recentOrders: List<RecentOrderItem>,
    @SerializedName("top_menus")     val topMenus: List<TopMenuItem>
)

// ── User / Staff ──────────────────────────────────────────────────────────────

data class UserInfo(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    @SerializedName("is_active") val isActive: Boolean,
    val initials: String? = null
)

data class StaffItem(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    @SerializedName("is_active")  val isActive: Boolean,
    val initials: String,
    @SerializedName("created_at") val createdAt: String,
    @SerializedName("updated_at") val updatedAt: String
)

data class StaffListResponse(
    val success: Boolean,
    val data: List<StaffItem>,
    val meta: PaginationMeta
)

data class StaffResponse(
    val success: Boolean,
    val message: String? = null,
    val data: StaffItem?
)

data class StaffRequest(
    val name: String,
    val email: String,
    val role: String,
    @SerializedName("is_active") val isActive: Boolean,
    val password: String? = null
)

data class ToggleActiveResponse(
    val success: Boolean,
    val message: String,
    @SerializedName("is_active") val isActive: Boolean
)

data class ActiveStaffItem(val id: Int, val name: String, val role: String)

// ── Shift ─────────────────────────────────────────────────────────────────────

data class ShiftItem(
    val id: Int,
    @SerializedName("user_id")        val userId: Int,
    @SerializedName("shift_date")     val shiftDate: String,
    @SerializedName("start_time")     val startTime: String,
    @SerializedName("end_time")       val endTime: String,
    @SerializedName("duration_hours") val durationHours: Double,
    val position: String,
    @SerializedName("position_label") val positionLabel: String,
    val notes: String? = null,
    @SerializedName("created_at")     val createdAt: String,
    val user: ShiftUserInfo? = null
)

data class ShiftUserInfo(val id: Int, val name: String, val initials: String)

data class ShiftListResponse(
    val success: Boolean,
    val data: List<ShiftItem>,
    val meta: PaginationMeta
)

data class ShiftResponse(
    val success: Boolean,
    val message: String? = null,
    val data: ShiftItem?
)

data class ShiftRequest(
    @SerializedName("user_id")    val userId: Int,
    @SerializedName("shift_date") val shiftDate: String,
    @SerializedName("start_time") val startTime: String,
    @SerializedName("end_time")   val endTime: String,
    val position: String,
    val notes: String? = null
)
