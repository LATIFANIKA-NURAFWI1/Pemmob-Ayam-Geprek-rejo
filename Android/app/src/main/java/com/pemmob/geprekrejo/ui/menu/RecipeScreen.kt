package com.pemmob.geprekrejo.ui.menu

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.pemmob.geprekrejo.data.model.RecipeItem
import com.pemmob.geprekrejo.data.model.StockItem
import com.pemmob.geprekrejo.ui.theme.BrandRed
import java.text.NumberFormat
import java.util.Locale

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun RecipeScreen(
    menuId: Int,
    onBack: () -> Unit,
    viewModel: RecipeViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()

    LaunchedEffect(menuId) {
        viewModel.loadData(menuId)
    }

    LaunchedEffect(uiState.successMessage, uiState.error) {
        if (uiState.successMessage != null || uiState.error != null) {
            // Usually we show a Snackbar here, for now just clear after a delay
            kotlinx.coroutines.delay(2000)
            viewModel.clearMessages()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Atur Resep - ${uiState.menuName}") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Kembali")
                    }
                },
                actions = {
                    if (!uiState.isLoading) {
                        TextButton(
                            onClick = { viewModel.saveRecipes() },
                            enabled = !uiState.isSaving
                        ) {
                            Text(if (uiState.isSaving) "Menyimpan..." else "Simpan", color = Color.White)
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = BrandRed,
                    titleContentColor = Color.White,
                    navigationIconContentColor = Color.White,
                    actionIconContentColor = Color.White
                )
            )
        },
        containerColor = MaterialTheme.colorScheme.background
    ) { padding ->
        Box(modifier = Modifier.padding(padding).fillMaxSize()) {
            if (uiState.isLoading) {
                CircularProgressIndicator(modifier = Modifier.align(Alignment.Center))
            } else {
                Column(modifier = Modifier.fillMaxSize()) {
                    // Total HPP Summary
                    Card(
                        modifier = Modifier.fillMaxWidth().padding(16.dp),
                        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                        border = BorderStroke(1.dp, MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.5f))
                    ) {
                        Column(modifier = Modifier.padding(16.dp)) {
                            Text(
                                text = "Estimasi HPP per porsi:",
                                style = MaterialTheme.typography.bodyMedium
                            )
                            val formatRp = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
                            formatRp.maximumFractionDigits = 0
                            Text(
                                text = formatRp.format(uiState.totalHpp),
                                style = MaterialTheme.typography.titleLarge,
                                fontWeight = FontWeight.Bold,
                                color = MaterialTheme.colorScheme.primary
                            )
                        }
                    }

                    // Ingredient Selection
                    var showDropdown by remember { mutableStateOf(false) }
                    Box(modifier = Modifier.padding(horizontal = 16.dp).fillMaxWidth()) {
                        Button(
                            onClick = { showDropdown = true },
                            modifier = Modifier.fillMaxWidth(),
                            colors = ButtonDefaults.buttonColors(containerColor = BrandRed)
                        ) {
                            Icon(Icons.Default.Add, contentDescription = null, tint = Color.White)
                            Spacer(Modifier.width(8.dp))
                            Text("Tambah Bahan Baku", color = Color.White)
                        }
                        DropdownMenu(
                            expanded = showDropdown,
                            onDismissRequest = { showDropdown = false }
                        ) {
                            val available = uiState.availableIngredients.filter { stock -> 
                                uiState.recipes.none { recipe -> recipe.stockIngredientId == stock.id } 
                            }
                            if (available.isEmpty()) {
                                DropdownMenuItem(
                                    text = { Text("Semua bahan sudah ditambahkan") },
                                    onClick = { showDropdown = false }
                                )
                            } else {
                                available.forEach { stock ->
                                    DropdownMenuItem(
                                        text = { Text(stock.name) },
                                        onClick = {
                                            viewModel.addIngredient(stock)
                                            showDropdown = false
                                        }
                                    )
                                }
                            }
                        }
                    }

                    Spacer(Modifier.height(16.dp))

                    // Recipe List
                    LazyColumn(
                        contentPadding = PaddingValues(16.dp),
                        verticalArrangement = Arrangement.spacedBy(8.dp),
                        modifier = Modifier.fillMaxWidth().weight(1f)
                    ) {
                        items(
                            items = uiState.recipes,
                            key = { it.stockIngredientId }
                        ) { recipe ->
                            RecipeItemCard(
                                item = recipe,
                                onQtyChange = { newQty -> 
                                    viewModel.updateQuantity(recipe.stockIngredientId, newQty) 
                                },
                                onRemove = { 
                                    viewModel.removeIngredient(recipe.stockIngredientId) 
                                }
                            )
                        }
                    }
                }
            }

            // Message Overlays
            if (uiState.error != null) {
                Snackbar(
                    modifier = Modifier.align(Alignment.BottomCenter).padding(16.dp),
                    containerColor = MaterialTheme.colorScheme.error
                ) {
                    Text(uiState.error!!, color = MaterialTheme.colorScheme.onError)
                }
            }
            if (uiState.successMessage != null) {
                Snackbar(
                    modifier = Modifier.align(Alignment.BottomCenter).padding(16.dp),
                    containerColor = Color(0xFF4CAF50)
                ) {
                    Text(uiState.successMessage!!, color = Color.White)
                }
            }
        }
    }
}

@Composable
fun RecipeItemCard(
    item: RecipeItem,
    onQtyChange: (Double) -> Unit,
    onRemove: () -> Unit
) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        border = BorderStroke(1.dp, MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.5f))
    ) {
        Row(
            modifier = Modifier.padding(12.dp).fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(text = item.ingredientName ?: "Bahan", fontWeight = FontWeight.Bold)
                
                val formatRp = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
                formatRp.maximumFractionDigits = 2
                val cost = item.unitCost ?: 0.0
                Text(
                    text = "${formatRp.format(cost)} / ${item.unit ?: ""}",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
            }
            
            OutlinedTextField(
                value = if (item.qtyUsed == 0.0) "" else item.qtyUsed.toString(),
                onValueChange = { 
                    val value = it.toDoubleOrNull() ?: 0.0
                    onQtyChange(value)
                },
                modifier = Modifier.width(100.dp),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                singleLine = true,
                suffix = { Text(item.unit ?: "") },
                label = { Text("Qty") }
            )
            
            IconButton(onClick = onRemove) {
                Icon(Icons.Default.Delete, contentDescription = "Hapus", tint = MaterialTheme.colorScheme.error)
            }
        }
    }
}
