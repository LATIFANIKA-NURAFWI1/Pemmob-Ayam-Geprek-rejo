package com.pemmob.geprekrejo.ui.staff

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.pemmob.geprekrejo.data.model.*
import com.pemmob.geprekrejo.data.repository.StaffRepository
import com.pemmob.geprekrejo.util.Result
import kotlinx.coroutines.FlowPreview
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

@OptIn(FlowPreview::class)
class StaffViewModel(private val repository: StaffRepository) : ViewModel() {

    private val _uiState = MutableStateFlow(StaffUiState())
    val uiState: StateFlow<StaffUiState> = _uiState.asStateFlow()

    private val _searchQuery = MutableStateFlow("")
    val searchQuery: StateFlow<String> = _searchQuery.asStateFlow()

    init {
        loadStaffList()
        loadActiveStaffList()
        viewModelScope.launch {
            _searchQuery.debounce(400L).distinctUntilChanged().collect { q ->
                _uiState.update { it.copy(staffPage = 1) }
                loadStaffList(search = q.ifBlank { null })
            }
        }
    }

    // ── Load ──────────────────────────────────────────────────────────────────

    fun loadStaffList(search: String? = null, page: Int = 1) {
        viewModelScope.launch {
            _uiState.update { it.copy(isStaffLoading = true) }
            when (val r = repository.getStaffList(search, page)) {
                is Result.Success -> _uiState.update {
                    it.copy(isStaffLoading = false, staffList = r.data.data,
                        staffPage = r.data.currentPage, staffLastPage = r.data.lastPage)
                }
                is Result.Error -> _uiState.update { it.copy(isStaffLoading = false, error = r.message) }
                else -> {}
            }
        }
    }

    fun loadShiftList(date: String? = null, page: Int = 1) {
        viewModelScope.launch {
            _uiState.update { it.copy(isShiftLoading = true) }
            when (val r = repository.getShiftList(date, page)) {
                is Result.Success -> _uiState.update {
                    it.copy(isShiftLoading = false, shiftList = r.data.data,
                        shiftPage = r.data.currentPage, shiftLastPage = r.data.lastPage)
                }
                is Result.Error -> _uiState.update { it.copy(isShiftLoading = false, error = r.message) }
                else -> {}
            }
        }
    }

    fun loadActiveStaffList() {
        viewModelScope.launch {
            when (val r = repository.getActiveStaffList()) {
                is Result.Success -> _uiState.update { it.copy(activeStaffList = r.data) }
                is Result.Error -> _uiState.update { it.copy(error = r.message) }
                else -> {}
            }
        }
    }

    // ── Search ────────────────────────────────────────────────────────────────

    fun onSearchChanged(q: String) { _searchQuery.value = q }

    // ── Staff Form ────────────────────────────────────────────────────────────

    fun openCreateStaff()           = _uiState.update { it.copy(showStaffForm = true, staffForm = StaffFormState()) }
    fun openEditStaff(s: StaffItem) = _uiState.update { it.copy(showStaffForm = true,
        staffForm = StaffFormState(s.id, s.name, s.email, "", s.role, s.isActive)) }
    fun dismissStaffForm()          = _uiState.update { it.copy(showStaffForm = false) }
    fun onStaffFormChange(fn: (StaffFormState) -> StaffFormState) = _uiState.update { it.copy(staffForm = fn(it.staffForm)) }

    fun saveStaff() {
        val f = _uiState.value.staffForm
        val req = StaffRequest(f.name, f.email, f.role, f.isActive, f.password.ifBlank { null })
        viewModelScope.launch {
            _uiState.update { it.copy(isSaving = true) }
            val r = if (f.id != null) repository.updateStaff(f.id, req) else repository.createStaff(req)
            when (r) {
                is Result.Success -> {
                    _uiState.update { it.copy(isSaving = false, showStaffForm = false,
                        success = "Staf \"${r.data.name}\" berhasil ${if (f.id != null) "diperbarui" else "ditambahkan"}.") }
                    loadStaffList()
                    loadActiveStaffList()
                }
                is Result.Error -> _uiState.update { it.copy(isSaving = false, error = r.message) }
                else -> {}
            }
        }
    }

    fun toggleActive(id: Int) {
        viewModelScope.launch {
            when (val r = repository.toggleStaffActive(id)) {
                is Result.Success -> {
                    _uiState.update { s ->
                        s.copy(staffList = s.staffList.map { if (it.id == id) it.copy(isActive = r.data) else it },
                            success = "Status staf berhasil diubah.")
                    }
                    loadActiveStaffList()
                }
                is Result.Error -> _uiState.update { it.copy(error = r.message) }
                else -> {}
            }
        }
    }

    // ── Shift Form ────────────────────────────────────────────────────────────

    fun openCreateShift()            = _uiState.update { it.copy(showShiftForm = true, shiftForm = ShiftFormState()) }
    fun openEditShift(s: ShiftItem)  = _uiState.update { it.copy(showShiftForm = true,
        shiftForm = ShiftFormState(s.id, s.userId, s.shiftDate, s.startTime, s.endTime, s.position, s.notes ?: "")) }
    fun dismissShiftForm()           = _uiState.update { it.copy(showShiftForm = false) }
    fun onShiftFormChange(fn: (ShiftFormState) -> ShiftFormState) = _uiState.update { it.copy(shiftForm = fn(it.shiftForm)) }

    fun saveShift() {
        val f = _uiState.value.shiftForm
        val req = ShiftRequest(f.userId, f.shiftDate, f.startTime, f.endTime, f.position, f.notes.ifBlank { null })
        viewModelScope.launch {
            _uiState.update { it.copy(isSaving = true) }
            val r = if (f.id != null) repository.updateShift(f.id, req) else repository.createShift(req)
            when (r) {
                is Result.Success -> {
                    _uiState.update { it.copy(isSaving = false, showShiftForm = false,
                        success = "Shift berhasil ${if (f.id != null) "diperbarui" else "ditambahkan"}.") }
                    loadShiftList()
                }
                is Result.Error -> _uiState.update { it.copy(isSaving = false, error = r.message) }
                else -> {}
            }
        }
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    fun requestDelete(id: Int, type: DeleteType, label: String) =
        _uiState.update { it.copy(deleteConfirm = DeleteConfirm(id, type, label)) }
    fun dismissDelete() = _uiState.update { it.copy(deleteConfirm = null) }

    fun executeDelete() {
        val c = _uiState.value.deleteConfirm ?: return
        viewModelScope.launch {
            val r = when (c.type) {
                DeleteType.STAFF -> repository.deleteStaff(c.id)
                DeleteType.SHIFT -> repository.deleteShift(c.id)
            }
            when (r) {
                is Result.Success -> {
                    _uiState.update { it.copy(deleteConfirm = null, success = "\"${c.label}\" dihapus.") }
                    if (c.type == DeleteType.STAFF) loadStaffList() else loadShiftList()
                }
                is Result.Error -> _uiState.update { it.copy(deleteConfirm = null, error = r.message) }
                else -> {}
            }
        }
    }

    fun clearMessages() = _uiState.update { it.copy(success = null, error = null) }
}

// ── State Classes ─────────────────────────────────────────────────────────────

data class StaffUiState(
    val isStaffLoading: Boolean         = false,
    val staffList: List<StaffItem>      = emptyList(),
    val staffPage: Int                  = 1,
    val staffLastPage: Int              = 1,
    val isShiftLoading: Boolean         = false,
    val shiftList: List<ShiftItem>      = emptyList(),
    val shiftPage: Int                  = 1,
    val shiftLastPage: Int              = 1,
    val activeStaffList: List<ActiveStaffItem> = emptyList(),
    val showStaffForm: Boolean          = false,
    val staffForm: StaffFormState       = StaffFormState(),
    val showShiftForm: Boolean          = false,
    val shiftForm: ShiftFormState       = ShiftFormState(),
    val isSaving: Boolean               = false,
    val deleteConfirm: DeleteConfirm?   = null,
    val success: String?                = null,
    val error: String?                  = null
)

data class StaffFormState(
    val id: Int?       = null,
    val name: String   = "",
    val email: String  = "",
    val password: String = "",
    val role: String   = "kasir",
    val isActive: Boolean = true
)

data class ShiftFormState(
    val id: Int?       = null,
    val userId: Int    = 0,
    val shiftDate: String = "",
    val startTime: String = "",
    val endTime: String   = "",
    val position: String  = "kasir",
    val notes: String     = ""
)

data class DeleteConfirm(val id: Int, val type: DeleteType, val label: String)
enum class DeleteType { STAFF, SHIFT }
