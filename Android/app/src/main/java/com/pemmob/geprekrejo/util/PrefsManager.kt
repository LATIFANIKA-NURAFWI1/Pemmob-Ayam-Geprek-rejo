package com.pemmob.geprekrejo.util

import android.content.Context
import android.content.SharedPreferences

/**
 * PrefsManager — wrapper SharedPreferences untuk menyimpan token Sanctum
 * dan data user yang login.
 *
 * Token disimpan di sini setelah login berhasil, dan dibaca oleh
 * RetrofitClient untuk setiap request ke API.
 */
class PrefsManager(context: Context) {

    private val prefs: SharedPreferences =
        context.getSharedPreferences("geprek_rejo_prefs", Context.MODE_PRIVATE)

    companion object {
        private const val KEY_TOKEN    = "auth_token"
        private const val KEY_USER_ID  = "user_id"
        private const val KEY_USER_NAME = "user_name"
        private const val KEY_USER_ROLE = "user_role"
        private const val KEY_USER_EMAIL = "user_email"
    }

    var authToken: String
        get() = prefs.getString(KEY_TOKEN, "") ?: ""
        set(value) = prefs.edit().putString(KEY_TOKEN, value).apply()

    var userId: Int
        get() = prefs.getInt(KEY_USER_ID, 0)
        set(value) = prefs.edit().putInt(KEY_USER_ID, value).apply()

    var userName: String
        get() = prefs.getString(KEY_USER_NAME, "") ?: ""
        set(value) = prefs.edit().putString(KEY_USER_NAME, value).apply()

    var userRole: String
        get() = prefs.getString(KEY_USER_ROLE, "") ?: ""
        set(value) = prefs.edit().putString(KEY_USER_ROLE, value).apply()

    var userEmail: String
        get() = prefs.getString(KEY_USER_EMAIL, "") ?: ""
        set(value) = prefs.edit().putString(KEY_USER_EMAIL, value).apply()

    val isLoggedIn: Boolean get() = authToken.isNotBlank()

    fun clearAll() = prefs.edit().clear().apply()
}
