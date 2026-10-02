package com.pemmob.geprekrejo.ui.stock.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.DeleteOutline
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.pemmob.geprekrejo.data.model.StockItem
import com.pemmob.geprekrejo.ui.theme.BrandRed
import java.text.NumberFormat
import java.util.Locale

/**
 * Komponen Baris Item Bahan Baku (StockItemRow)
 * Diadaptasi dari tabel stok dashboard web:
 * - Ikon box / kardus
 * - Nama bahan & info batas minimum
 * - Stok saat ini + indikator garis kemajuan stok
 * - Badge status ("Aman" / "Stok Rendah")
 * - 3 Tombol aksi: "+ Restock", Edit (pensil), Hapus (tong sampah)
 */
@Composable
fun StockItemRow(
    item: StockItem,
    onRestockClick: (StockItem) -> Unit,
    onEditClick: (StockItem) -> Unit,
    onDeleteClick: (StockItem) -> Unit,
    modifier: Modifier = Modifier
) {
    val isLow = item.currentStock < item.minimumStock
    val isDark = isSystemInDarkTheme()

    // Perhitungan rasio stok terhadap 2x batas minimum untuk garis kemajuan
    val ratio = if (item.minimumStock > 0) {
        (item.currentStock / (item.minimumStock * 2.0)).toFloat().coerceIn(0.05f, 1f)
    } else 1f

    Card(
        modifier = modifier.fillMaxWidth(),
        shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(
            containerColor = if (isLow) {
                if (isDark) BrandRed.copy(alpha = 0.1f) else Color(0xFFFFF5F5)
            } else {
                MaterialTheme.colorScheme.surface
            }
        ),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        border = BorderStroke(
            1.dp,
            if (isLow) BrandRed.copy(alpha = 0.45f)
            else MaterialTheme.colorScheme.outlineVariant.copy(alpha = if (isDark) 0.35f else 0.6f)
        )
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(14.dp),
            verticalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            // ── Baris Atas: Ikon + Nama Bahan + Badge Status ───────────────────
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(10.dp),
                    modifier = Modifier.weight(1f)
                ) {
                    // Box Icon
                    Box(
                        modifier = Modifier
                            .size(38.dp)
                            .clip(RoundedCornerShape(8.dp))
                            .background(MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.5f))
                            .border(
                                BorderStroke(
                                    1.dp,
                                    MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.4f)
                                ),
                                RoundedCornerShape(8.dp)
                            ),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = if (isLow) "⚠️" else "📦",
                            fontSize = 18.sp
                        )
                    }

                    Column {
                        Text(
                            text = item.name,
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold,
                            color = MaterialTheme.colorScheme.onSurface,
                            maxLines = 1,
                            overflow = TextOverflow.Ellipsis
                        )
                        Text(
                            text = "Min: ${formatNumber(item.minimumStock)} ${item.unit}",
                            style = MaterialTheme.typography.labelSmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }

                // Status Badge ("Aman" atau "Stok Rendah")
                if (isLow) {
                    Surface(
                        shape = RoundedCornerShape(50),
                        color = if (isDark) BrandRed.copy(alpha = 0.25f) else Color(0xFFFFEBEE),
                        border = BorderStroke(1.dp, BrandRed.copy(alpha = 0.5f))
                    ) {
                        Row(
                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(4.dp)
                        ) {
                            Box(
                                modifier = Modifier
                                    .size(6.dp)
                                    .clip(CircleShape)
                                    .background(BrandRed)
                            )
                            Text(
                                text = "Stok Rendah",
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold,
                                color = if (isDark) Color(0xFFFF8A80) else BrandRed
                            )
                        }
                    }
                } else {
                    Surface(
                        shape = RoundedCornerShape(50),
                        color = if (isDark) Color(0xFFE6A700).copy(alpha = 0.15f) else Color(0xFFFFF9E6),
                        border = BorderStroke(1.dp, Color(0xFFE6A700).copy(alpha = 0.4f))
                    ) {
                        Row(
                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(4.dp)
                        ) {
                            Box(
                                modifier = Modifier
                                    .size(6.dp)
                                    .clip(CircleShape)
                                    .background(Color(0xFFE6A700))
                            )
                            Text(
                                text = "Aman",
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold,
                                color = if (isDark) Color(0xFFFFD54F) else Color(0xFFB28100)
                            )
                        }
                    }
                }
            }

            // ── Baris Tengah: Stok Saat Ini + Garis Kemajuan Kuning ────────────
            Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Stok saat ini:",
                        style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                    Text(
                        text = "${formatNumber(item.currentStock)} ${item.unit}",
                        style = MaterialTheme.typography.bodyMedium,
                        fontWeight = FontWeight.ExtraBold,
                        color = if (isLow) BrandRed else MaterialTheme.colorScheme.onSurface
                    )
                }

                // Progress Bar Stok Kuning / Merah
                LinearProgressIndicator(
                    progress = { ratio },
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(6.dp)
                        .clip(RoundedCornerShape(3.dp)),
                    color = if (isLow) BrandRed else Color(0xFFE6A700),
                    trackColor = MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.5f)
                )
            }

            HorizontalDivider(
                color = MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.35f),
                thickness = 1.dp
            )

            // ── Baris Bawah: Tombol Aksi (+ Restock, Edit, Hapus) ─────────────
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Tombol "+ Restock"
                Button(
                    onClick = { onRestockClick(item) },
                    shape = RoundedCornerShape(8.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = if (isLow) BrandRed else Color(0xFFE6A700),
                        contentColor = Color.White
                    ),
                    contentPadding = PaddingValues(horizontal = 12.dp, vertical = 6.dp),
                    modifier = Modifier.height(34.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.Add,
                        contentDescription = null,
                        modifier = Modifier.size(16.dp)
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(
                        text = "Restock",
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold
                    )
                }

                // Tombol Edit & Hapus
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    // Tombol Edit (Pensil)
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
                            .clickable { onEditClick(item) },
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Default.Edit,
                            contentDescription = "Edit Bahan",
                            tint = if (isDark) Color(0xFF64B5F6) else Color(0xFF0D6EFD),
                            modifier = Modifier.size(16.dp)
                        )
                    }

                    // Tombol Hapus (Tempat Sampah)
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
                            .clickable { onDeleteClick(item) },
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Default.DeleteOutline,
                            contentDescription = "Hapus Bahan",
                            tint = if (isDark) Color(0xFFFF5252) else BrandRed,
                            modifier = Modifier.size(16.dp)
                        )
                    }
                }
            }
        }
    }
}

private fun formatNumber(value: Double): String {
    val formatter = NumberFormat.getNumberInstance(Locale.forLanguageTag("id-ID"))
    return if (value == value.toLong().toDouble()) {
        formatter.format(value.toLong())
    } else {
        String.format(Locale.forLanguageTag("id-ID"), "%,.2f", value)
    }
}
