package com.pemmob.geprekrejo.ui.kasir

import androidx.lifecycle.ViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update

data class KasirOrder(
    val id: Int,
    val queueNumber: Int,
    val orderNumber: String,
    val totalAmount: Double,
    val paymentMethod: String,
    val status: String,
    val items: List<String>
)

data class KasirState(
    val isLoading: Boolean = false,
    val pending: List<KasirOrder> = emptyList(),
    val proses: List<KasirOrder> = emptyList(),
    val error: String? = null
)

class KasirViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(KasirState())
    val uiState: StateFlow<KasirState> = _uiState.asStateFlow()

    init {
        // Mock data
        _uiState.update {
            it.copy(
                pending = listOf(
                    KasirOrder(1, 101, "ORD-001", 35000.0, "qris", "pending", listOf("Ayam Geprek x2", "Es Teh x2")),
                    KasirOrder(2, 102, "ORD-002", 15000.0, "cash", "pending", listOf("Ayam Bakar x1"))
                ),
                proses = listOf(
                    KasirOrder(3, 100, "ORD-000", 20000.0, "qris", "confirmed", listOf("Mie Geprek x1"))
                )
            )
        }
    }

    fun confirmPayment(orderId: Int) {
        val pendingList = _uiState.value.pending.toMutableList()
        val prosesList = _uiState.value.proses.toMutableList()

        val order = pendingList.find { it.id == orderId }
        if (order != null) {
            pendingList.remove(order)
            prosesList.add(order.copy(status = "confirmed"))
        }

        _uiState.update { it.copy(pending = pendingList, proses = prosesList) }
    }

    fun cancelOrder(orderId: Int, reason: String) {
        val pendingList = _uiState.value.pending.filter { it.id != orderId }
        _uiState.update { it.copy(pending = pendingList) }
    }

    fun refresh() {
        // TODO: ganti dengan panggilan API saat backend sudah siap
        // Untuk sekarang reload mock data
        _uiState.update {
            it.copy(
                pending = listOf(
                    KasirOrder(1, 101, "ORD-001", 35000.0, "qris", "pending", listOf("Ayam Geprek x2", "Es Teh x2")),
                    KasirOrder(2, 102, "ORD-002", 15000.0, "cash", "pending", listOf("Ayam Bakar x1"))
                ),
                proses = listOf(
                    KasirOrder(3, 100, "ORD-000", 20000.0, "qris", "confirmed", listOf("Mie Geprek x1"))
                )
            )
        }
    }
}
