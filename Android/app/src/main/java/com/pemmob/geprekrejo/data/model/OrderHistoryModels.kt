package com.pemmob.geprekrejo.data.model

import com.google.gson.annotations.SerializedName

data class OrderHistoryResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: OrderHistoryData
)

data class OrderHistoryData(
    @SerializedName("mode") val mode: String,
    @SerializedName("tanggal") val tanggal: String? = null,
    @SerializedName("bulan") val bulan: String? = null,
    @SerializedName("total_revenue") val totalRevenue: Double,
    @SerializedName("total_orders") val totalOrders: Int,
    @SerializedName("orders") val orders: PaginatedOrders? = null,
    @SerializedName("daily_summary") val dailySummary: List<DailySummary>? = null
)

data class PaginatedOrders(
    @SerializedName("current_page") val currentPage: Int,
    @SerializedName("data") val data: List<OrderData>,
    @SerializedName("last_page") val lastPage: Int
)

data class OrderData(
    @SerializedName("id") val id: Int,
    @SerializedName("order_number") val orderNumber: String,
    @SerializedName("type") val type: String,
    @SerializedName("table_number") val tableNumber: String?,
    @SerializedName("queue_number") val queueNumber: Int?,
    @SerializedName("total_amount") val totalAmount: Double,
    @SerializedName("status") val status: String,
    @SerializedName("created_at") val createdAt: String,
    @SerializedName("member") val member: MemberData?,
    @SerializedName("details") val details: List<OrderDetailData>
)

data class MemberData(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String
)

data class OrderDetailData(
    @SerializedName("id") val id: Int,
    @SerializedName("menu_item") val menuItem: MenuItem?
)

data class DailySummary(
    @SerializedName("tanggal") val tanggal: String,
    @SerializedName("total_pesanan") val totalPesanan: Int,
    @SerializedName("revenue") val revenue: Double,
    @SerializedName("terbayar") val terbayar: Int
)
