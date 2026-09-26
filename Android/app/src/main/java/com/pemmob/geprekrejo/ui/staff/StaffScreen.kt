package com.pemmob.geprekrejo.ui.staff

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.pemmob.geprekrejo.data.model.ActiveStaffItem
import com.pemmob.geprekrejo.data.model.ShiftItem
import com.pemmob.geprekrejo.data.model.StaffItem
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale
import java.util.TimeZone

private val BrandRed = Color(0xFFBC000A)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StaffScreen(viewModel: StaffViewModel) {
    val state by viewModel.uiState.collectAsStateWithLifecycle()
    val searchQuery by viewModel.searchQuery.collectAsStateWithLifecycle()
    val snackbar = remember { SnackbarHostState() }
    var selectedTab by remember { mutableIntStateOf(0) }

    LaunchedEffect(state.success) {
        state.success?.let { snackbar.showSnackbar(it); viewModel.clearMessages() }
    }
    LaunchedEffect(state.error) {
        state.error?.let { snackbar.showSnackbar(it); viewModel.clearMessages() }
    }
    LaunchedEffect(selectedTab) {
        if (selectedTab == 1 && state.shiftList.isEmpty()) viewModel.loadShiftList()
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Manajemen Staf & Shift", fontWeight = FontWeight.Bold) }
            )
        },
        floatingActionButton = {
            FloatingActionButton(
                onClick = { if (selectedTab == 0) viewModel.openCreateStaff() else viewModel.openCreateShift() },
                containerColor = BrandRed, contentColor = Color.White
            ) { Icon(Icons.Default.Add, "Tambah") }
        },
        snackbarHost = { SnackbarHost(snackbar) }
    ) { pad ->
        Column(Modifier.padding(pad)) {
            TabRow(selectedTabIndex = selectedTab) {
                Tab(selected = selectedTab == 0, onClick = { selectedTab = 0 },
                    text = { Text("Daftar Staf") })
                Tab(selected = selectedTab == 1, onClick = { selectedTab = 1 },
                    text = { Text("Jadwal Shift") })
            }

            when (selectedTab) {
                0 -> StaffTabContent(state, searchQuery, viewModel)
                1 -> ShiftTabContent(state, viewModel)
            }
        }
    }

    // Form staf
    if (state.showStaffForm) StaffFormSheet(state, viewModel)

    // Form shift
    if (state.showShiftForm) ShiftFormSheet(state, viewModel)

    // Konfirmasi hapus
    state.deleteConfirm?.let { confirm ->
        AlertDialog(
            onDismissRequest = viewModel::dismissDelete,
            title = { Text("Konfirmasi Hapus") },
            text = { Text("Hapus \"${confirm.label}\"? Tindakan ini tidak dapat dibatalkan.") },
            confirmButton = {
                Button(onClick = viewModel::executeDelete,
                    colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.error)) {
                    Text("Hapus")
                }
            },
            dismissButton = { TextButton(onClick = viewModel::dismissDelete) { Text("Batal") } }
        )
    }
}

// ── Staff Tab ─────────────────────────────────────────────────────────────────

@Composable
private fun StaffTabContent(state: StaffUiState, searchQuery: String, viewModel: StaffViewModel) {
    Column {
        OutlinedTextField(
            value = searchQuery, onValueChange = viewModel::onSearchChanged,
            modifier = Modifier.fillMaxWidth().padding(16.dp),
            placeholder = { Text("Cari nama atau email...") },
            leadingIcon = { Icon(Icons.Default.Search, null) },
            trailingIcon = {
                if (searchQuery.isNotBlank())
                    IconButton(onClick = { viewModel.onSearchChanged("") }) {
                        Icon(Icons.Default.Close, null)
                    }
            },
            singleLine = true, shape = RoundedCornerShape(12.dp)
        )

        if (state.isStaffLoading) {
            Box(Modifier.fillMaxSize(), Alignment.Center) { CircularProgressIndicator(color = BrandRed) }
        } else {
            LazyColumn(contentPadding = PaddingValues(horizontal = 16.dp, vertical = 4.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)) {
                items(state.staffList, key = { it.id }) { staff ->
                    StaffCard(staff,
                        onEdit   = { viewModel.openEditStaff(staff) },
                        onDelete = { viewModel.requestDelete(staff.id, DeleteType.STAFF, staff.name) },
                        onToggle = { viewModel.toggleActive(staff.id) }
                    )
                }
                if (state.staffPage < state.staffLastPage) {
                    item {
                        TextButton(onClick = { viewModel.loadStaffList(page = state.staffPage + 1) },
                            modifier = Modifier.fillMaxWidth()) { Text("Muat lebih banyak") }
                    }
                }
            }
        }
    }
}

@Composable
private fun StaffCard(staff: StaffItem, onEdit: () -> Unit, onDelete: () -> Unit, onToggle: () -> Unit) {
    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(14.dp),
        elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp)) {
            Row(Modifier.fillMaxWidth(), Arrangement.SpaceBetween, Alignment.CenterVertically) {
                Row(verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                    Box(Modifier.size(44.dp).clip(CircleShape)
                        .background(BrandRed.copy(alpha = 0.15f)), Alignment.Center) {
                        Text(staff.initials, fontWeight = FontWeight.Bold, color = BrandRed)
                    }
                    Column {
                        Text(staff.name, fontWeight = FontWeight.SemiBold)
                        Text(staff.email, style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                            maxLines = 1, overflow = TextOverflow.Ellipsis)
                    }
                }
                Row {
                    IconButton(onClick = onEdit) { Icon(Icons.Default.Edit, null, tint = BrandRed) }
                    IconButton(onClick = onDelete) { Icon(Icons.Default.Delete, null,
                        tint = MaterialTheme.colorScheme.error) }
                }
            }
            HorizontalDivider(Modifier.padding(vertical = 8.dp))
            Row(Modifier.fillMaxWidth(), Arrangement.SpaceBetween, Alignment.CenterVertically) {
                Box(Modifier.clip(RoundedCornerShape(6.dp))
                    .background(MaterialTheme.colorScheme.secondaryContainer)
                    .padding(horizontal = 8.dp, vertical = 4.dp)) {
                    Text(staff.role.replaceFirstChar { it.uppercase() },
                        style = MaterialTheme.typography.labelSmall,
                        color = MaterialTheme.colorScheme.onSecondaryContainer)
                }
                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                    Text(if (staff.isActive) "Aktif" else "Nonaktif",
                        style = MaterialTheme.typography.labelSmall,
                        color = if (staff.isActive) BrandRed else MaterialTheme.colorScheme.onSurfaceVariant)
                    Switch(checked = staff.isActive, onCheckedChange = { onToggle() })
                }
            }
        }
    }
}

// ── Shift Tab ─────────────────────────────────────────────────────────────────

@Composable
private fun ShiftTabContent(state: StaffUiState, viewModel: StaffViewModel) {
    if (state.isShiftLoading) {
        Box(Modifier.fillMaxSize(), Alignment.Center) { CircularProgressIndicator(color = BrandRed) }
    } else {
        LazyColumn(contentPadding = PaddingValues(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            items(state.shiftList, key = { it.id }) { shift ->
                ShiftCard(shift,
                    onEdit   = { viewModel.openEditShift(shift) },
                    onDelete = { viewModel.requestDelete(shift.id, DeleteType.SHIFT,
                        "${shift.user?.name ?: "Staf"} (${shift.shiftDate})") }
                )
            }
            if (state.shiftPage < state.shiftLastPage) {
                item {
                    TextButton(onClick = { viewModel.loadShiftList(page = state.shiftPage + 1) },
                        modifier = Modifier.fillMaxWidth()) { Text("Muat lebih banyak") }
                }
            }
        }
    }
}

@Composable
private fun ShiftCard(shift: ShiftItem, onEdit: () -> Unit, onDelete: () -> Unit) {
    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(14.dp),
        elevation = CardDefaults.cardElevation(2.dp)) {
        Row(Modifier.padding(16.dp).fillMaxWidth(),
            Arrangement.SpaceBetween, Alignment.CenterVertically) {
            Column(Modifier.weight(1f)) {
                Text(shift.user?.name ?: "Staf", fontWeight = FontWeight.SemiBold)
                Text("${shift.shiftDate}  •  ${shift.startTime}–${shift.endTime}",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant)
                Text("${shift.positionLabel}  •  ${shift.durationHours} jam",
                    style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant)
                shift.notes?.let { if (it.isNotBlank()) Text("📝 $it",
                    style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    maxLines = 1, overflow = TextOverflow.Ellipsis) }
            }
            Row {
                IconButton(onClick = onEdit) { Icon(Icons.Default.Edit, null, tint = BrandRed) }
                IconButton(onClick = onDelete) { Icon(Icons.Default.Delete, null,
                    tint = MaterialTheme.colorScheme.error) }
            }
        }
    }
}

// ── Staff Form Sheet ──────────────────────────────────────────────────────────

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun StaffFormSheet(state: StaffUiState, viewModel: StaffViewModel) {
    val f = state.staffForm
    val sheetState = rememberModalBottomSheetState(skipPartiallyExpanded = true)

    ModalBottomSheet(onDismissRequest = viewModel::dismissStaffForm, sheetState = sheetState) {
        Column(Modifier.fillMaxWidth().padding(horizontal = 24.dp).padding(bottom = 40.dp),
            verticalArrangement = Arrangement.spacedBy(14.dp)) {
            Text(if (f.id != null) "Edit Staf" else "Tambah Staf Baru",
                style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)

            OutlinedTextField(f.name, { viewModel.onStaffFormChange { s -> s.copy(name = it) } },
                Modifier.fillMaxWidth(), label = { Text("Nama *") }, singleLine = true)
            OutlinedTextField(f.email, { viewModel.onStaffFormChange { s -> s.copy(email = it) } },
                Modifier.fillMaxWidth(), label = { Text("Email *") }, singleLine = true)
            OutlinedTextField(f.password, { viewModel.onStaffFormChange { s -> s.copy(password = it) } },
                Modifier.fillMaxWidth(),
                label = { Text(if (f.id != null) "Password Baru (kosongkan = tidak diubah)" else "Password *") },
                supportingText = { Text("Min. 8 karakter") },
                visualTransformation = PasswordVisualTransformation(), singleLine = true)

            RoleDropdown(f.role) { viewModel.onStaffFormChange { s -> s.copy(role = it) } }

            Row(Modifier.fillMaxWidth(), Arrangement.SpaceBetween, Alignment.CenterVertically) {
                Text("Akun Aktif")
                Switch(f.isActive, { viewModel.onStaffFormChange { s -> s.copy(isActive = it) } })
            }

            state.error?.let { Text(it, color = MaterialTheme.colorScheme.error,
                style = MaterialTheme.typography.bodySmall) }

            Button(onClick = viewModel::saveStaff, Modifier.fillMaxWidth(), enabled = !state.isSaving,
                colors = ButtonDefaults.buttonColors(containerColor = BrandRed)) {
                if (state.isSaving) CircularProgressIndicator(Modifier.size(20.dp), color = Color.White)
                else Text(if (f.id != null) "Simpan Perubahan" else "Buat Staf", color = Color.White)
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun RoleDropdown(selected: String, onSelect: (String) -> Unit) {
    val roles = listOf("kasir" to "Kasir", "kds" to "KDS Dapur", "inventory" to "Inventory")
    var expanded by remember { mutableStateOf(false) }
    ExposedDropdownMenuBox(expanded, { expanded = it }) {
        OutlinedTextField(roles.find { it.first == selected }?.second ?: selected, {},
            Modifier.fillMaxWidth().menuAnchor(), readOnly = true,
            label = { Text("Role *") }, trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) })
        ExposedDropdownMenu(expanded, { expanded = false }) {
            roles.forEach { (v, l) -> DropdownMenuItem({ Text(l) }, { onSelect(v); expanded = false }) }
        }
    }
}

// ── Shift Form Sheet ──────────────────────────────────────────────────────────

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun ShiftFormSheet(state: StaffUiState, viewModel: StaffViewModel) {
    val f = state.shiftForm
    val sheetState = rememberModalBottomSheetState(skipPartiallyExpanded = true)

    // Date picker state
    var showDatePicker by remember { mutableStateOf(false) }
    // Time picker state
    var showStartTimePicker by remember { mutableStateOf(false) }
    var showEndTimePicker by remember { mutableStateOf(false) }

    ModalBottomSheet(onDismissRequest = viewModel::dismissShiftForm, sheetState = sheetState) {
        Column(Modifier.fillMaxWidth().padding(horizontal = 24.dp).padding(bottom = 40.dp),
            verticalArrangement = Arrangement.spacedBy(14.dp)) {
            Text(if (f.id != null) "Edit Shift" else "Tambah Shift",
                style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)

            StaffDropdown(state.activeStaffList, f.userId) { viewModel.onShiftFormChange { s -> s.copy(userId = it) } }

            // Tanggal — klik untuk buka DatePicker
            OutlinedTextField(
                value = f.shiftDate,
                onValueChange = {},
                modifier = Modifier
                    .fillMaxWidth()
                    .clickable { showDatePicker = true },
                label = { Text("Tanggal *") },
                placeholder = { Text("Tap untuk pilih tanggal") },
                readOnly = true,
                enabled = false,
                singleLine = true,
                colors = OutlinedTextFieldDefaults.colors(
                    disabledTextColor = MaterialTheme.colorScheme.onSurface,
                    disabledBorderColor = MaterialTheme.colorScheme.outline,
                    disabledLabelColor = MaterialTheme.colorScheme.onSurfaceVariant,
                    disabledPlaceholderColor = MaterialTheme.colorScheme.onSurfaceVariant,
                    disabledTrailingIconColor = MaterialTheme.colorScheme.onSurfaceVariant
                ),
                trailingIcon = { Icon(Icons.Default.CalendarToday, null) }
            )

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                // Jam Mulai — klik untuk buka TimePicker
                OutlinedTextField(
                    value = f.startTime,
                    onValueChange = {},
                    modifier = Modifier
                        .weight(1f)
                        .clickable { showStartTimePicker = true },
                    label = { Text("Mulai") },
                    placeholder = { Text("HH:mm") },
                    readOnly = true,
                    enabled = false,
                    singleLine = true,
                    colors = OutlinedTextFieldDefaults.colors(
                        disabledTextColor = MaterialTheme.colorScheme.onSurface,
                        disabledBorderColor = MaterialTheme.colorScheme.outline,
                        disabledLabelColor = MaterialTheme.colorScheme.onSurfaceVariant,
                        disabledPlaceholderColor = MaterialTheme.colorScheme.onSurfaceVariant,
                        disabledTrailingIconColor = MaterialTheme.colorScheme.onSurfaceVariant
                    ),
                    trailingIcon = { Icon(Icons.Default.Schedule, null) }
                )
                // Jam Selesai — klik untuk buka TimePicker
                OutlinedTextField(
                    value = f.endTime,
                    onValueChange = {},
                    modifier = Modifier
                        .weight(1f)
                        .clickable { showEndTimePicker = true },
                    label = { Text("Selesai") },
                    placeholder = { Text("HH:mm") },
                    readOnly = true,
                    enabled = false,
                    singleLine = true,
                    colors = OutlinedTextFieldDefaults.colors(
                        disabledTextColor = MaterialTheme.colorScheme.onSurface,
                        disabledBorderColor = MaterialTheme.colorScheme.outline,
                        disabledLabelColor = MaterialTheme.colorScheme.onSurfaceVariant,
                        disabledPlaceholderColor = MaterialTheme.colorScheme.onSurfaceVariant,
                        disabledTrailingIconColor = MaterialTheme.colorScheme.onSurfaceVariant
                    ),
                    trailingIcon = { Icon(Icons.Default.Schedule, null) }
                )
            }

            PositionDropdown(f.position) { viewModel.onShiftFormChange { s -> s.copy(position = it) } }

            OutlinedTextField(f.notes, { viewModel.onShiftFormChange { s -> s.copy(notes = it) } },
                Modifier.fillMaxWidth(), label = { Text("Catatan (opsional)") }, maxLines = 3)

            Button(onClick = viewModel::saveShift, Modifier.fillMaxWidth(), enabled = !state.isSaving,
                colors = ButtonDefaults.buttonColors(containerColor = BrandRed)) {
                if (state.isSaving) CircularProgressIndicator(Modifier.size(20.dp), color = Color.White)
                else Text(if (f.id != null) "Simpan" else "Tambah Shift", color = Color.White)
            }
        }
    }

    // ── DatePicker Dialog ──────────────────────────────────────────────────────
    if (showDatePicker) {
        val datePickerState = rememberDatePickerState()
        DatePickerDialog(
            onDismissRequest = { showDatePicker = false },
            confirmButton = {
                TextButton(onClick = {
                    datePickerState.selectedDateMillis?.let { millis ->
                        val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                        sdf.timeZone = TimeZone.getTimeZone("UTC")
                        val formatted = sdf.format(millis)
                        viewModel.onShiftFormChange { s -> s.copy(shiftDate = formatted) }
                    }
                    showDatePicker = false
                }) { Text("OK") }
            },
            dismissButton = {
                TextButton(onClick = { showDatePicker = false }) { Text("Batal") }
            }
        ) {
            DatePicker(state = datePickerState)
        }
    }

    // ── TimePicker Dialog — Jam Mulai ─────────────────────────────────────────
    if (showStartTimePicker) {
        TimePickerDialogWrapper(
            onConfirm = { hour, minute ->
                val formatted = String.format(Locale.getDefault(), "%02d:%02d", hour, minute)
                viewModel.onShiftFormChange { s -> s.copy(startTime = formatted) }
                showStartTimePicker = false
            },
            onDismiss = { showStartTimePicker = false }
        )
    }

    // ── TimePicker Dialog — Jam Selesai ───────────────────────────────────────
    if (showEndTimePicker) {
        TimePickerDialogWrapper(
            onConfirm = { hour, minute ->
                val formatted = String.format(Locale.getDefault(), "%02d:%02d", hour, minute)
                viewModel.onShiftFormChange { s -> s.copy(endTime = formatted) }
                showEndTimePicker = false
            },
            onDismiss = { showEndTimePicker = false }
        )
    }
}

/**
 * Composable wrapper untuk Material3 TimePicker yang dibungkus dalam AlertDialog.
 */
@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun TimePickerDialogWrapper(
    onConfirm: (hour: Int, minute: Int) -> Unit,
    onDismiss: () -> Unit
) {
    val timePickerState = rememberTimePickerState(is24Hour = true)

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Pilih Jam") },
        text = {
            Box(Modifier.fillMaxWidth(), Alignment.Center) {
                TimePicker(state = timePickerState)
            }
        },
        confirmButton = {
            TextButton(onClick = {
                onConfirm(timePickerState.hour, timePickerState.minute)
            }) { Text("OK") }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Batal") }
        }
    )
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun StaffDropdown(list: List<ActiveStaffItem>, selectedId: Int, onSelect: (Int) -> Unit) {
    var expanded by remember { mutableStateOf(false) }
    val label = list.find { it.id == selectedId }?.name ?: "Pilih Staf"
    ExposedDropdownMenuBox(expanded, { expanded = it }) {
        OutlinedTextField(label, {}, Modifier.fillMaxWidth().menuAnchor(), readOnly = true,
            label = { Text("Staf *") }, trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) })
        ExposedDropdownMenu(expanded, { expanded = false }) {
            list.forEach { staff ->
                DropdownMenuItem({ Text("${staff.name} (${staff.role})") },
                    { onSelect(staff.id); expanded = false })
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun PositionDropdown(selected: String, onSelect: (String) -> Unit) {
    val positions = listOf("kasir" to "Kasir", "inventory" to "Inventory", "dapur" to "Dapur")
    var expanded by remember { mutableStateOf(false) }
    ExposedDropdownMenuBox(expanded, { expanded = it }) {
        OutlinedTextField(positions.find { it.first == selected }?.second ?: selected, {},
            Modifier.fillMaxWidth().menuAnchor(), readOnly = true,
            label = { Text("Posisi *") }, trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) })
        ExposedDropdownMenu(expanded, { expanded = false }) {
            positions.forEach { (v, l) -> DropdownMenuItem({ Text(l) }, { onSelect(v); expanded = false }) }
        }
    }
}
