package com.pemmob.geprekrejo.ui.dashboard

import androidx.compose.animation.core.animateFloatAsState
import androidx.compose.animation.core.tween
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.Payments
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.StrokeCap
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.pemmob.geprekrejo.data.model.*
import java.text.NumberFormat
import java.util.Locale

// Warna brand dari web: primary = #BC000A
private val BrandRed   = Color(0xFFBC000A)
private val BrandRedBg = Color(0x1FBC000A)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen(
    viewModel: DashboardViewModel,
    onNavigateToStock: () -> Unit = {},
    onLogoutClick: () -> Unit = {}
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
                        Text("Dashboard", fontWeight = FontWeight.Bold)
                        Text("Ringkasan hari ini",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant)
                    }
                },
                actions = {
                    IconButton(onClick = viewModel::loadDashboard) {
                        Icon(Icons.Default.Refresh, "Refresh")
                    }
                    IconButton(onClick = onLogoutClick) {
                        Icon(Icons.Default.Logout, "Keluar", tint = MaterialTheme.colorScheme.error)
                    }
                }
            )
        },
        snackbarHost = { SnackbarHost(snackbar) }
    ) { pad ->
        when {
            state.isLoading -> Box(Modifier.fillMaxSize().padding(pad), Alignment.Center) {
                CircularProgressIndicator(color = BrandRed)
            }
            state.data != null -> DashboardContent(
                data = state.data!!,
                onNavigateToStock = onNavigateToStock,
                modifier = Modifier.padding(pad)
            )
            else -> Box(Modifier.fillMaxSize().padding(pad), Alignment.Center) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text("Gagal memuat data", color = MaterialTheme.colorScheme.error)
                    Spacer(Modifier.height(8.dp))
                    Button(onClick = viewModel::loadDashboard,
                        colors = ButtonDefaults.buttonColors(containerColor = BrandRed)) {
                        Text("Coba Lagi")
                    }
                }
            }
        }
    }
}

@Composable
private fun DashboardContent(
    data: DashboardData,
    onNavigateToStock: () -> Unit,
    modifier: Modifier = Modifier
) {
    LazyColumn(
        modifier = modifier.fillMaxSize(),
        contentPadding = PaddingValues(16.dp),
        verticalArrangement = Arrangement.spacedBy(20.dp)
    ) {
        item {
            Text("Statistik Hari Ini", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
            Spacer(Modifier.height(10.dp))
            StatsRow(stats = data.stats, onNavigateToStock = onNavigateToStock)
        }
        item {
            Text("Pesanan Terkini", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
            Spacer(Modifier.height(10.dp))
            RecentOrdersCard(orders = data.recentOrders)
        }
        item {
            Text("Menu Terlaris", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
            Spacer(Modifier.height(10.dp))
            TopMenuCard(topMenus = data.topMenus)
        }
    }
}

// ── Stats Row ─────────────────────────────────────────────────────────────────

@Composable
private fun StatsRow(stats: DashboardStats, onNavigateToStock: () -> Unit) {
    LazyRow(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
        item { StatCard(Icons.Default.ShoppingCart, BrandRed, BrandRedBg, "Total Pesanan",
            stats.totalPesanan.toString(), subtitle = "${stats.paidCount} terbayar") }
        item { StatCard(Icons.Outlined.Payments, Color(0xFFD4842A), Color(0x1FD4842A), "Omset",
            formatRp(stats.omset), isAmount = true) }
        item { StatCard(Icons.Default.TrendingUp, Color(0xFF2E7D32), Color(0x1F2E7D32), "Laba Kotor",
            formatRp(stats.grossProfit), isAmount = true) }
        item { StatCard(Icons.Default.Restaurant, Color(0xFF1565C0), Color(0x1F1565C0), "Menu Aktif",
            stats.menuAktif.toString()) }
        item { CriticalStockCard(stats.stokKritis, onNavigateToStock) }
    }
}

@Composable
private fun StatCard(
    icon: ImageVector, iconTint: Color, iconBg: Color,
    label: String, value: String, subtitle: String? = null, isAmount: Boolean = false
) {
    Card(Modifier.width(160.dp), shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(3.dp)) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Box(Modifier.size(40.dp).clip(CircleShape).background(iconBg), Alignment.Center) {
                Icon(icon, null, tint = iconTint, modifier = Modifier.size(20.dp))
            }
            Text(label, style = MaterialTheme.typography.labelSmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant)
            Text(value, style = if (isAmount) MaterialTheme.typography.titleMedium
                else MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold, maxLines = 1, overflow = TextOverflow.Ellipsis)
            subtitle?.let { Text(it, style = MaterialTheme.typography.labelSmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant) }
        }
    }
}

@Composable
private fun CriticalStockCard(count: Int, onClick: () -> Unit) {
    Card(Modifier.width(190.dp), shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0xFFFFF0F0)),
        border = androidx.compose.foundation.BorderStroke(2.dp, BrandRed),
        elevation = CardDefaults.cardElevation(4.dp)) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Box(Modifier.size(40.dp).clip(CircleShape).background(BrandRedBg), Alignment.Center) {
                Icon(Icons.Default.Warning, null, tint = BrandRed, modifier = Modifier.size(20.dp))
            }
            Text("Stok Kritis", style = MaterialTheme.typography.labelMedium,
                fontWeight = FontWeight.Bold, color = BrandRed)
            Text(count.toString(), style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold, color = BrandRed)
            Button(onClick = onClick, modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.buttonColors(containerColor = BrandRed),
                shape = RoundedCornerShape(8.dp),
                contentPadding = PaddingValues(vertical = 6.dp)) {
                Text("Cek Stok →", style = MaterialTheme.typography.labelSmall, fontWeight = FontWeight.Bold)
            }
        }
    }
}

// ── Recent Orders ─────────────────────────────────────────────────────────────

@Composable
private fun RecentOrdersCard(orders: List<RecentOrderItem>) {
    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp)) {
            if (orders.isEmpty()) {
                Text("Belum ada pesanan hari ini.", color = MaterialTheme.colorScheme.onSurfaceVariant)
            } else {
                orders.forEachIndexed { i, order ->
                    Row(Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically) {
                        Column(Modifier.weight(1f)) {
                            Text("#${order.queueNumber} · ${order.orderNumber}",
                                fontWeight = FontWeight.SemiBold)
                            Text(order.items.joinToString(", ") { "${it.name} ×${it.quantity}" },
                                style = MaterialTheme.typography.bodySmall,
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                                maxLines = 1, overflow = TextOverflow.Ellipsis)
                        }
                        Spacer(Modifier.width(8.dp))
                        Column(horizontalAlignment = Alignment.End) {
                            StatusBadge(order.status)
                            Text(formatRp(order.totalAmount),
                                style = MaterialTheme.typography.labelSmall,
                                color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                    }
                    if (i < orders.lastIndex) HorizontalDivider(Modifier.padding(vertical = 8.dp))
                }
            }
        }
    }
}

@Composable
private fun StatusBadge(status: String) {
    val (bg, fg, label) = when (status) {
        "pending"   -> Triple(Color(0xFFFFF3E0), Color(0xFFE65100), "Menunggu")
        "confirmed" -> Triple(Color(0xFFE3F2FD), Color(0xFF1565C0), "Konfirmasi")
        "preparing" -> Triple(Color(0xFFE8F5E9), Color(0xFF2E7D32), "Diproses")
        "completed" -> Triple(Color(0xFFEEEEEE), Color(0xFF616161), "Selesai")
        else        -> Triple(Color(0xFFEEEEEE), Color(0xFF616161), status)
    }
    Box(Modifier.clip(RoundedCornerShape(6.dp)).background(bg).padding(horizontal = 6.dp, vertical = 2.dp)) {
        Text(label, style = MaterialTheme.typography.labelSmall, color = fg)
    }
}

// ── Top Menu ──────────────────────────────────────────────────────────────────

@Composable
private fun TopMenuCard(topMenus: List<TopMenuItem>) {
    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp)) {
            if (topMenus.isEmpty()) {
                Text("Belum ada penjualan hari ini.", color = MaterialTheme.colorScheme.onSurfaceVariant)
            } else {
                val maxSold = topMenus.maxOf { it.totalTerjual }.toFloat()
                topMenus.forEachIndexed { i, menu ->
                    val progress by animateFloatAsState(
                        targetValue = menu.totalTerjual / maxSold,
                        animationSpec = tween(700), label = "p$i"
                    )
                    Column(Modifier.fillMaxWidth()) {
                        Row(Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically) {
                            Row(verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                Box(Modifier.size(24.dp).clip(CircleShape)
                                    .background(if (i == 0) BrandRed else MaterialTheme.colorScheme.surfaceVariant),
                                    Alignment.Center) {
                                    Text("${i+1}", style = MaterialTheme.typography.labelSmall,
                                        fontWeight = FontWeight.Bold,
                                        color = if (i == 0) Color.White else MaterialTheme.colorScheme.onSurfaceVariant)
                                }
                                Text(menu.name, fontWeight = FontWeight.Medium)
                            }
                            Text("${menu.totalTerjual} terjual",
                                style = MaterialTheme.typography.labelMedium,
                                color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                        Spacer(Modifier.height(4.dp))
                        LinearProgressIndicator(progress = { progress },
                            modifier = Modifier.fillMaxWidth().height(6.dp).clip(RoundedCornerShape(3.dp)),
                            strokeCap = StrokeCap.Round,
                            color = if (i == 0) BrandRed else MaterialTheme.colorScheme.primary,
                            trackColor = MaterialTheme.colorScheme.surfaceVariant)
                    }
                    if (i < topMenus.lastIndex) Spacer(Modifier.height(12.dp))
                }
            }
        }
    }
}

private fun formatRp(amount: Double) =
    "Rp ${NumberFormat.getNumberInstance(Locale("id","ID")).format(amount.toLong())}"
