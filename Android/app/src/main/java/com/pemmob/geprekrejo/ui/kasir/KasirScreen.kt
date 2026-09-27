package com.pemmob.geprekrejo.ui.kasir

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.Logout
import androidx.compose.material.icons.filled.Receipt
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import java.text.NumberFormat
import java.util.Locale

// Warna brand sama seperti dashboard owner
private val BrandRed   = Color(0xFFBC000A)
private val BrandRedBg = Color(0x1FBC000A)
private val GreenOk    = Color(0xFF2E7D32)
private val BlueInfo   = Color(0xFF1565C0)
private val BlueInfoBg = Color(0x1F1565C0)

fun formatRupiah(amount: Double): String {
    val format = NumberFormat.getCurrencyInstance(Locale.forLanguageTag("id-ID"))
    return format.format(amount).replace(",00", "")
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun KasirScreen(
    viewModel: KasirViewModel = viewModel(),
    onLogout: () -> Unit = {}
) {
    val state by viewModel.uiState.collectAsState()

    var selectedTab      by remember { mutableStateOf(0) }
    var showConfirmDialog by remember { mutableStateOf(false) }
    var selectedOrderId   by remember { mutableStateOf<Int?>(null) }
    var showCancelDialog  by remember { mutableStateOf(false) }
    var cancelReason      by remember { mutableStateOf("") }
    var showLogoutDialog  by remember { mutableStateOf(false) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text("Kasir Geprek Rejo", fontWeight = FontWeight.Bold)
                        Text(
                            "${state.pending.size} pesanan menunggu",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                },
                actions = {
                    IconButton(onClick = { viewModel.refresh() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh")
                    }
                }
            )
        },
        bottomBar = {
            NavigationBar(
                containerColor = MaterialTheme.colorScheme.surface,
                contentColor = MaterialTheme.colorScheme.onSurface
            ) {
                NavigationBarItem(
                    selected = selectedTab == 0,
                    onClick = { selectedTab = 0 },
                    icon = {
                        BadgedBox(badge = {
                            if (state.pending.isNotEmpty())
                                Badge(containerColor = BrandRed) {
                                    Text("${state.pending.size}", color = Color.White, fontSize = 10.sp)
                                }
                        }) {
                            Icon(Icons.Default.Receipt, contentDescription = "Menunggu Bayar")
                        }
                    },
                    label = { Text("Menunggu Bayar") },
                    colors = NavigationBarItemDefaults.colors(
                        indicatorColor = BrandRed.copy(alpha = 0.15f),
                        selectedIconColor = BrandRed,
                        selectedTextColor = BrandRed
                    )
                )

                NavigationBarItem(
                    selected = selectedTab == 1,
                    onClick = { selectedTab = 1 },
                    icon = {
                        BadgedBox(badge = {
                            if (state.proses.isNotEmpty())
                                Badge(containerColor = BlueInfo) {
                                    Text("${state.proses.size}", color = Color.White, fontSize = 10.sp)
                                }
                        }) {
                            Icon(Icons.Default.Restaurant, contentDescription = "Diproses")
                        }
                    },
                    label = { Text("Diproses") },
                    colors = NavigationBarItemDefaults.colors(
                        indicatorColor = BrandRed.copy(alpha = 0.15f),
                        selectedIconColor = BrandRed,
                        selectedTextColor = BrandRed
                    )
                )

                NavigationBarItem(
                    selected = false,
                    onClick = { showLogoutDialog = true },
                    icon = {
                        Icon(Icons.Default.Logout, contentDescription = "Keluar",
                            tint = MaterialTheme.colorScheme.error)
                    },
                    label = { Text("Keluar", color = MaterialTheme.colorScheme.error) }
                )
            }
        }
    ) { padding ->
        when (selectedTab) {
            0 -> if (state.pending.isEmpty()) {
                KasirEmptyState("Tidak ada pesanan menunggu pembayaran",
                    modifier = Modifier.fillMaxSize().padding(padding))
            } else {
                LazyColumn(
                    Modifier.fillMaxSize().padding(padding),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    items(state.pending) { order ->
                        KasirOrderCard(order, isPending = true,
                            onConfirm = { selectedOrderId = order.id; showConfirmDialog = true },
                            onCancel  = { selectedOrderId = order.id; cancelReason = ""; showCancelDialog = true }
                        )
                    }
                }
            }
            1 -> if (state.proses.isEmpty()) {
                KasirEmptyState("Tidak ada pesanan yang sedang diproses",
                    modifier = Modifier.fillMaxSize().padding(padding))
            } else {
                LazyColumn(
                    Modifier.fillMaxSize().padding(padding),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    items(state.proses) { order ->
                        KasirOrderCard(order, isPending = false, onConfirm = {}, onCancel = {})
                    }
                }
            }
        }
    }

    // Dialog Konfirmasi Bayar
    if (showConfirmDialog) {
        AlertDialog(
            onDismissRequest = { showConfirmDialog = false },
            title = { Text("Konfirmasi Pembayaran", fontWeight = FontWeight.Bold) },
            text  = { Text("Apakah pembayaran sudah diterima?\nPastikan nominal sesuai sebelum konfirmasi.") },
            confirmButton = {
                Button(onClick = { selectedOrderId?.let { viewModel.confirmPayment(it) }; showConfirmDialog = false },
                    colors = ButtonDefaults.buttonColors(containerColor = GreenOk)) {
                    Icon(Icons.Default.CheckCircle, null, Modifier.size(18.dp))
                    Spacer(Modifier.width(6.dp)); Text("Konfirmasi")
                }
            },
            dismissButton = { TextButton(onClick = { showConfirmDialog = false }) { Text("Batal") } }
        )
    }

    // Dialog Batalkan
    if (showCancelDialog) {
        AlertDialog(
            onDismissRequest = { showCancelDialog = false },
            title = { Text("Batalkan Pesanan", fontWeight = FontWeight.Bold,
                color = MaterialTheme.colorScheme.error) },
            text  = {
                Column {
                    Text("Masukkan alasan pembatalan:")
                    Spacer(Modifier.height(8.dp))
                    OutlinedTextField(cancelReason, { cancelReason = it },
                        label = { Text("Alasan pembatalan") }, maxLines = 3)
                }
            },
            confirmButton = {
                Button(
                    onClick = { if (cancelReason.length >= 3) {
                        selectedOrderId?.let { viewModel.cancelOrder(it, cancelReason) }
                        showCancelDialog = false
                    }},
                    colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.error),
                    enabled = cancelReason.length >= 3
                ) {
                    Icon(Icons.Default.Close, null, Modifier.size(18.dp))
                    Spacer(Modifier.width(6.dp)); Text("Batalkan Pesanan")
                }
            },
            dismissButton = { TextButton(onClick = { showCancelDialog = false }) { Text("Kembali") } }
        )
    }

    // Dialog Logout
    if (showLogoutDialog) {
        AlertDialog(
            onDismissRequest = { showLogoutDialog = false },
            title = { Text("Konfirmasi Keluar", fontWeight = FontWeight.Bold) },
            text  = { Text("Apakah Anda yakin ingin keluar dari aplikasi?") },
            confirmButton = {
                Button(onClick = { showLogoutDialog = false; onLogout() },
                    colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.error)) {
                    Text("Keluar")
                }
            },
            dismissButton = { TextButton(onClick = { showLogoutDialog = false }) { Text("Batal") } }
        )
    }
}

@Composable
fun KasirOrderCard(
    order: KasirOrder,
    isPending: Boolean,
    onConfirm: () -> Unit,
    onCancel: () -> Unit
) {
    val accentColor = if (isPending) BrandRed else BlueInfo
    val accentBg    = if (isPending) BrandRedBg else BlueInfoBg

    Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(3.dp)) {
        Column(Modifier.padding(16.dp)) {
            // Header
            Row(Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically) {
                Column {
                    Text("Antrian #${order.queueNumber}", fontWeight = FontWeight.ExtraBold, fontSize = 18.sp)
                    Text(order.orderNumber, style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant)
                }
                Box(Modifier.clip(RoundedCornerShape(20.dp)).background(accentBg)
                    .padding(horizontal = 12.dp, vertical = 4.dp)) {
                    Text(if (isPending) "Menunggu" else "Diproses",
                        color = accentColor, fontWeight = FontWeight.SemiBold, fontSize = 12.sp)
                }
            }

            Spacer(Modifier.height(12.dp)); HorizontalDivider(); Spacer(Modifier.height(12.dp))

            // Items
            order.items.forEach { item ->
                Row(Modifier.fillMaxWidth().padding(vertical = 2.dp),
                    verticalAlignment = Alignment.CenterVertically) {
                    Box(Modifier.size(6.dp).clip(RoundedCornerShape(50)).background(accentColor))
                    Spacer(Modifier.width(8.dp))
                    Text(item, style = MaterialTheme.typography.bodyMedium,
                        maxLines = 1, overflow = TextOverflow.Ellipsis)
                }
            }

            Spacer(Modifier.height(12.dp)); HorizontalDivider(); Spacer(Modifier.height(12.dp))

            // Total + metode bayar
            Row(Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically) {
                Column {
                    Text("Total", style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant)
                    Text(formatRupiah(order.totalAmount),
                        fontWeight = FontWeight.ExtraBold, color = GreenOk, fontSize = 18.sp)
                }
                Box(Modifier.clip(RoundedCornerShape(8.dp))
                    .background(MaterialTheme.colorScheme.surfaceVariant)
                    .padding(horizontal = 10.dp, vertical = 4.dp)) {
                    Text(order.paymentMethod.uppercase(),
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        fontWeight = FontWeight.Bold, fontSize = 12.sp)
                }
            }

            // Tombol (hanya pending)
            if (isPending) {
                Spacer(Modifier.height(16.dp))
                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(10.dp)) {
                    OutlinedButton(onCancel, Modifier.weight(1f), shape = RoundedCornerShape(10.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = MaterialTheme.colorScheme.error),
                        border = androidx.compose.foundation.BorderStroke(1.dp, MaterialTheme.colorScheme.error)) {
                        Icon(Icons.Default.Close, null, Modifier.size(16.dp))
                        Spacer(Modifier.width(4.dp)); Text("Batalkan", fontWeight = FontWeight.SemiBold)
                    }
                    Button(onConfirm, Modifier.weight(1f), shape = RoundedCornerShape(10.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = GreenOk)) {
                        Icon(Icons.Default.CheckCircle, null, Modifier.size(16.dp))
                        Spacer(Modifier.width(4.dp)); Text("Konfirmasi", fontWeight = FontWeight.SemiBold)
                    }
                }
            }
        }
    }
}

@Composable
fun KasirEmptyState(message: String, modifier: Modifier = Modifier) {
    Box(modifier, contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Icon(Icons.Default.Receipt, null, Modifier.size(64.dp),
                tint = MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.4f))
            Text(message, color = MaterialTheme.colorScheme.onSurfaceVariant,
                style = MaterialTheme.typography.bodyLarge)
        }
    }
}
