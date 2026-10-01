package com.pemmob.geprekrejo.data.repository

import com.pemmob.geprekrejo.data.model.LoginRequest
import com.pemmob.geprekrejo.data.model.UserInfo
import com.pemmob.geprekrejo.network.ApiService
import com.pemmob.geprekrejo.network.RetrofitClient
import com.pemmob.geprekrejo.util.PrefsManager
import com.pemmob.geprekrejo.util.Result

class AuthRepository(
    private val apiService: ApiService,
    private val prefs: PrefsManager
) {
    suspend fun login(email: String, password: String): Result<UserInfo> {
        return try {
            val response = apiService.login(LoginRequest(email, password))
            if (response.isSuccessful) {
                val body = response.body()
                if (body?.success == true && body.data != null) {
                    // Simpan token ke SharedPreferences dan inject ke Retrofit
                    prefs.authToken = body.data.token
                    prefs.userId    = body.data.user.id
                    prefs.userName  = body.data.user.name
                    prefs.userRole  = body.data.user.role
                    prefs.userEmail = body.data.user.email
                    RetrofitClient.authToken = body.data.token
                    Result.Success(body.data.user)
                } else {
                    Result.Error(body?.message ?: "Login gagal.")
                }
            } else {
                val errorMsg = try {
                    val errString = response.errorBody()?.string()
                    if (!errString.isNullOrBlank()) {
                        val json = org.json.JSONObject(errString)
                        json.optString("message", "Email atau password salah.")
                    } else {
                        "Email atau password salah."
                    }
                } catch (e: Exception) {
                    "Email atau password salah."
                }
                Result.Error(errorMsg)
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Tidak dapat terhubung ke server.")
        }
    }

    suspend fun logout(): Result<Unit> {
        return try {
            apiService.logout()
            prefs.clearAll()
            RetrofitClient.authToken = ""
            Result.Success(Unit)
        } catch (e: Exception) {
            // Tetap logout lokal meski server error
            prefs.clearAll()
            RetrofitClient.authToken = ""
            Result.Success(Unit)
        }
    }

    fun loadSavedToken() {
        if (prefs.isLoggedIn) {
            RetrofitClient.authToken = prefs.authToken
        }
    }

    val isLoggedIn get() = prefs.isLoggedIn
    val currentUserName get() = prefs.userName
    val currentUserRole get() = prefs.userRole
}
