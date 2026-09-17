package com.pemmob.geprekrejo.util

/**
 * Sealed class untuk membungkus hasil operasi network.
 * Pengganti try-catch yang tersebar di seluruh ViewModel.
 */
sealed class Result<out T> {
    data class Success<T>(val data: T) : Result<T>()
    data class Error(val message: String) : Result<Nothing>()
    data object Loading : Result<Nothing>()
}

/**
 * Wrapper untuk hasil terpaginasi dari API Laravel (LengthAwarePaginator).
 */
data class PaginatedResult<T>(
    val data: List<T>,
    val currentPage: Int,
    val lastPage: Int,
    val total: Int
) {
    val hasNextPage: Boolean get() = currentPage < lastPage
}
