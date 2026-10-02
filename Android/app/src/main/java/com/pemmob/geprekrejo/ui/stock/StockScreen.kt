package com.pemmob.geprekrejo.ui.stock

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Inventory2
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.pemmob.geprekrejo.data.model.StockItem
import com.pemmob.geprekrejo.ui.stock.components.RestockDialog
import com.pemmob.geprekrejo.ui.stock.components.StockFilterBar
import com.pemmob.geprekrejo.ui.stock.components.StockFormDialog
import com.pemmob.geprekrejo.ui.stock.components.StockItemRow
import com.pemmob.geprekrejo.ui.stock.components.StockSummaryCards
import com.pemmob.geprekrejo.ui.theme.BrandRed

/**
 * Halaman Utama: Manajemen Stok Bahan Baku
 * Diadaptasi dari web dashboard Ayam Geprek Rejo:
 * 1. Bagian Ringkasan (Cards Statistik: Total Bahan, Stok Aman, Stok Rendah).
 * 2. Tombol aksi "+ Tambah Bahan".
 * 3. Kolom pencarian & filter status.
 * 4. List bahan baku dengan kuantitas, progress line, badge status, dan tombol aksi (+ Restock, Edit, Hapus).
 * 5. Modal Tambah/Edit Bahan Baru & Modal Restock Cepat.
 */
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StockScreen(
    viewModel: StockViewModel,
    onBack: (() -> Unit)? = null
) {
    val state by viewModel.uiState.collectAsStateWithLifecycle()
    val snackbarHostState = remember { SnackbarHostState() }

    // State untuk konfirmasi dialog hapus
    var itemToDelete by remember { mutableStateOf<StockItem?>(null) }

    LaunchedEffect(state.userMessage) {
        state.userMessage?.let {
            snackbarHostState.showSnackbar(it)
            viewModel.clearUserMessage()
        }
    }

    LaunchedEffect(state.error) {
        state.error?.let {
            snackbarHostState.showSnackbar(it)
            viewModel.clearError()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                            Text(
                                text = "Stok Bahan Baku",
                                fontWeight = FontWeight.Bold,
                                style = MaterialTheme.typography.titleLarge,
                                color = MaterialTheme.colorScheme.onSurface
                            )
                        }
                        Text(
                            text = "Pantau dan kelola persediaan bahan baku dapur",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                },
                navigationIcon = {
                    if (onBack != null) {
                        IconButton(onClick = onBack) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                                contentDescription = "Kembali",
                                tint = MaterialTheme.colorScheme.onSurface
                            )
                        }
                    }
                },
                actions = {
                    IconButton(onClick = viewModel::loadStock) {
                        Icon(
                            imageVector = Icons.Default.Refresh,
                            contentDescription = "Refresh",
                            tint = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.surface
                )
            )
        },
        snackbarHost = { SnackbarHost(snackbarHostState) },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(horizontal = 16.dp, vertical = 12.dp),
            verticalArrangement = Arrangement.spacedBy(14.dp)
        ) {
            // ── 1. Tombol Aksi Tambah Bahan & Header Action ────────────────────
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.End,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Button(
                    onClick = viewModel::openCreateDialog,
                    shape = RoundedCornerShape(10.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = BrandRed),
                    contentPadding = PaddingValues(horizontal = 14.dp, vertical = 10.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.Add,
                        contentDescription = "Tambah Bahan",
                        tint = Color.White,
                        modifier = Modifier.size(18.dp)
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(
                        text = "Tambah Bahan",
                        fontWeight = FontWeight.Bold,
                        color = Color.White,
                        fontSize = 13.sp
                    )
                }
            }

            // ── 2. Cards Statistik Ringkasan (Total, Aman, Rendah) ──────────────
            StockSummaryCards(
                totalCount = state.totalCount,
                safeCount = state.safeStockCount,
                lowCount = state.lowStockCount
            )

            // ── 3. Kolom Pencarian & Filter Status ─────────────────────────────
            StockFilterBar(
                searchQuery = state.searchQuery,
                onSearchChange = viewModel::onSearchChange,
                selectedStatus = state.selectedStatusFilter,
                onStatusSelected = viewModel::onStatusFilterChange
            )

            // ── 4. Daftar Bahan Baku (LazyColumn) ──────────────────────────────
            if (state.isLoading) {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator(color = BrandRed)
                }
            } else if (state.filteredItems.isEmpty()) {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f),
                    contentAlignment = Alignment.Center
                ) {
                    Column(
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.spacedBy(8.dp),
                        modifier = Modifier.padding(24.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Default.Inventory2,
                            contentDescription = null,
                            tint = MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.4f),
                            modifier = Modifier.size(56.dp)
                        )
                        Text(
                            text = "Tidak ada bahan baku yang cocok",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.SemiBold,
                            color = MaterialTheme.colorScheme.onSurface
                        )
                        Text(
                            text = "Ubah kata kunci pencarian atau ganti filter status.",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }
            } else {
                LazyColumn(
                    modifier = Modifier.weight(1f),
                    contentPadding = PaddingValues(bottom = 16.dp),
                    verticalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    items(state.filteredItems, key = { it.id }) { item ->
                        StockItemRow(
                            item = item,
                            onRestockClick = viewModel::openRestockDialog,
                            onEditClick = viewModel::openEditDialog,
                            onDeleteClick = { itemToDelete = item }
                        )
                    }
                }
            }
        }
    }

    // ── 5. Modal Tambah / Edit Bahan Baru ─────────────────────────────────────
    if (state.isFormOpen) {
        StockFormDialog(
            editingItem = state.editingItem,
            onDismiss = viewModel::closeFormDialog,
            onSave = { name, unit, cost, current, min ->
                viewModel.saveIngredient(name, unit, cost, current, min)
            }
        )
    }

    // ── 6. Modal Restock / Penyesuaian ────────────────────────────────────────
    if (state.isRestockOpen && state.restockTarget != null) {
        RestockDialog(
            targetItem = state.restockTarget,
            onDismiss = viewModel::closeRestockDialog,
            onApply = { addedQty ->
                viewModel.applyRestock(addedQty)
            }
        )
    }

    // ── 7. Dialog Konfirmasi Hapus Bahan ──────────────────────────────────────
    if (itemToDelete != null) {
        val target = itemToDelete!!
        AlertDialog(
            onDismissRequest = { itemToDelete = null },
            title = {
                Text(
                    text = "Hapus Bahan?",
                    fontWeight = FontWeight.Bold,
                    color = MaterialTheme.colorScheme.onSurface
                )
            },
            text = {
                Text(
                    "Apakah Anda yakin ingin menghapus '${target.name}' dari stok dapur? Tindakan ini tidak dapat dibatalkan.",
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
            },
            confirmButton = {
                Button(
                    onClick = {
                        viewModel.deleteIngredient(target)
                        itemToDelete = null
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = BrandRed)
                ) {
                    Text("Hapus", color = Color.White)
                }
            },
            dismissButton = {
                OutlinedButton(
                    onClick = { itemToDelete = null },
                    colors = ButtonDefaults.outlinedButtonColors(
                        contentColor = MaterialTheme.colorScheme.onSurface
                    )
                ) {
                    Text("Batal")
                }
            }
        )
    }
}
