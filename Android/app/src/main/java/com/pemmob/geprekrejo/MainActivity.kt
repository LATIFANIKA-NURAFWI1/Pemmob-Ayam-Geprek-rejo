package com.pemmob.geprekrejo

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.runtime.*
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.viewmodel.compose.viewModel
import com.pemmob.geprekrejo.data.repository.AuthRepository
import com.pemmob.geprekrejo.network.RetrofitClient
import com.pemmob.geprekrejo.ui.MainScreen
import com.pemmob.geprekrejo.ui.auth.LoginScreen
import com.pemmob.geprekrejo.ui.auth.LoginViewModel
import com.pemmob.geprekrejo.ui.theme.GeprekRejoTheme
import com.pemmob.geprekrejo.util.PrefsManager
import kotlinx.coroutines.launch

class MainActivity : ComponentActivity() {

    private lateinit var prefsManager: PrefsManager
    private lateinit var authRepository: AuthRepository

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        prefsManager = PrefsManager(this)
        authRepository = AuthRepository(RetrofitClient.apiService, prefsManager)
        authRepository.loadSavedToken()

        setContent {
            GeprekRejoTheme {
                var isLoggedIn by remember { mutableStateOf(authRepository.isLoggedIn) }

                if (isLoggedIn) {
                    MainScreen(
                        authRepo = authRepository,
                        onLogout = {
                            lifecycleScope.launch {
                                authRepository.logout()
                                isLoggedIn = false
                            }
                        }
                    )
                } else {
                    val loginViewModel: LoginViewModel = viewModel { LoginViewModel(authRepository) }
                    LoginScreen(
                        viewModel = loginViewModel,
                        onLoginSuccess = { isLoggedIn = true }
                    )
                }
            }
        }
    }
}
