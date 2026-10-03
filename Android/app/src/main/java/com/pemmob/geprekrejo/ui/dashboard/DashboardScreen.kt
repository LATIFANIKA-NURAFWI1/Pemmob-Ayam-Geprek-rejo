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
import androidx.compose.ui.unit.sp
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
            Text("Selamat datang di Sistem Self-Order Geprek Rejo \uD83D\uDC4B", 
                style = MaterialTheme.typography.bodyMedium, 
                color = MaterialTheme.colorScheme.onSurfaceVariant)
            Spacer(Modifier.height(16.dp))
            StatsGrid(stats = data.stats, onNavigateToStock = onNavigateToStock)
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

// ── Stats Grid (Vertical) ──────────────────────────────────────────────────

@Composable
private fun StatsGrid(stats: DashboardStats, onNavigateToStock: () -> Unit) {
    Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            StatCard(
                modifier = Modifier.weight(1f),
                icon = Icons.Default.ShoppingCart,
                iconTint = BrandRed,
                iconBg = Color(0xFFFDE8E8),
                label = "Total Pesanan",
                value = stats.totalPesanan.toString(),
                subtitle = "${stats.paidCount} terbayar"
            )
            StatCard(
                modifier = Modifier.weight(1f),
                icon = Icons.Outlined.Payments,
                iconTint = Color(0xFFF59E0B), // Yellow for Omset
                iconBg = Color(0xFFFEF3C7),
                label = "Omset Hari Ini",
                value = formatRp(stats.omset),
                isAmount = true,
                valueColor = Color(0xFFF59E0B)
            )
        }
        Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            StatCard(
                modifier = Modifier.weight(1f),
                icon = Icons.Default.TrendingUp,
                iconTint = BrandRed,
                iconBg = Color(0xFFFDE8E8),
                label = "Laba Kotor",
                value = formatRp(stats.grossProfit),
                isAmount = true
            )
            StatCard(
                modifier = Modifier.weight(1f),
                icon = Icons.Default.Restaurant,
                iconTint = Color(0xFF6B7280), // Gray for Menu Aktif
                iconBg = Color(0xFFF3F4F6),
                label = "Menu Aktif",
                value = stats.menuAktif.toString()
            )
        }
        CriticalStockCard(count = stats.stokKritis, onClick = onNavigateToStock)
    }
}

@Composable
private fun StatCard(
    modifier: Modifier = Modifier,
    icon: ImageVector, iconTint: Color, iconBg: Color,
    label: String, value: String, subtitle: String? = null, isAmount: Boolean = false,
    valueColor: Color = MaterialTheme.colorScheme.onSurface
) {
    Card(modifier = modifier, shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
            Box(Modifier.size(40.dp).clip(CircleShape).background(iconBg), Alignment.Center) {
                Icon(icon, null, tint = iconTint, modifier = Modifier.size(20.dp))
            }
            Text(label, style = MaterialTheme.typography.labelSmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant)
            Text(value, style = if (isAmount) MaterialTheme.typography.titleMedium
                else MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold, maxLines = 1, overflow = TextOverflow.Ellipsis,
                color = valueColor)
            if (subtitle != null) {
                Text(subtitle, style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant)
            } else {
                Spacer(Modifier.height(16.dp)) // Maintain height if no subtitle
            }
        }
    }
}

@Composable
private fun CriticalStockCard(count: Int, onClick: () -> Unit) {
    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        border = androidx.compose.foundation.BorderStroke(2.dp, BrandRed),
        elevation = CardDefaults.cardElevation(2.dp)) {
        
        // Background decoration like in web
        Box {
            // Decorative background circle at top right
            Box(
                Modifier
                    .align(Alignment.TopEnd)
                    .size(100.dp)
                    .offset(x = 20.dp, y = (-20).dp)
                    .clip(CircleShape)
                    .background(BrandRedBg.copy(alpha = 0.5f))
            )
            
            Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                Box(Modifier.size(40.dp).clip(CircleShape).background(BrandRedBg), Alignment.Center) {
                    Icon(Icons.Default.Warning, null, tint = BrandRed, modifier = Modifier.size(20.dp))
                }
                Text("Bahan Stok Rendah", style = MaterialTheme.typography.labelMedium,
                    fontWeight = FontWeight.Bold, color = BrandRed)
                Text(count.toString(), style = MaterialTheme.typography.headlineMedium,
                    fontWeight = FontWeight.Bold, color = BrandRed)
                Button(onClick = onClick, modifier = Modifier.width(140.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = BrandRed),
                    shape = RoundedCornerShape(8.dp),
                    contentPadding = PaddingValues(vertical = 8.dp)) {
                    Text("Cek Stok →", style = MaterialTheme.typography.labelMedium, fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

// ── Recent Orders ─────────────────────────────────────────────────────────────

@Composable
private fun RecentOrdersCard(orders: List<RecentOrderItem>) {
    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
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
                            Text(order.orderNumber, fontWeight = FontWeight.Bold)
                            // Parse time if possible, fallback to original. Also show item count
                            val timeStr = try {
                                val sdfIn = java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                                val date = sdfIn.parse(order.createdAt)
                                if (date != null) {
                                    java.text.SimpleDateFormat("HH:mm", Locale.getDefault()).format(date)
                                } else order.createdAt.take(5)
                            } catch (e: Exception) {
                                order.createdAt.take(5)
                            }
                            val totalItems = order.items.sumOf { it.quantity }
                            Text("$timeStr · $totalItems item",
                                style = MaterialTheme.typography.bodySmall,
                                color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                        Spacer(Modifier.width(8.dp))
                        Column(horizontalAlignment = Alignment.End) {
                            Text(formatRp(order.totalAmount), fontWeight = FontWeight.Bold, fontSize = 14.sp)
                            Spacer(Modifier.height(4.dp))
                            StatusBadge(order.status)
                        }
                    }
                    if (i < orders.lastIndex) HorizontalDivider(Modifier.padding(vertical = 12.dp), color = MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.5f))
                }
            }
        }
    }
}

@Composable
private fun StatusBadge(status: String) {
    val (bg, fg, label) = when (status) {
        "pending"   -> Triple(Color(0xFFFFF8E1), Color(0xFFF59E0B), "MENUNGGU")
        "confirmed" -> Triple(Color(0xFFFFF8E1), Color(0xFFF59E0B), "KONFIRMASI")
        "preparing" -> Triple(Color(0xFFFFF8E1), Color(0xFFF59E0B), "DIPROSES")
        "completed" -> Triple(Color(0xFFFFF8E1), Color(0xFFF59E0B), "SELESAI")
        else        -> Triple(Color(0xFFEEEEEE), Color(0xFF616161), status.uppercase())
    }
    Surface(
        shape = RoundedCornerShape(12.dp),
        color = Color.Transparent,
        border = androidx.compose.foundation.BorderStroke(1.dp, fg.copy(alpha = 0.5f))
    ) {
        Text(label, style = MaterialTheme.typography.labelSmall.copy(fontSize = 10.sp), 
            color = fg, fontWeight = FontWeight.Bold,
            modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp))
    }
}

// ── Top Menu ──────────────────────────────────────────────────────────────────

@Composable
private fun TopMenuCard(topMenus: List<TopMenuItem>) {
    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp)) {
            if (topMenus.isEmpty()) {
                Text("Belum ada penjualan hari ini.", color = MaterialTheme.colorScheme.onSurfaceVariant)
            } else {
                topMenus.forEachIndexed { i, menu ->
                    Row(Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically) {
                        Row(verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(16.dp),
                            modifier = Modifier.weight(1f)) {
                            // Rank Circle
                            val isTop3 = i < 3
                            val circleBg = if (isTop3) BrandRed else Color(0xFFFFF8E1)
                            val textColor = if (isTop3) Color.White else Color(0xFFF59E0B)
                            
                            Box(Modifier.size(32.dp).clip(CircleShape).background(circleBg),
                                Alignment.Center) {
                                Text("${i+1}", style = MaterialTheme.typography.labelLarge,
                                    fontWeight = FontWeight.Bold, color = textColor)
                            }
                            
                            Column {
                                Text(menu.name, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                                // We don't have category in TopMenuItem unfortunately, so we can just show price or a dummy
                                Text("Menu", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                            }
                        }
                        Text("${menu.totalTerjual}x",
                            fontWeight = FontWeight.Bold,
                            color = MaterialTheme.colorScheme.onSurface)
                    }
                    if (i < topMenus.lastIndex) HorizontalDivider(Modifier.padding(vertical = 12.dp), color = MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.5f))
                }
            }
        }
    }
}

private fun formatRp(amount: Double) =
    "Rp ${NumberFormat.getNumberInstance(Locale("id","ID")).format(amount.toLong())}"
