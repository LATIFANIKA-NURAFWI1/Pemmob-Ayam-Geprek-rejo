package com.pemmob.geprekrejo.ui.menu

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Menu
import androidx.compose.material.icons.filled.RestaurantMenu
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.pemmob.geprekrejo.data.model.MenuItem
import com.pemmob.geprekrejo.ui.menu.components.CategoryFilterBar
import com.pemmob.geprekrejo.ui.menu.components.MenuCard
import com.pemmob.geprekrejo.ui.theme.BrandRed

/**
 * Halaman 1: Daftar Menu Makanan (Menu List)
 * Menyesuaikan tema sistem perangkat (Dark Mode / Light Mode):
 * - Header dengan judul "Menu Makanan", subtitle, dan tombol "+ Tambah".
 * - Filter kategori pills + filter status ketersediaan + Search bar.
 * - Grid 2 kolom kartu menu (Card) dengan foto, badge, nama, harga, dan tombol aksi.
 * - Dialog konfirmasi hapus menu.
 */
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MenuListScreen(
    viewModel: MenuViewModel,
    onNavigateToAddMenu: () -> Unit,
    onNavigateToEditMenu: (MenuItem) -> Unit,
    onOpenDrawer: () -> Unit = {},
    modifier: Modifier = Modifier
) {
    val state by viewModel.uiState.collectAsStateWithLifecycle()
    val snackbarHostState = remember { SnackbarHostState() }

    // State untuk konfirmasi dialog hapus
    var itemToDelete by remember { mutableStateOf<MenuItem?>(null) }

    // Tampilkan snackbar jika ada pesan sukses / status update
    LaunchedEffect(state.userMessage) {
        state.userMessage?.let {
            snackbarHostState.showSnackbar(it)
            viewModel.clearUserMessage()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "Menu Makanan",
                        fontWeight = FontWeight.Bold,
                        style = MaterialTheme.typography.titleLarge
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onOpenDrawer) {
                        Icon(Icons.Default.Menu, contentDescription = "Menu", tint = MaterialTheme.colorScheme.onSurface)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.background
                )
            )
        },
        snackbarHost = { SnackbarHost(snackbarHostState) },
        floatingActionButton = {
            FloatingActionButton(
                onClick = onNavigateToAddMenu,
                containerColor = BrandRed,
                contentColor = Color.White
            ) {
                Icon(Icons.Default.Add, contentDescription = "Tambah Menu")
            }
        },
        containerColor = MaterialTheme.colorScheme.background,
        modifier = modifier
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(horizontal = 16.dp)
                .padding(top = 8.dp),
            verticalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            // ── Filter Bar (Search + Dropdown Status + Category Pills) ────────
            CategoryFilterBar(
                searchQuery = state.searchQuery,
                onSearchChange = viewModel::onSearchQueryChange,
                categories = state.categories,
                selectedCategoryId = state.selectedCategoryId,
                onCategorySelected = viewModel::onCategorySelect,
                selectedStatus = state.selectedStatus,
                onStatusSelected = viewModel::onStatusSelect
            )

            // ── 3. Grid Daftar Menu (Menu Items Grid) ──────────────────────────
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
                // Tampilan Empty State
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
                            imageVector = Icons.Default.RestaurantMenu,
                            contentDescription = null,
                            tint = MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.4f),
                            modifier = Modifier.size(64.dp)
                        )
                        Text(
                            text = "Tidak ada menu yang ditemukan",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.SemiBold,
                            color = MaterialTheme.colorScheme.onSurface
                        )
                        Text(
                            text = "Coba ubah kata kunci pencarian atau ganti filter kategori.",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }
            } else {
                // Grid Kartu Menu (2 Kolom)
                LazyVerticalGrid(
                    columns = GridCells.Fixed(2),
                    contentPadding = PaddingValues(bottom = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                    modifier = Modifier.weight(1f)
                ) {
                    items(state.filteredItems, key = { it.id }) { item ->
                        MenuCard(
                            item = item,
                            onEditClick = { onNavigateToEditMenu(item) },
                            onDeleteClick = { itemToDelete = item },
                            onToggleStatusClick = { viewModel.toggleAvailability(item) }
                        )
                    }
                }
            }
        }
    }

    // ── Dialog Konfirmasi Hapus ───────────────────────────────────────────────
    if (itemToDelete != null) {
        val target = itemToDelete!!
        AlertDialog(
            onDismissRequest = { itemToDelete = null },
            title = {
                Text(
                    text = "Hapus Menu?",
                    fontWeight = FontWeight.Bold,
                    color = MaterialTheme.colorScheme.onSurface
                )
            },
            text = {
                Text(
                    "Apakah Anda yakin ingin menghapus '${target.name}'? Tindakan ini tidak dapat dibatalkan.",
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
            },
            confirmButton = {
                Button(
                    onClick = {
                        viewModel.deleteMenu(target)
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
