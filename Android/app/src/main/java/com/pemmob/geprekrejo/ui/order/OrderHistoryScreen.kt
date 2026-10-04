package com.pemmob.geprekrejo.ui.order

import android.app.DatePickerDialog
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.material3.TabRowDefaults.tabIndicatorOffset
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.pemmob.geprekrejo.data.model.DailySummary
import com.pemmob.geprekrejo.data.model.OrderData
import java.text.NumberFormat
import java.text.SimpleDateFormat
import java.util.*

private val BrandRed = Color(0xFFBC000A)
private val BrandAmber = Color(0xFFED6C02)
private val BrandGreen = Color(0xFF2E7D32)
private val BrandBlue = Color(0xFF1976D2)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun OrderHistoryScreen(
    viewModel: OrderViewModel,
    onOpenDrawer: () -> Unit = {}
) {
    val state by viewModel.uiState.collectAsStateWithLifecycle()
    val snackbar = remember { SnackbarHostState() }
    val context = LocalContext.current

    LaunchedEffect(state.errorMessage) {
        state.errorMessage?.let {
            snackbar.showSnackbar(it)
            viewModel.clearError()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "Pesanan",
                        fontWeight = FontWeight.Bold,
                        style = MaterialTheme.typography.titleLarge
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onOpenDrawer) {
                        Icon(Icons.Default.Menu, "Menu")
                    }
                }
            )
        },
        snackbarHost = { SnackbarHost(snackbar) }
    ) { pad ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(pad)
                .background(MaterialTheme.colorScheme.surface)
        ) {
            // Tabs Harian / Bulanan
            TabRow(
                selectedTabIndex = if (state.mode == "harian") 0 else 1,
                containerColor = MaterialTheme.colorScheme.surface,
                contentColor = BrandRed,
                indicator = { tabPositions ->
                    TabRowDefaults.Indicator(
                        Modifier.tabIndicatorOffset(tabPositions[if (state.mode == "harian") 0 else 1]),
                        color = BrandRed
                    )
                }
            ) {
                Tab(
                    selected = state.mode == "harian",
                    onClick = { viewModel.setMode("harian") },
                    text = { Text("Harian", fontWeight = FontWeight.Bold, fontSize = 14.sp) },
                    selectedContentColor = BrandRed,
                    unselectedContentColor = MaterialTheme.colorScheme.onSurfaceVariant
                )
                Tab(
                    selected = state.mode == "bulanan",
                    onClick = { viewModel.setMode("bulanan") },
                    text = { Text("Bulanan", fontWeight = FontWeight.Bold, fontSize = 14.sp) },
                    selectedContentColor = BrandRed,
                    unselectedContentColor = MaterialTheme.colorScheme.onSurfaceVariant
                )
            }

            // Filters
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 8.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Date picker
                Surface(
                    onClick = {
                        val cal = Calendar.getInstance()
                        val isHarian = state.mode == "harian"

                        if (isHarian) {
                            val parts = state.tanggal.split("-")
                            if (parts.size == 3) {
                                cal.set(parts[0].toInt(), parts[1].toInt() - 1, parts[2].toInt())
                            }
                            DatePickerDialog(
                                context,
                                { _, y, m, d ->
                                    val formatted = String.format(Locale.US, "%04d-%02d-%02d", y, m + 1, d)
                                    viewModel.setTanggal(formatted)
                                },
                                cal.get(Calendar.YEAR),
                                cal.get(Calendar.MONTH),
                                cal.get(Calendar.DAY_OF_MONTH)
                            ).show()
                        } else {
                            val parts = state.bulan.split("-")
                            if (parts.size == 2) {
                                cal.set(parts[0].toInt(), parts[1].toInt() - 1, 1)
                            }
                            DatePickerDialog(
                                context,
                                { _, y, m, _ ->
                                    val formatted = String.format(Locale.US, "%04d-%02d", y, m + 1)
                                    viewModel.setBulan(formatted)
                                },
                                cal.get(Calendar.YEAR),
                                cal.get(Calendar.MONTH),
                                cal.get(Calendar.DAY_OF_MONTH)
                            ).show()
                        }
                    },
                    shape = RoundedCornerShape(12.dp),
                    border = androidx.compose.foundation.BorderStroke(1.dp, MaterialTheme.colorScheme.outline),
                    color = MaterialTheme.colorScheme.surface,
                    modifier = Modifier.weight(1f).height(48.dp)
                ) {
                    Row(
                        Modifier.padding(horizontal = 12.dp).fillMaxHeight(),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Text(if (state.mode == "harian") state.tanggal else state.bulan, style = MaterialTheme.typography.bodyMedium)
                        Icon(Icons.Default.CalendarToday, contentDescription = null, tint = BrandRed, modifier = Modifier.size(18.dp))
                    }
                }

                com.pemmob.geprekrejo.ui.components.CustomSearchBar(
                    value = state.search,
                    onValueChange = viewModel::setSearch,
                    placeholder = "Cari nomor pesanan...",
                    modifier = Modifier.weight(1.5f)
                )
            }

            // Summary Cards
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp),
                horizontalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                SummaryCard(
                    modifier = Modifier.weight(1f),
                    title = "PENDAPATAN",
                    value = formatRupiah(state.data?.totalRevenue ?: 0.0),
                    icon = Icons.Default.Payments,
                    iconTint = BrandRed,
                    containerColor = BrandRed.copy(alpha = 0.03f)
                )
                SummaryCard(
                    modifier = Modifier.weight(1f),
                    title = "TOTAL PESANAN",
                    value = (state.data?.totalOrders ?: 0).toString(),
                    icon = Icons.Default.ReceiptLong,
                    iconTint = BrandBlue,
                    containerColor = BrandBlue.copy(alpha = 0.03f)
                )
            }

            Spacer(Modifier.height(12.dp))

            // List
            if (state.isLoading) {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = BrandRed)
                }
            } else {
                if (state.mode == "harian") {
                    val orders = state.data?.orders?.data ?: emptyList()
                    if (orders.isEmpty()) {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Tidak ada pesanan", color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                    } else {
                        LazyColumn(
                            contentPadding = PaddingValues(16.dp),
                            verticalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            items(orders) { order ->
                                OrderItemCard(order)
                            }
                        }
                    }
                } else {
                    val summary = state.data?.dailySummary ?: emptyList()
                    if (summary.isEmpty()) {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Belum ada data untuk bulan ini", color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                    } else {
                        LazyColumn(
                            contentPadding = PaddingValues(16.dp),
                            verticalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            items(summary) { daily ->
                                DailySummaryCard(daily)
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun SummaryCard(
    modifier: Modifier = Modifier,
    title: String,
    value: String,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    iconTint: Color,
    containerColor: Color
) {
    Card(
        modifier = modifier,
        colors = CardDefaults.cardColors(containerColor = containerColor),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp),
        border = androidx.compose.foundation.BorderStroke(1.dp, iconTint.copy(alpha = 0.2f)),
        shape = RoundedCornerShape(12.dp)
    ) {
        Column(Modifier.padding(12.dp)) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier.size(28.dp).clip(CircleShape).background(iconTint.copy(alpha = 0.15f)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(icon, contentDescription = null, tint = iconTint, modifier = Modifier.size(16.dp))
                }
                Spacer(Modifier.width(6.dp))
                Text(title, style = MaterialTheme.typography.labelSmall, fontWeight = FontWeight.Bold, color = iconTint)
            }
            Spacer(Modifier.height(8.dp))
            Text(value, fontWeight = FontWeight.ExtraBold, fontSize = 16.sp, color = MaterialTheme.colorScheme.onSurface)
        }
    }
}

@Composable
fun OrderItemCard(order: OrderData) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Customer Info
            Box(
                modifier = Modifier
                    .size(40.dp)
                    .clip(CircleShape)
                    .background(BrandAmber.copy(alpha = 0.2f)),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    text = order.member?.name?.take(1)?.uppercase() ?: "G",
                    fontWeight = FontWeight.Bold,
                    color = BrandAmber
                )
            }
            Spacer(Modifier.width(12.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text(order.orderNumber, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                Text(
                    text = order.member?.name ?: "Guest",
                    style = MaterialTheme.typography.bodyMedium,
                    fontWeight = FontWeight.Medium
                )
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(
                        if (order.type == "dine_in") Icons.Default.Restaurant else Icons.Default.ShoppingBag,
                        contentDescription = null,
                        modifier = Modifier.size(12.dp),
                        tint = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                    Spacer(Modifier.width(4.dp))
                    Text(
                        text = if (order.type == "dine_in") "Dine In (Meja ${order.tableNumber ?: "-"})" else "Take Away (Antrian ${order.queueNumber ?: "-"})",
                        style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                }
            }

            Column(horizontalAlignment = Alignment.End) {
                Text(
                    text = formatRupiah(order.totalAmount),
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp
                )
                Spacer(Modifier.height(4.dp))

                val statusColor = when (order.status) {
                    "completed" -> BrandGreen
                    "confirmed" -> BrandBlue
                    "preparing" -> BrandAmber
                    else -> Color.Gray
                }

                val statusText = when (order.status) {
                    "completed" -> "Selesai"
                    "confirmed" -> "Dikonfirmasi"
                    "preparing" -> "Disiapkan"
                    "pending" -> "Menunggu"
                    "cancelled" -> "Batal"
                    else -> order.status
                }

                Surface(
                    color = statusColor.copy(alpha = 0.1f),
                    shape = RoundedCornerShape(4.dp),
                    contentColor = statusColor
                ) {
                    Text(
                        text = statusText,
                        modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp),
                        fontSize = 10.sp,
                        fontWeight = FontWeight.Bold
                    )
                }

                Spacer(Modifier.height(4.dp))
                // Time (extract HH:mm from YYYY-MM-DD HH:mm:ss if possible)
                val timeStr = try {
                    val sdf = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", Locale.getDefault()) // typical laravel created_at
                    val date = sdf.parse(order.createdAt)
                    if (date != null) {
                        SimpleDateFormat("HH:mm", Locale.getDefault()).format(date)
                    } else order.createdAt
                } catch (e: Exception) {
                    try {
                        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                        val date = sdf.parse(order.createdAt)
                        if (date != null) {
                            SimpleDateFormat("HH:mm", Locale.getDefault()).format(date)
                        } else order.createdAt
                    } catch (e2: Exception) {
                        order.createdAt
                    }
                }
                Text(timeStr, style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
        }
    }
}

@Composable
fun DailySummaryCard(daily: DailySummary) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        shape = RoundedCornerShape(16.dp)
    ) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier.size(48.dp).clip(RoundedCornerShape(12.dp)).background(BrandRed.copy(alpha = 0.08f)),
                contentAlignment = Alignment.Center
            ) {
                Icon(Icons.Default.EventNote, contentDescription = null, tint = BrandRed, modifier = Modifier.size(24.dp))
            }
            Spacer(Modifier.width(16.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(daily.tanggal, fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Spacer(Modifier.height(4.dp))
                Text("${daily.totalPesanan} Pesanan • ${daily.terbayar} Terbayar", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
            Column(horizontalAlignment = Alignment.End) {
                Text(
                    text = formatRupiah(daily.revenue),
                    fontWeight = FontWeight.ExtraBold,
                    color = BrandRed,
                    fontSize = 15.sp
                )
                Spacer(Modifier.height(4.dp))
                Icon(Icons.Default.ChevronRight, contentDescription = null, tint = MaterialTheme.colorScheme.outline, modifier = Modifier.size(16.dp))
            }
        }
    }
}

fun formatRupiah(amount: Double): String {
    val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
    return format.format(amount).replace("Rp", "Rp ").substringBefore(",")
}
