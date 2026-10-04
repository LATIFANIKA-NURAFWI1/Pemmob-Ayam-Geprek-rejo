package com.pemmob.geprekrejo.ui.stock.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Inventory2
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.pemmob.geprekrejo.ui.theme.BrandRed

/**
 * Kartu Statistik Ringkasan Stok (Summary Cards)
 * Diadaptasi dari 3 kartu pada dashboard web:
 * 1. Total Bahan (Ikon Package / Emas)
 * 2. Stok Aman (Ikon Check Circle / Hijau-Kuning)
 * 3. Stok Rendah (Ikon Warning / Merah)
 */
@Composable
fun StockSummaryCards(
    totalCount: Int,
    safeCount: Int,
    lowCount: Int,
    modifier: Modifier = Modifier
) {
    Row(
        modifier = modifier
            .fillMaxWidth()
            .horizontalScroll(rememberScrollState()),
        horizontalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        // ── 1. Total Bahan ─────────────────────────────────────────────────────
        SummaryCard(
            title = "TOTAL BAHAN",
            count = totalCount.toString(),
            icon = Icons.Default.Inventory2,
            iconTint = Color(0xFFD4842A),
            iconBg = Color(0xFFD4842A).copy(alpha = 0.15f),
            titleColor = MaterialTheme.colorScheme.onSurfaceVariant,
            borderColor = MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.4f),
            containerColor = MaterialTheme.colorScheme.surface
        )

        // ── 2. Stok Aman ───────────────────────────────────────────────────────
        SummaryCard(
            title = "STOK AMAN",
            count = safeCount.toString(),
            icon = Icons.Default.CheckCircle,
            iconTint = Color(0xFFE6A700),
            iconBg = Color(0xFFE6A700).copy(alpha = 0.15f),
            titleColor = MaterialTheme.colorScheme.onSurfaceVariant,
            borderColor = Color(0xFFE6A700).copy(alpha = 0.4f),
            containerColor = MaterialTheme.colorScheme.surface
        )

        // ── 3. Stok Rendah ────────────────────────────────────────────────────
        SummaryCard(
            title = "STOK RENDAH",
            count = lowCount.toString(),
            icon = Icons.Default.Warning,
            iconTint = BrandRed,
            iconBg = BrandRed.copy(alpha = 0.15f),
            titleColor = BrandRed,
            borderColor = if (lowCount > 0) BrandRed.copy(alpha = 0.6f) else MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.4f),
            containerColor = if (lowCount > 0) Color(0xFFFDE8E8) else MaterialTheme.colorScheme.surface
        )
    }
}

@Composable
private fun SummaryCard(
    title: String,
    count: String,
    icon: ImageVector,
    iconTint: Color,
    iconBg: Color,
    titleColor: Color,
    borderColor: Color,
    containerColor: Color
) {
    Card(
        modifier = Modifier
            .width(135.dp)
            .height(72.dp),
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = containerColor),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        border = BorderStroke(1.dp, borderColor)
    ) {
        Row(
            modifier = Modifier
                .fillMaxSize()
                .padding(10.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(verticalArrangement = Arrangement.Center) {
                Text(
                    text = title,
                    fontSize = 9.sp,
                    fontWeight = FontWeight.Bold,
                    color = titleColor,
                    letterSpacing = 0.5.sp
                )
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = count,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.ExtraBold,
                    color = if (title == "STOK RENDAH" && count != "0") BrandRed else MaterialTheme.colorScheme.onSurface
                )
            }

            Box(
                modifier = Modifier
                    .size(32.dp)
                    .clip(CircleShape)
                    .background(iconBg),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = icon,
                    contentDescription = null,
                    tint = iconTint,
                    modifier = Modifier.size(16.dp)
                )
            }
        }
    }
}
