package com.pemmob.geprekrejo.ui.finance

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.*
import com.pemmob.geprekrejo.data.repository.FinanceRepository
import com.pemmob.geprekrejo.util.Result
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Date
import java.util.Locale

data class FinanceUiState(
    // ── Tab ───────────────────────────────────────────────────────────────────
    val selectedTab: Int = 0, // 0 = Laba/Rugi, 1 = Pengeluaran

    // ── Laporan Laba / Rugi ───────────────────────────────────────────────────
    val reportPreset: String = "bulan_ini", // "hari_ini", "minggu_ini", "bulan_ini", "tahun_ini"
    val reportData: ProfitLossWrapper? = null,
    val isReportLoading: Boolean = false,

    // ── Pengeluaran ───────────────────────────────────────────────────────────
    val expenseMonth: String = SimpleDateFormat("yyyy-MM", Locale.getDefault()).format(Date()),
    val expenseCategoryFilter: String? = null,
    val expenseList: List<ExpenseItem> = emptyList(),
    val expenseMonthTotal: Double = 0.0,
    val isExpenseLoading: Boolean = false,
    val isSubmitting: Boolean = false,

    // ── Form Modal Pengeluaran ────────────────────────────────────────────────
    val showExpenseDialog: Boolean = false,
    val editingExpenseId: Int? = null,
    val formDescription: String = "",
    val formCategory: String = "operasional",
    val formAmount: String = "",
    val formDate: String = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date()),
    val formNotes: String = "",

    // ── Dialog Hapus ──────────────────────────────────────────────────────────
    val deletingExpense: ExpenseItem? = null,

    // ── Feedback ──────────────────────────────────────────────────────────────
    val successMessage: String? = null,
    val errorMessage: String? = null
)

class FinanceViewModel(private val repository: FinanceRepository) : ViewModel() {

    private val _uiState = MutableStateFlow(FinanceUiState())
    val uiState: StateFlow<FinanceUiState> = _uiState.asStateFlow()

    init {
        loadReport()
        loadExpenses()
    }

    fun selectTab(tab: Int) {
        _uiState.update { it.copy(selectedTab = tab) }
        if (tab == 0 && _uiState.value.reportData == null) {
            loadReport()
        } else if (tab == 1 && _uiState.value.expenseList.isEmpty()) {
            loadExpenses()
        }
    }

    // ── Laporan Actions ───────────────────────────────────────────────────────

    fun setReportPreset(preset: String) {
        _uiState.update { it.copy(reportPreset = preset) }
        loadReport()
    }

    fun loadReport() {
        viewModelScope.launch {
            _uiState.update { it.copy(isReportLoading = true) }
            val preset = _uiState.value.reportPreset
            when (val result = repository.getProfitLoss(preset = preset)) {
                is Result.Success -> {
                    _uiState.update {
                        it.copy(isReportLoading = false, reportData = result.data)
                    }
                }
                is Result.Error -> {
                    _uiState.update {
                        it.copy(isReportLoading = false, errorMessage = result.message)
                    }
                }
                else -> {}
            }
        }
    }

    // ── Pengeluaran Actions ───────────────────────────────────────────────────

    fun setExpenseCategoryFilter(category: String?) {
        _uiState.update { it.copy(expenseCategoryFilter = category) }
        loadExpenses()
    }

    fun changeExpenseMonth(offsetMonths: Int) {
        val currentMonth = _uiState.value.expenseMonth
        try {
            val sdf = SimpleDateFormat("yyyy-MM", Locale.getDefault())
            val date = sdf.parse(currentMonth) ?: Date()
            val cal = Calendar.getInstance().apply {
                time = date
                add(Calendar.MONTH, offsetMonths)
            }
            val newMonth = sdf.format(cal.time)
            _uiState.update { it.copy(expenseMonth = newMonth) }
            loadExpenses()
        } catch (_: Exception) {}
    }

    fun loadExpenses() {
        viewModelScope.launch {
            _uiState.update { it.copy(isExpenseLoading = true) }
            val month = _uiState.value.expenseMonth
            val category = _uiState.value.expenseCategoryFilter
            when (val result = repository.getExpenses(month = month, category = category)) {
                is Result.Success -> {
                    _uiState.update {
                        it.copy(
                            isExpenseLoading = false,
                            expenseList = result.data.data,
                            expenseMonthTotal = result.data.meta.monthTotal
                        )
                    }
                }
                is Result.Error -> {
                    _uiState.update {
                        it.copy(isExpenseLoading = false, errorMessage = result.message)
                    }
                }
                else -> {}
            }
        }
    }

    // ── Form Pengeluaran ──────────────────────────────────────────────────────

    fun openCreateExpense() {
        _uiState.update {
            it.copy(
                showExpenseDialog = true,
                editingExpenseId = null,
                formDescription = "",
                formCategory = "operasional",
                formAmount = "",
                formDate = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date()),
                formNotes = ""
            )
        }
    }

    fun openEditExpense(item: ExpenseItem) {
        _uiState.update {
            it.copy(
                showExpenseDialog = true,
                editingExpenseId = item.id,
                formDescription = item.description,
                formCategory = item.category,
                formAmount = item.amount.toInt().toString(),
                formDate = item.expenseDate,
                formNotes = item.notes ?: ""
            )
        }
    }

    fun dismissExpenseDialog() {
        _uiState.update { it.copy(showExpenseDialog = false, editingExpenseId = null) }
    }

    fun updateFormDescription(value: String) { _uiState.update { it.copy(formDescription = value) } }
    fun updateFormCategory(value: String) { _uiState.update { it.copy(formCategory = value) } }
    fun updateFormAmount(value: String) { _uiState.update { it.copy(formAmount = value) } }
    fun updateFormDate(value: String) { _uiState.update { it.copy(formDate = value) } }
    fun updateFormNotes(value: String) { _uiState.update { it.copy(formNotes = value) } }

    fun submitExpense() {
        val s = _uiState.value
        val amount = s.formAmount.toDoubleOrNull()
        if (s.formDescription.isBlank()) {
            _uiState.update { it.copy(errorMessage = "Keterangan pengeluaran tidak boleh kosong.") }
            return
        }
        if (amount == null || amount <= 0) {
            _uiState.update { it.copy(errorMessage = "Nominal harus lebih dari 0.") }
            return
        }
        if (s.formDate.isBlank()) {
            _uiState.update { it.copy(errorMessage = "Tanggal harus diisi.") }
            return
        }

        viewModelScope.launch {
            _uiState.update { it.copy(isSubmitting = true) }
            val req = ExpenseRequest(
                description = s.formDescription.trim(),
                category = s.formCategory,
                amount = amount,
                expenseDate = s.formDate.trim(),
                notes = s.formNotes.trim().ifBlank { null }
            )

            val result = if (s.editingExpenseId != null) {
                repository.updateExpense(s.editingExpenseId, req)
            } else {
                repository.createExpense(req)
            }

            when (result) {
                is Result.Success -> {
                    _uiState.update {
                        it.copy(
                            isSubmitting = false,
                            showExpenseDialog = false,
                            successMessage = if (s.editingExpenseId != null) "Pengeluaran diperbarui." else "Pengeluaran dicatat."
                        )
                    }
                    loadExpenses()
                    loadReport() // reload laba rugi karena beban operasional berubah
                }
                is Result.Error -> {
                    _uiState.update {
                        it.copy(isSubmitting = false, errorMessage = result.message)
                    }
                }
                else -> {}
            }
        }
    }

    // ── Hapus Pengeluaran ─────────────────────────────────────────────────────

    fun confirmDeleteExpense(item: ExpenseItem) {
        _uiState.update { it.copy(deletingExpense = item) }
    }

    fun dismissDeleteDialog() {
        _uiState.update { it.copy(deletingExpense = null) }
    }

    fun deleteExpense() {
        val item = _uiState.value.deletingExpense ?: return
        viewModelScope.launch {
            _uiState.update { it.copy(isSubmitting = true) }
            when (val r = repository.deleteExpense(item.id)) {
                is Result.Success -> {
                    _uiState.update {
                        it.copy(
                            isSubmitting = false,
                            deletingExpense = null,
                            successMessage = "Pengeluaran '${item.description}' berhasil dihapus."
                        )
                    }
                    loadExpenses()
                    loadReport()
                }
                is Result.Error -> {
                    _uiState.update {
                        it.copy(isSubmitting = false, errorMessage = r.message)
                    }
                }
                else -> {}
            }
        }
    }

    fun clearMessages() {
        _uiState.update { it.copy(successMessage = null, errorMessage = null) }
    }
}
