package com.pemmob.geprekrejo.ui

import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Logout
import androidx.compose.material.icons.filled.People
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.pemmob.geprekrejo.data.repository.AuthRepository
import com.pemmob.geprekrejo.data.repository.DashboardRepository
import com.pemmob.geprekrejo.data.repository.StaffRepository
import com.pemmob.geprekrejo.network.RetrofitClient
import com.pemmob.geprekrejo.ui.dashboard.DashboardScreen
import com.pemmob.geprekrejo.ui.dashboard.DashboardViewModel
import com.pemmob.geprekrejo.ui.staff.StaffScreen
import com.pemmob.geprekrejo.ui.staff.StaffViewModel
import com.pemmob.geprekrejo.ui.stock.StockScreen
import com.pemmob.geprekrejo.ui.stock.StockViewModel
import com.pemmob.geprekrejo.ui.kasir.KasirScreen
import com.pemmob.geprekrejo.ui.kasir.KasirViewModel

@Composable
fun MainScreen(
    authRepo: AuthRepository,
    onLogout: () -> Unit
) {
    val navController = rememberNavController()
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    // ViewModel Instances
    val dashboardViewModel: DashboardViewModel = viewModel {
        DashboardViewModel(DashboardRepository(RetrofitClient.apiService))
    }
    val staffViewModel: StaffViewModel = viewModel {
        StaffViewModel(StaffRepository(RetrofitClient.apiService))
    }
    val stockViewModel: StockViewModel = viewModel {
        StockViewModel(RetrofitClient.apiService)
    }
    val kasirViewModel: KasirViewModel = viewModel()

    var showLogoutConfirm by remember { mutableStateOf(false) }
    
    val userRole = authRepo.currentUserRole

    // Memaksa reload data saat pengguna berhasil login kembali
    // agar error 401 kadaluarsa tidak ter-cache di ViewModel
    LaunchedEffect(Unit) {
        dashboardViewModel.loadDashboard()
        staffViewModel.loadStaffList()
        stockViewModel.loadStock()
    }

    // Halaman stock tidak menampilkan bottom bar
    val showBottomBar = currentRoute != "stock" && userRole == "owner"

    // Tentukan start destination berdasarkan role
    val startDest = if (userRole == "kasir") "kasir_dashboard" else "dashboard"

    Scaffold(
        bottomBar = {
            if (showBottomBar) {
                NavigationBar(
                    containerColor = MaterialTheme.colorScheme.surface,
                    contentColor = MaterialTheme.colorScheme.onSurface
                ) {
                    NavigationBarItem(
                        selected = currentRoute == "dashboard",
                        onClick = {
                            if (currentRoute != "dashboard") {
                                navController.navigate("dashboard") {
                                    popUpTo(navController.graph.startDestinationId) { saveState = true }
                                    launchSingleTop = true
                                    restoreState = true
                                }
                            }
                        },
                        icon = { Icon(Icons.Default.Dashboard, "Dashboard") },
                        label = { Text("Dashboard") },
                        colors = NavigationBarItemDefaults.colors(
                            indicatorColor = Color(0xFFBC000A).copy(alpha = 0.15f),
                            selectedIconColor = Color(0xFFBC000A),
                            selectedTextColor = Color(0xFFBC000A)
                        )
                    )

                    NavigationBarItem(
                        selected = currentRoute == "staff",
                        onClick = {
                            if (currentRoute != "staff") {
                                navController.navigate("staff") {
                                    popUpTo(navController.graph.startDestinationId) { saveState = true }
                                    launchSingleTop = true
                                    restoreState = true
                                }
                            }
                        },
                        icon = { Icon(Icons.Default.People, "Staf & Shift") },
                        label = { Text("Staf & Shift") },
                        colors = NavigationBarItemDefaults.colors(
                            indicatorColor = Color(0xFFBC000A).copy(alpha = 0.15f),
                            selectedIconColor = Color(0xFFBC000A),
                            selectedTextColor = Color(0xFFBC000A)
                        )
                    )

                    NavigationBarItem(
                        selected = false,
                        onClick = { showLogoutConfirm = true },
                        icon = { Icon(Icons.Default.Logout, "Keluar", tint = MaterialTheme.colorScheme.error) },
                        label = { Text("Keluar", color = MaterialTheme.colorScheme.error) }
                    )
                }
            }
        }
    ) { innerPadding ->
        NavHost(
            navController = navController,
            startDestination = startDest,
            modifier = Modifier.padding(innerPadding)
        ) {
            composable("kasir_dashboard") {
                KasirScreen(viewModel = kasirViewModel, onLogout = { showLogoutConfirm = true })
            }
            composable("dashboard") {
                DashboardScreen(
                    viewModel = dashboardViewModel,
                    onNavigateToStock = {
                        navController.navigate("stock") {
                            launchSingleTop = true
                        }
                    }
                )
            }
            composable("staff") {
                StaffScreen(viewModel = staffViewModel)
            }
            composable("stock") {
                StockScreen(
                    viewModel = stockViewModel,
                    onBack = { navController.popBackStack() }
                )
            }
        }
    }

    if (showLogoutConfirm) {
        AlertDialog(
            onDismissRequest = { showLogoutConfirm = false },
            title = { Text("Konfirmasi Keluar") },
            text = { Text("Apakah Anda yakin ingin keluar dari aplikasi?") },
            confirmButton = {
                Button(
                    onClick = {
                        showLogoutConfirm = false
                        onLogout()
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.error)
                ) {
                    Text("Keluar")
                }
            },
            dismissButton = {
                TextButton(onClick = { showLogoutConfirm = false }) {
                    Text("Batal")
                }
            }
        )
    }
}
