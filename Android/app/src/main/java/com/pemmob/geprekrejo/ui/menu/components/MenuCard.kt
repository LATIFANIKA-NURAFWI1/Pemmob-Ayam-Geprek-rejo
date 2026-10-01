package com.pemmob.geprekrejo.ui.menu.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Block
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.DeleteOutline
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.alpha
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import coil.request.ImageRequest
import com.pemmob.geprekrejo.data.model.MenuItem
import com.pemmob.geprekrejo.ui.theme.BrandRed
import com.pemmob.geprekrejo.util.CurrencyFormatter

/**
 * Kartu Menu Makanan (MenuCard)
 * Mendukung adaptasi tema gelap (Dark Mode) dan terang (Light Mode):
 * - Foto menu dengan ratio 4:3
 * - Badge ketersediaan adaptif
 * - Label kategori & nama menu
 * - Harga berformat Rupiah
 * - 3 Tombol aksi (Toggle status, Edit, Hapus)
 */
@Composable
fun MenuCard(
    item: MenuItem,
    onEditClick: (MenuItem) -> Unit,
    onDeleteClick: (MenuItem) -> Unit,
    onToggleStatusClick: (MenuItem) -> Unit,
    modifier: Modifier = Modifier
) {
    val isHabis = !item.isAvailable
    val isDark = isSystemInDarkTheme()

    Card(
        modifier = modifier
            .fillMaxWidth()
            .then(if (isHabis) Modifier.alpha(0.75f) else Modifier),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface
        ),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        border = BorderStroke(
            1.dp,
            MaterialTheme.colorScheme.outlineVariant.copy(alpha = if (isDark) 0.35f else 0.7f)
        )
    ) {
        Column(modifier = Modifier.fillMaxWidth()) {
            // ── Foto Menu + Badge Overlay ──────────────────────────────────────
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(130.dp)
                    .background(MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.45f))
            ) {
                if (!item.image.isNullOrBlank()) {
                    AsyncImage(
                        model = ImageRequest.Builder(LocalContext.current)
                            .data(item.image)
                            .crossfade(true)
                            .build(),
                        contentDescription = item.name,
                        contentScale = ContentScale.Crop,
                        modifier = Modifier
                            .fillMaxSize()
                            .clip(RoundedCornerShape(topStart = 16.dp, topEnd = 16.dp))
                    )
                } else {
                    // Fallback placeholder jika foto belum ada
                    Box(
                        modifier = Modifier.fillMaxSize(),
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Default.Restaurant,
                            contentDescription = null,
                            tint = MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.4f),
                            modifier = Modifier.size(40.dp)
                        )
                    }
                }

                // Availability Badge (Top Right)
                val badgeBg = if (isHabis) {
                    if (isDark) BrandRed.copy(alpha = 0.35f) else Color(0xFFFFEBEE)
                } else {
                    if (isDark) Color(0xFF1B5E20).copy(alpha = 0.35f) else Color.White.copy(alpha = 0.95f)
                }

                val badgeBorder = if (isHabis) {
                    BrandRed.copy(alpha = 0.5f)
                } else {
                    if (isDark) Color(0xFF4CAF50).copy(alpha = 0.5f) else Color(0xFF2E7D32).copy(alpha = 0.3f)
                }

                val badgeTextColor = if (isHabis) {
                    if (isDark) Color(0xFFFF8A80) else BrandRed
                } else {
                    if (isDark) Color(0xFF81C784) else Color(0xFF2E7D32)
                }

                Surface(
                    shape = RoundedCornerShape(50),
                    color = badgeBg,
                    border = BorderStroke(1.dp, badgeBorder),
                    modifier = Modifier
                        .align(Alignment.TopEnd)
                        .padding(8.dp)
                ) {
                    Text(
                        text = if (isHabis) "Habis" else "Tersedia",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = badgeTextColor,
                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
                    )
                }
            }

            // ── Informasi Menu (Body) ──────────────────────────────────────────
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(12.dp)
            ) {
                // Kategori
                Text(
                    text = item.category?.name ?: "Tanpa Kategori",
                    style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )

                Spacer(modifier = Modifier.height(2.dp))

                // Nama Menu
                Text(
                    text = item.name,
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.Bold,
                    color = MaterialTheme.colorScheme.onSurface,
                    maxLines = 2,
                    overflow = TextOverflow.Ellipsis,
                    modifier = Modifier.heightIn(min = 44.dp)
                )

                Spacer(modifier = Modifier.height(8.dp))

                // Harga
                Text(
                    text = CurrencyFormatter.formatRupiah(item.price),
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.ExtraBold,
                    color = if (isHabis) MaterialTheme.colorScheme.onSurfaceVariant else BrandRed
                )

                Spacer(modifier = Modifier.height(10.dp))

                HorizontalDivider(
                    color = MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.35f),
                    thickness = 1.dp
                )

                Spacer(modifier = Modifier.height(8.dp))

                // ── Tombol Aksi ────────────────────────────────────────────────
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.End,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Tombol Toggle Status (Tersedia / Habis)
                    ActionSquareButton(
                        icon = if (item.isAvailable) Icons.Default.Block else Icons.Default.CheckCircle,
                        contentDescription = if (item.isAvailable) "Tandai Habis" else "Tandai Tersedia",
                        tint = if (item.isAvailable) MaterialTheme.colorScheme.onSurfaceVariant else Color(0xFF4CAF50),
                        onClick = { onToggleStatusClick(item) }
                    )

                    Spacer(modifier = Modifier.width(6.dp))

                    // Tombol Edit
                    ActionSquareButton(
                        icon = Icons.Default.Edit,
                        contentDescription = "Edit Menu",
                        tint = if (isDark) Color(0xFF64B5F6) else Color(0xFF0D6EFD),
                        onClick = { onEditClick(item) }
                    )

                    Spacer(modifier = Modifier.width(6.dp))

                    // Tombol Hapus
                    ActionSquareButton(
                        icon = Icons.Default.DeleteOutline,
                        contentDescription = "Hapus Menu",
                        tint = if (isDark) Color(0xFFFF5252) else BrandRed,
                        onClick = { onDeleteClick(item) }
                    )
                }
            }
        }
    }
}

/**
 * Tombol kotak kecil bergaris tepi halus (Bordered Icon Button)
 */
@Composable
private fun ActionSquareButton(
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    contentDescription: String,
    tint: Color,
    onClick: () -> Unit
) {
    Box(
        modifier = Modifier
            .size(34.dp)
            .clip(RoundedCornerShape(8.dp))
            .border(
                BorderStroke(
                    1.dp,
                    MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.5f)
                ),
                RoundedCornerShape(8.dp)
            )
            .background(MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.3f))
            .clickable(onClick = onClick),
        contentAlignment = Alignment.Center
    ) {
        Icon(
            imageVector = icon,
            contentDescription = contentDescription,
            tint = tint,
            modifier = Modifier.size(18.dp)
        )
    }
}
