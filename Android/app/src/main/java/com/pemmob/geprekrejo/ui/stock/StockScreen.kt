package com.pemmob.geprekrejo.ui.stock

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Inventory2
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.pemmob.geprekrejo.data.model.StockItem
import java.text.NumberFormat
import java.util.Locale

private val BrandRed = Color(0xFFBC000A)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StockScreen(
    viewModel: StockViewModel,
    onBack: () -> Unit
) {
    val state by viewModel.uiState.collectAsStateWithLifecycle()
    val snackbar = remember { SnackbarHostState() }

    LaunchedEffect(state.error) {
        state.error?.let { snackbar.showSnackbar(it); viewModel.clearError() }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text("Stok Bahan Baku", fontWeight = FontWeight.Bold)
                        if (state.criticalCount > 0) {
                            Text(
                                "${state.criticalCount} item kritis",
                                style = MaterialTheme.typography.bodySmall,
                                color = BrandRed
                            )
                        }
                    }
                },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, "Kembali")
                    }
                },
                actions = {
                    IconButton(onClick = viewModel::loadStock) {
                        Icon(Icons.Default.Refresh, "Refresh")
                    }
                }
            )
        },
        snackbarHost = { SnackbarHost(snackbar) }
    ) { pad ->
        when {
            state.isLoading -> Box(
                Modifier.fillMaxSize().padding(pad),
                Alignment.Center
            ) {
                CircularProgressIndicator(color = BrandRed)
            }

            state.items.isEmpty() -> Box(
                Modifier.fillMaxSize().padding(pad),
                Alignment.Center
            ) {
                Text("Belum ada data stok.", color = MaterialTheme.colorScheme.onSurfaceVariant)
            }

            else -> LazyColumn(
                modifier = Modifier.padding(pad),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(state.items, key = { it.id }) { item ->
                    StockItemCard(item)
                }
            }
        }
    }
}

@Composable
private fun StockItemCard(item: StockItem) {
    val isCritical = item.isCritical

    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(14.dp),
        elevation = CardDefaults.cardElevation(2.dp),
        colors = if (isCritical) CardDefaults.cardColors(containerColor = Color(0xFFFFF5F5))
                 else CardDefaults.cardColors()
    ) {
        Row(
            modifier = Modifier.padding(16.dp).fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(12.dp),
                modifier = Modifier.weight(1f)
            ) {
                Box(
                    Modifier
                        .size(44.dp)
                        .clip(CircleShape)
                        .background(
                            if (isCritical) BrandRed.copy(alpha = 0.15f)
                            else MaterialTheme.colorScheme.primaryContainer
                        ),
                    Alignment.Center
                ) {
                    Icon(
                        if (isCritical) Icons.Default.Warning else Icons.Default.Inventory2,
                        contentDescription = null,
                        tint = if (isCritical) BrandRed else MaterialTheme.colorScheme.primary,
                        modifier = Modifier.size(22.dp)
                    )
                }

                Column {
                    Text(item.name, fontWeight = FontWeight.SemiBold)
                    Text(
                        "Stok: ${formatStock(item.currentStock)} ${item.unit}",
                        style = MaterialTheme.typography.bodySmall,
                        color = if (isCritical) BrandRed else MaterialTheme.colorScheme.onSurfaceVariant
                    )
                    Text(
                        "Min: ${formatStock(item.minimumStock)} ${item.unit}",
                        style = MaterialTheme.typography.labelSmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                }
            }

            if (isCritical) {
                Box(
                    Modifier
                        .clip(RoundedCornerShape(6.dp))
                        .background(BrandRed)
                        .padding(horizontal = 8.dp, vertical = 4.dp)
                ) {
                    Text(
                        "KRITIS",
                        style = MaterialTheme.typography.labelSmall,
                        fontWeight = FontWeight.Bold,
                        color = Color.White
                    )
                }
            } else {
                Box(
                    Modifier
                        .clip(RoundedCornerShape(6.dp))
                        .background(Color(0xFF2E7D32).copy(alpha = 0.1f))
                        .padding(horizontal = 8.dp, vertical = 4.dp)
                ) {
                    Text(
                        "AMAN",
                        style = MaterialTheme.typography.labelSmall,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF2E7D32)
                    )
                }
            }
        }
    }
}

private fun formatStock(value: Double): String {
    return if (value == value.toLong().toDouble()) {
        value.toLong().toString()
    } else {
        NumberFormat.getNumberInstance(Locale("id", "ID")).format(value)
    }
}
