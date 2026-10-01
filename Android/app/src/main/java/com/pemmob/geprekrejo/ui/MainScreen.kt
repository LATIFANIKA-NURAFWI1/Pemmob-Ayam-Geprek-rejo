package com.pemmob.geprekrejo.ui

import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AccountBalanceWallet
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Inventory2
import androidx.compose.material.icons.filled.Logout
import androidx.compose.material.icons.filled.People
import androidx.compose.material.icons.filled.RestaurantMenu
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
import com.pemmob.geprekrejo.data.repository.FinanceRepository
import com.pemmob.geprekrejo.data.repository.StaffRepository
import com.pemmob.geprekrejo.network.RetrofitClient
import com.pemmob.geprekrejo.ui.dashboard.DashboardScreen
import com.pemmob.geprekrejo.ui.dashboard.DashboardViewModel
import com.pemmob.geprekrejo.ui.finance.FinanceScreen
import com.pemmob.geprekrejo.ui.finance.FinanceViewModel
import com.pemmob.geprekrejo.ui.menu.MenuFormScreen
import com.pemmob.geprekrejo.ui.menu.MenuFormViewModel
import com.pemmob.geprekrejo.ui.menu.MenuListScreen
import com.pemmob.geprekrejo.ui.menu.MenuViewModel
import com.pemmob.geprekrejo.ui.staff.StaffScreen
import com.pemmob.geprekrejo.ui.staff.StaffViewModel
import com.pemmob.geprekrejo.ui.stock.StockScreen
import com.pemmob.geprekrejo.ui.stock.StockViewModel

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
    val menuViewModel: MenuViewModel = viewModel()
    val menuFormViewModel: MenuFormViewModel = viewModel()
    val financeViewModel: FinanceViewModel = viewModel {
        FinanceViewModel(FinanceRepository(RetrofitClient.apiService))
    }
    val staffViewModel: StaffViewModel = viewModel {
        StaffViewModel(StaffRepository(RetrofitClient.apiService))
    }
    val stockViewModel: StockViewModel = viewModel {
        StockViewModel(RetrofitClient.apiService)
    }

    var showLogoutConfirm by remember { mutableStateOf(false) }
    
    val userRole = authRepo.currentUserRole

    // Memaksa reload data saat pengguna berhasil login kembali
    LaunchedEffect(Unit) {
        dashboardViewModel.loadDashboard()
        menuViewModel.loadData()
        financeViewModel.loadReport()
        financeViewModel.loadExpenses()
        staffViewModel.loadStaffList()
        stockViewModel.loadStock()
    }

    // Form menu tidak menampilkan bottom bar
    val showBottomBar = currentRoute != "menu_form" && userRole == "owner"

    // Tentukan start destination
    val startDest = "dashboard"

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
                        selected = currentRoute == "menu",
                        onClick = {
                            if (currentRoute != "menu") {
                                navController.navigate("menu") {
                                    popUpTo(navController.graph.startDestinationId) { saveState = true }
                                    launchSingleTop = true
                                    restoreState = true
                                }
                            }
                        },
                        icon = { Icon(Icons.Default.RestaurantMenu, "Menu") },
                        label = { Text("Menu") },
                        colors = NavigationBarItemDefaults.colors(
                            indicatorColor = Color(0xFFBC000A).copy(alpha = 0.15f),
                            selectedIconColor = Color(0xFFBC000A),
                            selectedTextColor = Color(0xFFBC000A)
                        )
                    )

                    NavigationBarItem(
                        selected = currentRoute == "stock",
                        onClick = {
                            if (currentRoute != "stock") {
                                navController.navigate("stock") {
                                    popUpTo(navController.graph.startDestinationId) { saveState = true }
                                    launchSingleTop = true
                                    restoreState = true
                                }
                            }
                        },
                        icon = { Icon(Icons.Default.Inventory2, "Stok") },
                        label = { Text("Stok") },
                        colors = NavigationBarItemDefaults.colors(
                            indicatorColor = Color(0xFFBC000A).copy(alpha = 0.15f),
                            selectedIconColor = Color(0xFFBC000A),
                            selectedTextColor = Color(0xFFBC000A)
                        )
                    )

                    NavigationBarItem(
                        selected = currentRoute == "finance",
                        onClick = {
                            if (currentRoute != "finance") {
                                navController.navigate("finance") {
                                    popUpTo(navController.graph.startDestinationId) { saveState = true }
                                    launchSingleTop = true
                                    restoreState = true
                                }
                            }
                        },
                        icon = { Icon(Icons.Default.AccountBalanceWallet, "Keuangan") },
                        label = { Text("Keuangan") },
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
                        icon = { Icon(Icons.Default.People, "Staf") },
                        label = { Text("Staf") },
                        colors = NavigationBarItemDefaults.colors(
                            indicatorColor = Color(0xFFBC000A).copy(alpha = 0.15f),
                            selectedIconColor = Color(0xFFBC000A),
                            selectedTextColor = Color(0xFFBC000A)
                        )
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
            composable("dashboard") {
                DashboardScreen(
                    viewModel = dashboardViewModel,
                    onNavigateToStock = {
                        navController.navigate("stock") {
                            launchSingleTop = true
                        }
                    },
                    onLogoutClick = { showLogoutConfirm = true }
                )
            }
            composable("menu") {
                MenuListScreen(
                    viewModel = menuViewModel,
                    onNavigateToAddMenu = {
                        menuFormViewModel.resetForCreate()
                        navController.navigate("menu_form")
                    },
                    onNavigateToEditMenu = { item ->
                        menuFormViewModel.initForEdit(item)
                        navController.navigate("menu_form")
                    }
                )
            }
            composable("menu_form") {
                MenuFormScreen(
                    viewModel = menuFormViewModel,
                    onNavigateBack = { navController.popBackStack() },
                    onSaveComplete = { savedItem ->
                        menuViewModel.saveMenu(savedItem)
                        navController.popBackStack()
                    }
                )
            }
            composable("finance") {
                FinanceScreen(viewModel = financeViewModel)
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
