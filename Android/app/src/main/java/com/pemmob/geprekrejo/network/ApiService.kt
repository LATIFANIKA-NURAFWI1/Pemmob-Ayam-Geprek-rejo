package com.pemmob.geprekrejo.network

import com.pemmob.geprekrejo.data.model.*
import retrofit2.Response
import retrofit2.http.*

/**
 * ApiService — Retrofit interface yang memetakan semua endpoint
 * Laravel REST API ke fungsi suspend Kotlin.
 */
interface ApiService {

    // ── Auth ──────────────────────────────────────────────────────────────────
    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>

    @POST("logout")
    suspend fun logout(): Response<ApiResponse<Unit>>

    // ── Dashboard ─────────────────────────────────────────────────────────────
    @GET("admin/dashboard")
    suspend fun getDashboard(): Response<ApiResponse<DashboardData>>

    @GET("admin/dashboard/stats")
    suspend fun getDashboardStats(): Response<ApiResponse<DashboardStats>>

    // ── Staff ─────────────────────────────────────────────────────────────────
    @GET("admin/staff")
    suspend fun getStaffList(
        @Query("search") search: String? = null,
        @Query("page") page: Int = 1
    ): Response<StaffListResponse>

    @GET("admin/staff/active-list")
    suspend fun getActiveStaffList(): Response<ApiResponse<List<ActiveStaffItem>>>

    @GET("admin/staff/{id}")
    suspend fun getStaff(@Path("id") id: Int): Response<StaffResponse>

    @POST("admin/staff")
    suspend fun createStaff(@Body request: StaffRequest): Response<StaffResponse>

    @PUT("admin/staff/{id}")
    suspend fun updateStaff(@Path("id") id: Int, @Body request: StaffRequest): Response<StaffResponse>

    @DELETE("admin/staff/{id}")
    suspend fun deleteStaff(@Path("id") id: Int): Response<ApiResponse<Unit>>

    @PATCH("admin/staff/{id}/toggle-active")
    suspend fun toggleStaffActive(@Path("id") id: Int): Response<ToggleActiveResponse>

    // ── Shift ─────────────────────────────────────────────────────────────────
    @GET("admin/shifts")
    suspend fun getShiftList(
        @Query("shift_date") shiftDate: String? = null,
        @Query("page") page: Int = 1
    ): Response<ShiftListResponse>

    @POST("admin/shifts")
    suspend fun createShift(@Body request: ShiftRequest): Response<ShiftResponse>

    @PUT("admin/shifts/{id}")
    suspend fun updateShift(@Path("id") id: Int, @Body request: ShiftRequest): Response<ShiftResponse>

    @DELETE("admin/shifts/{id}")
    suspend fun deleteShift(@Path("id") id: Int): Response<ApiResponse<Unit>>
}
