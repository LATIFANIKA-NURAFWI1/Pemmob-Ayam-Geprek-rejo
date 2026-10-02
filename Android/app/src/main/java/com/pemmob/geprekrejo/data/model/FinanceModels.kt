package com.pemmob.geprekrejo.data.model

import com.google.gson.annotations.SerializedName

// ─────────────────────────────────────────────────────────────────────────────
// Model Data Keuangan & Laporan Laba Rugi untuk Owner (Mobile)
// ─────────────────────────────────────────────────────────────────────────────

// ── Laporan Laba / Rugi ───────────────────────────────────────────────────────

data class ProfitLossResponse(
    val success: Boolean,
    val data: ProfitLossWrapper?
)

data class ProfitLossWrapper(
    val preset: String,
    val from: String,
    val to: String,
    val report: ProfitLossReport,
    @SerializedName("daily_trend") val dailyTrend: List<DailyTrendItem> = emptyList(),
    @SerializedName("top_menus") val topMenus: List<ReportTopMenu> = emptyList()
)

data class ProfitLossReport(
    @SerializedName("period_from") val periodFrom: String,
    @SerializedName("period_to") val periodTo: String,
    val revenue: Double,
    @SerializedName("total_hpp") val totalHpp: Double,
    @SerializedName("gross_profit") val grossProfit: Double,
    @SerializedName("gross_margin_pct") val grossMarginPct: Double,
    @SerializedName("total_expenses") val totalExpenses: Double,
    @SerializedName("expenses_by_category") val expensesByCategory: Map<String, Double> = emptyMap(),
    @SerializedName("net_profit") val netProfit: Double,
    @SerializedName("net_margin_pct") val netMarginPct: Double,
    @SerializedName("order_count") val orderCount: Int,
    @SerializedName("avg_order_value") val avgOrderValue: Double,
    val summary: List<SummaryItem> = emptyList()
)

data class SummaryItem(
    val label: String,
    val amount: Double,
    val type: String // "income", "expense", "subtotal", "total"
)

data class DailyTrendItem(
    val date: String,
    val revenue: Double,
    val hpp: Double,
    @SerializedName("gross_profit") val grossProfit: Double,
    val expenses: Double,
    @SerializedName("net_profit") val netProfit: Double
)

data class ReportTopMenu(
    @SerializedName("menu_item_name") val menuItemName: String,
    @SerializedName("total_qty") val totalQty: Int,
    @SerializedName("total_revenue") val totalRevenue: Double,
    @SerializedName("gross_profit") val grossProfit: Double,
    @SerializedName("margin_pct") val marginPct: Double
)

// ── Pengeluaran Operasional (Expenses) ────────────────────────────────────────

data class ExpenseItem(
    val id: Int,
    val description: String,
    val category: String,
    val amount: Double,
    @SerializedName("expense_date") val expenseDate: String,
    val notes: String? = null,
    val recorder: RecorderInfo? = null,
    @SerializedName("created_at") val createdAt: String? = null
)

data class RecorderInfo(
    val id: Int,
    val name: String
)

data class ExpenseListResponse(
    val success: Boolean,
    val data: List<ExpenseItem>,
    val meta: ExpensePaginationMeta
)

data class ExpensePaginationMeta(
    @SerializedName("current_page") val currentPage: Int,
    @SerializedName("last_page") val lastPage: Int,
    @SerializedName("per_page") val perPage: Int,
    val total: Int,
    val month: String,
    @SerializedName("month_total") val monthTotal: Double,
    val categories: Map<String, String>? = null
)

data class ExpenseResponse(
    val success: Boolean,
    val message: String? = null,
    val data: ExpenseItem?
)

data class ExpenseRequest(
    val description: String,
    val category: String,
    val amount: Double,
    @SerializedName("expense_date") val expenseDate: String,
    val notes: String? = null
)

data class ExpenseSummaryData(
    val month: String,
    @SerializedName("grand_total") val grandTotal: Double,
    val breakdown: List<ExpenseBreakdownItem>
)

data class ExpenseBreakdownItem(
    val category: String,
    @SerializedName("category_label") val categoryLabel: String,
    val total: Double,
    val count: Int
)
