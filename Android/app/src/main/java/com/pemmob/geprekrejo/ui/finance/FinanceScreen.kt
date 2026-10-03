package com.pemmob.geprekrejo.ui.finance

import android.content.Intent
import android.net.Uri
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.pemmob.geprekrejo.data.model.*
import com.pemmob.geprekrejo.network.RetrofitClient
import java.text.NumberFormat
import java.text.SimpleDateFormat
import java.util.Locale

private val BrandRed = Color(0xFFBC000A)
private val BrandGreen = Color(0xFF2E7D32)
private val BrandAmber = Color(0xFFED6C02)
private val BrandBlue = Color(0xFF1976D2)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun FinanceScreen(
    viewModel: FinanceViewModel,
    onOpenDrawer: () -> Unit = {}
) {
    val state by viewModel.uiState.collectAsStateWithLifecycle()
    val snackbar = remember { SnackbarHostState() }
    val context = LocalContext.current

    LaunchedEffect(state.successMessage) {
        state.successMessage?.let {
            snackbar.showSnackbar(it)
            viewModel.clearMessages()
        }
    }

    LaunchedEffect(state.errorMessage) {
        state.errorMessage?.let {
            snackbar.showSnackbar(it)
            viewModel.clearMessages()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text("Keuangan & Laporan", fontWeight = FontWeight.Bold)
                        Text(
                            if (state.selectedTab == 0) "Laporan Laba / Rugi Toko" else "Pencatatan Biaya Operasional",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                },
                navigationIcon = {
                    IconButton(onClick = onOpenDrawer) {
                        Icon(Icons.Default.Menu, "Menu")
                    }
                },
                actions = {
                    if (state.selectedTab == 0) {
                        IconButton(onClick = {
                            val preset = state.reportData?.preset ?: "bulan_ini"
                            val url = RetrofitClient.BASE_URL + "admin/reports/export-pdf?preset=" + preset
                            val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                            context.startActivity(intent)
                        }) {
                            Icon(Icons.Default.Print, "Cetak PDF")
                        }
                    }
                    IconButton(onClick = {
                        if (state.selectedTab == 0) viewModel.loadReport() else viewModel.loadExpenses()
                    }) {
                        Icon(Icons.Default.Refresh, "Segarkan")
                    }
                }
            )
        },
        floatingActionButton = {
            if (state.selectedTab == 1) {
                FloatingActionButton(
                    onClick = viewModel::openCreateExpense,
                    containerColor = BrandRed,
                    contentColor = Color.White
                ) {
                    Icon(Icons.Default.Add, "Catat Pengeluaran")
                }
            }
        },
        snackbarHost = { SnackbarHost(snackbar) }
    ) { pad ->
        Column(
            Modifier
                .fillMaxSize()
                .padding(pad)
        ) {
            TabRow(
                selectedTabIndex = state.selectedTab,
                containerColor = MaterialTheme.colorScheme.surface,
                contentColor = BrandRed
            ) {
                Tab(
                    selected = state.selectedTab == 0,
                    onClick = { viewModel.selectTab(0) },
                    text = { Text("Laba / Rugi", fontWeight = FontWeight.SemiBold) },
                    icon = { Icon(Icons.Default.TrendingUp, null) }
                )
                Tab(
                    selected = state.selectedTab == 1,
                    onClick = { viewModel.selectTab(1) },
                    text = { Text("Pengeluaran", fontWeight = FontWeight.SemiBold) },
                    icon = { Icon(Icons.Default.ReceiptLong, null) }
                )
            }

            when (state.selectedTab) {
                0 -> ProfitLossTabContent(state = state, viewModel = viewModel)
                1 -> ExpenseTabContent(state = state, viewModel = viewModel)
            }
        }
    }

    // Modal Form Pengeluaran
    if (state.showExpenseDialog) {
        ExpenseFormDialog(state = state, viewModel = viewModel)
    }

    // Dialog Konfirmasi Hapus
    if (state.deletingExpense != null) {
        AlertDialog(
            onDismissRequest = viewModel::dismissDeleteDialog,
            title = { Text("Hapus Pengeluaran?") },
            text = {
                Text("Apakah Anda yakin ingin menghapus '${state.deletingExpense?.description}' sejumlah ${formatRupiah(state.deletingExpense?.amount ?: 0.0)}?")
            },
            confirmButton = {
                Button(
                    onClick = viewModel::deleteExpense,
                    colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.error)
                ) {
                    Text("Hapus")
                }
            },
            dismissButton = {
                TextButton(onClick = viewModel::dismissDeleteDialog) {
                    Text("Batal")
                }
            }
        )
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// TAB 1: LAPORAN LABA / RUGI
// ─────────────────────────────────────────────────────────────────────────────

@Composable
private fun ProfitLossTabContent(
    state: FinanceUiState,
    viewModel: FinanceViewModel
) {
    val presets = listOf(
        "hari_ini" to "Hari Ini",
        "minggu_ini" to "Minggu Ini",
        "bulan_ini" to "Bulan Ini",
        "tahun_ini" to "Tahun Ini"
    )

    LazyColumn(
        modifier = Modifier.fillMaxSize(),
        contentPadding = PaddingValues(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        // Preset Chips
        item {
            LazyRow(
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(presets) { (key, label) ->
                    val isSelected = state.reportPreset == key
                    FilterChip(
                        selected = isSelected,
                        onClick = { viewModel.setReportPreset(key) },
                        label = { Text(label) },
                        colors = FilterChipDefaults.filterChipColors(
                            selectedContainerColor = BrandRed.copy(alpha = 0.15f),
                            selectedLabelColor = BrandRed
                        )
                    )
                }
            }
        }

        if (state.isReportLoading) {
            item {
                Box(
                    Modifier
                        .fillMaxWidth()
                        .height(200.dp),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator(color = BrandRed)
                }
            }
        } else if (state.reportData != null) {
            val report = state.reportData.report

            // Hero Card: Laba Bersih
            item {
                NetProfitHeroCard(report = report)
            }

            // 4 Grid Metrik Utama
            item {
                Text("Ringkasan Arus Kas", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Spacer(Modifier.height(8.dp))
                Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        MetricCard(
                            modifier = Modifier.weight(1f),
                            title = "Pendapatan",
                            value = formatRupiah(report.revenue),
                            subtitle = "${report.orderCount} pesanan selesai",
                            icon = Icons.Default.Payments,
                            tint = BrandBlue
                        )
                        MetricCard(
                            modifier = Modifier.weight(1f),
                            title = "HPP Bahan",
                            value = formatRupiah(report.totalHpp),
                            subtitle = "Modal resep menu",
                            icon = Icons.Default.Inventory2,
                            tint = BrandAmber
                        )
                    }
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        MetricCard(
                            modifier = Modifier.weight(1f),
                            title = "Laba Kotor",
                            value = formatRupiah(report.grossProfit),
                            subtitle = "Margin: ${report.grossMarginPct}%",
                            icon = Icons.Default.ShowChart,
                            tint = BrandGreen
                        )
                        MetricCard(
                            modifier = Modifier.weight(1f),
                            title = "Pengeluaran",
                            value = formatRupiah(report.totalExpenses),
                            subtitle = "Beban operasional",
                            icon = Icons.Default.Receipt,
                            tint = BrandRed
                        )
                    }
                }
            }

            // Struktur Laba Rugi
            if (report.summary.isNotEmpty()) {
                item {
                    Text("Struktur Laba / Rugi", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                    Spacer(Modifier.height(8.dp))
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.5f))
                    ) {
                        Column(Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            report.summary.forEach { item ->
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Text(
                                        item.label,
                                        style = if (item.type == "total") MaterialTheme.typography.bodyMedium.copy(fontWeight = FontWeight.Bold)
                                        else MaterialTheme.typography.bodyMedium
                                    )
                                    val formattedAmt = formatRupiah(item.amount)
                                    val color = when (item.type) {
                                        "expense" -> BrandRed
                                        "income" -> BrandBlue
                                        "total" -> if (item.amount >= 0) BrandGreen else BrandRed
                                        else -> MaterialTheme.colorScheme.onSurface
                                    }
                                    Text(
                                        formattedAmt,
                                        fontWeight = if (item.type == "total") FontWeight.Bold else FontWeight.Medium,
                                        color = color
                                    )
                                }
                                if (item.type == "subtotal") {
                                    HorizontalDivider(color = MaterialTheme.colorScheme.outlineVariant)
                                }
                            }
                        }
                    }
                }
            }

            // Top Menu Terlaris di Periode Ini
            if (state.reportData.topMenus.isNotEmpty()) {
                item {
                    Text("Menu Penyumbang Omset Terbesar", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                    Spacer(Modifier.height(8.dp))
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                        elevation = CardDefaults.cardElevation(2.dp)
                    ) {
                        Column(Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            state.reportData.topMenus.forEachIndexed { idx, menu ->
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Row(
                                        verticalAlignment = Alignment.CenterVertically,
                                        modifier = Modifier.weight(1f)
                                    ) {
                                        val isTop3 = idx < 3
                                        val circleBg = if (isTop3) BrandRed else Color(0xFFFFF8E1)
                                        val textColor = if (isTop3) Color.White else Color(0xFFF59E0B)
                                        
                                        Box(
                                            modifier = Modifier
                                                .size(24.dp)
                                                .clip(CircleShape)
                                                .background(circleBg),
                                            contentAlignment = Alignment.Center
                                        ) {
                                            Text(
                                                "${idx + 1}",
                                                color = textColor,
                                                fontSize = 12.sp,
                                                fontWeight = FontWeight.Bold
                                            )
                                        }
                                        Spacer(Modifier.width(10.dp))
                                        Column {
                                            Text(menu.menuItemName, fontWeight = FontWeight.SemiBold, maxLines = 1, overflow = TextOverflow.Ellipsis)
                                            Text("${menu.totalQty} porsi terjual • Margin ${menu.marginPct}%", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                                        }
                                    }
                                    Text(formatRupiah(menu.totalRevenue), fontWeight = FontWeight.Bold, color = BrandRed)
                                }
                                if (idx < state.reportData.topMenus.size - 1) {
                                    HorizontalDivider(color = MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.5f))
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun NetProfitHeroCard(report: ProfitLossReport) {
    val isProfit = report.netProfit >= 0
    val cardBg = if (isProfit) Color(0xFF1B3D2F) else Color(0xFF4A1A1A)
    val accentColor = if (isProfit) Color(0xFF4CAF50) else Color(0xFFFF5252)

    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = cardBg)
    ) {
        Column(
            Modifier
                .fillMaxWidth()
                .padding(20.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(
                        if (isProfit) Icons.Default.TrendingUp else Icons.Default.TrendingDown,
                        contentDescription = null,
                        tint = accentColor,
                        modifier = Modifier.size(20.dp)
                    )
                    Spacer(Modifier.width(6.dp))
                    Text(
                        "LABA BERSIH (NET PROFIT)",
                        color = Color.White.copy(alpha = 0.8f),
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold,
                        letterSpacing = 1.sp
                    )
                }

                Surface(
                    color = accentColor.copy(alpha = 0.2f),
                    shape = RoundedCornerShape(20.dp)
                ) {
                    Text(
                        "${report.netMarginPct}% margin",
                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp),
                        color = accentColor,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }

            Spacer(Modifier.height(10.dp))

            Text(
                formatRupiah(report.netProfit),
                fontSize = 28.sp,
                fontWeight = FontWeight.ExtraBold,
                color = Color.White
            )

            Spacer(Modifier.height(6.dp))

            Text(
                "Periode: ${report.periodFrom} s/d ${report.periodTo}",
                color = Color.White.copy(alpha = 0.6f),
                fontSize = 12.sp
            )
        }
    }
}

@Composable
private fun MetricCard(
    modifier: Modifier = Modifier,
    title: String,
    value: String,
    subtitle: String,
    icon: ImageVector,
    tint: Color
) {
    Card(
        modifier = modifier,
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(2.dp)
    ) {
        Column(Modifier.padding(14.dp)) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(title, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                Icon(icon, contentDescription = null, tint = tint, modifier = Modifier.size(18.dp))
            }
            Spacer(Modifier.height(6.dp))
            Text(value, fontWeight = FontWeight.Bold, fontSize = 16.sp)
            Spacer(Modifier.height(2.dp))
            Text(subtitle, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant, fontSize = 11.sp)
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// TAB 2: PENGELUARAN OPERASIONAL
// ─────────────────────────────────────────────────────────────────────────────

@Composable
private fun ExpenseTabContent(
    state: FinanceUiState,
    viewModel: FinanceViewModel
) {
    val categoryOptions = listOf(
        null to "Semua",
        "bahan_baku" to "Bahan Baku",
        "operasional" to "Operasional",
        "gaji" to "Gaji",
        "perawatan" to "Perawatan",
        "lainnya" to "Lainnya"
    )

    LazyColumn(
        modifier = Modifier.fillMaxSize(),
        contentPadding = PaddingValues(16.dp),
        verticalArrangement = Arrangement.spacedBy(14.dp)
    ) {
        // Month Selector Bar
        item {
            Card(
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp)
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 8.dp, vertical = 6.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    IconButton(onClick = { viewModel.changeExpenseMonth(-1) }) {
                        Icon(Icons.Default.ChevronLeft, "Bulan Sebelumnya")
                    }
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text("Bulan", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                        Text(formatMonthDisplay(state.expenseMonth), fontWeight = FontWeight.Bold, fontSize = 15.sp)
                    }
                    IconButton(onClick = { viewModel.changeExpenseMonth(1) }) {
                        Icon(Icons.Default.ChevronRight, "Bulan Berikutnya")
                    }
                }
            }
        }

        // Total Pengeluaran Bulan Ini Banner
        item {
            Card(
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = BrandRed.copy(alpha = 0.08f))
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Column {
                        Text("Total Pengeluaran Bulan Ini", style = MaterialTheme.typography.bodySmall, color = BrandRed)
                        Text(
                            formatRupiah(state.expenseMonthTotal),
                            fontSize = 22.sp,
                            fontWeight = FontWeight.ExtraBold,
                            color = BrandRed
                        )
                    }
                    Icon(
                        Icons.Default.AccountBalanceWallet,
                        contentDescription = null,
                        tint = BrandRed,
                        modifier = Modifier.size(32.dp)
                    )
                }
            }
        }

        // Category Filter Chips
        item {
            LazyRow(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                items(categoryOptions) { (catKey, catLabel) ->
                    val isSelected = state.expenseCategoryFilter == catKey
                    FilterChip(
                        selected = isSelected,
                        onClick = { viewModel.setExpenseCategoryFilter(catKey) },
                        label = { Text(catLabel) },
                        colors = FilterChipDefaults.filterChipColors(
                            selectedContainerColor = BrandRed.copy(alpha = 0.15f),
                            selectedLabelColor = BrandRed
                        )
                    )
                }
            }
        }

        // Expense Items List
        if (state.isExpenseLoading) {
            item {
                Box(
                    Modifier
                        .fillMaxWidth()
                        .height(150.dp),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator(color = BrandRed)
                }
            }
        } else if (state.expenseList.isEmpty()) {
            item {
                Box(
                    Modifier
                        .fillMaxWidth()
                        .padding(vertical = 40.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Icon(Icons.Default.ReceiptLong, contentDescription = null, modifier = Modifier.size(48.dp), tint = MaterialTheme.colorScheme.outline)
                        Spacer(Modifier.height(8.dp))
                        Text("Belum ada pengeluaran dicatat.", color = MaterialTheme.colorScheme.onSurfaceVariant)
                        Text("Tekan tombol + di bawah untuk mencatat pengeluaran.", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.outline)
                    }
                }
            }
        } else {
            items(state.expenseList, key = { it.id }) { item ->
                ExpenseCard(
                    item = item,
                    onEdit = { viewModel.openEditExpense(item) },
                    onDelete = { viewModel.confirmDeleteExpense(item) }
                )
            }
        }
    }
}

@Composable
private fun ExpenseCard(
    item: ExpenseItem,
    onEdit: () -> Unit,
    onDelete: () -> Unit
) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp)
    ) {
        Column(Modifier.padding(14.dp)) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.Top
            ) {
                Column(Modifier.weight(1f)) {
                    Text(item.description, fontWeight = FontWeight.Bold, fontSize = 15.sp)
                    Spacer(Modifier.height(4.dp))
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        CategoryPill(category = item.category)
                        Spacer(Modifier.width(8.dp))
                        Text(
                            item.expenseDate,
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }

                Text(
                    "- ${formatRupiah(item.amount)}",
                    fontWeight = FontWeight.ExtraBold,
                    color = BrandRed,
                    fontSize = 15.sp
                )
            }

            if (!item.notes.isNullOrBlank()) {
                Spacer(Modifier.height(8.dp))
                Text(
                    item.notes,
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
            }

            Spacer(Modifier.height(8.dp))
            HorizontalDivider(color = MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.5f))
            Spacer(Modifier.height(4.dp))

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Dicatat: ${item.recorder?.name ?: "Staf"}",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.outline,
                    fontSize = 11.sp
                )

                Row {
                    IconButton(onClick = onEdit, modifier = Modifier.size(32.dp)) {
                        Icon(Icons.Default.Edit, "Edit", tint = MaterialTheme.colorScheme.onSurfaceVariant, modifier = Modifier.size(16.dp))
                    }
                    IconButton(onClick = onDelete, modifier = Modifier.size(32.dp)) {
                        Icon(Icons.Default.Delete, "Hapus", tint = MaterialTheme.colorScheme.error, modifier = Modifier.size(16.dp))
                    }
                }
            }
        }
    }
}

@Composable
private fun CategoryPill(category: String) {
    val (label, bg, textCol) = when (category) {
        "bahan_baku" -> Triple("Bahan Baku", BrandAmber.copy(alpha = 0.15f), BrandAmber)
        "operasional" -> Triple("Operasional", BrandBlue.copy(alpha = 0.15f), BrandBlue)
        "gaji" -> Triple("Gaji", BrandGreen.copy(alpha = 0.15f), BrandGreen)
        "perawatan" -> Triple("Perawatan", Color(0xFF9C27B0).copy(alpha = 0.15f), Color(0xFF9C27B0))
        else -> Triple("Lainnya", Color.Gray.copy(alpha = 0.15f), Color.Gray)
    }

    Surface(
        color = bg,
        shape = RoundedCornerShape(6.dp)
    ) {
        Text(
            label,
            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp),
            fontSize = 11.sp,
            fontWeight = FontWeight.SemiBold,
            color = textCol
        )
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// DIALOG FORM TAMBAH / EDIT PENGELUARAN
// ─────────────────────────────────────────────────────────────────────────────

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun ExpenseFormDialog(
    state: FinanceUiState,
    viewModel: FinanceViewModel
) {
    val isEdit = state.editingExpenseId != null
    val categories = listOf(
        "operasional" to "Operasional",
        "bahan_baku" to "Bahan Baku",
        "gaji" to "Gaji Karyawan",
        "perawatan" to "Perawatan & Servis",
        "lainnya" to "Lainnya"
    )

    AlertDialog(
        onDismissRequest = viewModel::dismissExpenseDialog,
        title = {
            Text(if (isEdit) "Edit Pengeluaran" else "Catat Pengeluaran Baru", fontWeight = FontWeight.Bold)
        },
        text = {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .verticalScroll(rememberScrollState())
                    .padding(vertical = 4.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                OutlinedTextField(
                    value = state.formDescription,
                    onValueChange = viewModel::updateFormDescription,
                    label = { Text("Keterangan *") },
                    placeholder = { Text("misal: Beli gas LPG 3kg") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                // Category selector chips
                Text("Kategori *", style = MaterialTheme.typography.bodySmall, fontWeight = FontWeight.Medium)
                LazyRow(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                    items(categories) { (key, label) ->
                        val isSelected = state.formCategory == key
                        FilterChip(
                            selected = isSelected,
                            onClick = { viewModel.updateFormCategory(key) },
                            label = { Text(label, fontSize = 12.sp) },
                            colors = FilterChipDefaults.filterChipColors(
                                selectedContainerColor = BrandRed.copy(alpha = 0.15f),
                                selectedLabelColor = BrandRed
                            )
                        )
                    }
                }

                OutlinedTextField(
                    value = state.formAmount,
                    onValueChange = viewModel::updateFormAmount,
                    label = { Text("Nominal (Rp) *") },
                    placeholder = { Text("misal: 25000") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = state.formDate,
                    onValueChange = viewModel::updateFormDate,
                    label = { Text("Tanggal (YYYY-MM-DD) *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = state.formNotes,
                    onValueChange = viewModel::updateFormNotes,
                    label = { Text("Catatan Tambahan (Opsional)") },
                    modifier = Modifier.fillMaxWidth(),
                    maxLines = 2
                )
            }
        },
        confirmButton = {
            Button(
                onClick = viewModel::submitExpense,
                enabled = !state.isSubmitting,
                colors = ButtonDefaults.buttonColors(containerColor = BrandRed)
            ) {
                if (state.isSubmitting) {
                    CircularProgressIndicator(modifier = Modifier.size(16.dp), color = Color.White)
                } else {
                    Text(if (isEdit) "Simpan Perubahan" else "Simpan")
                }
            }
        },
        dismissButton = {
            TextButton(
                onClick = viewModel::dismissExpenseDialog,
                enabled = !state.isSubmitting
            ) {
                Text("Batal")
            }
        }
    )
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────────────────────────────

private fun formatRupiah(amount: Double): String {
    val formatter = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
    formatter.maximumFractionDigits = 0
    return formatter.format(amount)
}

private fun formatMonthDisplay(monthStr: String): String {
    return try {
        val parser = SimpleDateFormat("yyyy-MM", Locale.getDefault())
        val date = parser.parse(monthStr) ?: return monthStr
        val formatter = SimpleDateFormat("MMMM yyyy", Locale("id", "ID"))
        formatter.format(date)
    } catch (_: Exception) {
        monthStr
    }
}
