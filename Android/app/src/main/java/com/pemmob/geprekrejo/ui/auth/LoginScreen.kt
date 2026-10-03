package com.pemmob.geprekrejo.ui.auth

import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.slideInVertically
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Email
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.compose.collectAsStateWithLifecycle

@Composable
fun LoginScreen(
    viewModel: LoginViewModel,
    onLoginSuccess: () -> Unit
) {
    val state by viewModel.uiState.collectAsStateWithLifecycle()
    var passwordVisible by remember { mutableStateOf(false) }
    var visible by remember { mutableStateOf(false) }

    LaunchedEffect(Unit) { visible = true }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(
                    listOf(Color(0xFF1A0A0B), Color(0xFF2D1215), Color(0xFF1A0A0B))
                )
            )
    ) {
        // Dekorasi bulatan merah besar di belakang
        Box(
            modifier = Modifier
                .size(350.dp)
                .offset(x = 100.dp, y = (-80).dp)
                .clip(CircleShape)
                .background(Color(0xFFBC000A).copy(alpha = 0.15f))
                .align(Alignment.TopEnd)
        )

        AnimatedVisibility(
            visible = visible,
            enter = fadeIn(tween(600)) + slideInVertically(tween(600)) { it / 3 }
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .systemBarsPadding()
                    .imePadding()
                    .padding(horizontal = 28.dp)
                    .verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.Center,
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Logo / Icon
                Box(
                    modifier = Modifier
                        .size(80.dp)
                        .clip(RoundedCornerShape(20.dp))
                        .background(Color(0xFFBC000A)),
                    contentAlignment = Alignment.Center
                ) {
                    Text("🍗", fontSize = 40.sp)
                }

                Spacer(Modifier.height(24.dp))

                Text(
                    text = "Ayam Geprek Rejo",
                    style = MaterialTheme.typography.headlineMedium,
                    fontWeight = FontWeight.Bold,
                    color = Color.White
                )
                Text(
                    text = "Panel Administrasi",
                    style = MaterialTheme.typography.bodyMedium,
                    color = Color.White.copy(alpha = 0.6f)
                )

                Spacer(Modifier.height(48.dp))

                // Email field
                OutlinedTextField(
                    value         = state.email,
                    onValueChange = viewModel::onEmailChanged,
                    modifier      = Modifier.fillMaxWidth(),
                    label         = { Text("Email") },
                    leadingIcon   = { Icon(Icons.Default.Email, null) },
                    singleLine    = true,
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                    shape         = RoundedCornerShape(14.dp),
                    colors        = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor   = Color(0xFFBC000A),
                        focusedLabelColor    = Color(0xFFBC000A),
                        focusedLeadingIconColor = Color(0xFFBC000A),
                        cursorColor          = Color(0xFFBC000A),
                        unfocusedBorderColor = Color.White.copy(alpha = 0.3f),
                        unfocusedLabelColor  = Color.White.copy(alpha = 0.6f),
                        unfocusedLeadingIconColor = Color.White.copy(alpha = 0.4f),
                        focusedTextColor     = Color.White,
                        unfocusedTextColor   = Color.White
                    )
                )

                Spacer(Modifier.height(16.dp))

                // Password field
                OutlinedTextField(
                    value         = state.password,
                    onValueChange = viewModel::onPasswordChanged,
                    modifier      = Modifier.fillMaxWidth(),
                    label         = { Text("Password") },
                    leadingIcon   = { Icon(Icons.Default.Lock, null) },
                    trailingIcon  = {
                        IconButton(onClick = { passwordVisible = !passwordVisible }) {
                            Icon(
                                if (passwordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                contentDescription = null,
                                tint = Color.White.copy(alpha = 0.5f)
                            )
                        }
                    },
                    visualTransformation = if (passwordVisible) VisualTransformation.None
                                           else PasswordVisualTransformation(),
                    singleLine    = true,
                    shape         = RoundedCornerShape(14.dp),
                    colors        = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor   = Color(0xFFBC000A),
                        focusedLabelColor    = Color(0xFFBC000A),
                        focusedLeadingIconColor = Color(0xFFBC000A),
                        cursorColor          = Color(0xFFBC000A),
                        unfocusedBorderColor = Color.White.copy(alpha = 0.3f),
                        unfocusedLabelColor  = Color.White.copy(alpha = 0.6f),
                        unfocusedLeadingIconColor = Color.White.copy(alpha = 0.4f),
                        focusedTextColor     = Color.White,
                        unfocusedTextColor   = Color.White
                    )
                )

                // Error message
                AnimatedVisibility(visible = state.error != null) {
                    Text(
                        text      = state.error ?: "",
                        color     = Color(0xFFFF6B6B),
                        style     = MaterialTheme.typography.bodySmall,
                        textAlign = TextAlign.Center,
                        modifier  = Modifier.padding(top = 8.dp)
                    )
                }

                Spacer(Modifier.height(32.dp))

                // Tombol Login
                Button(
                    onClick  = { viewModel.login(onLoginSuccess) },
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp),
                    enabled  = !state.isLoading,
                    shape    = RoundedCornerShape(14.dp),
                    colors   = ButtonDefaults.buttonColors(
                        containerColor = Color(0xFFBC000A),
                        contentColor   = Color.White
                    )
                ) {
                    if (state.isLoading) {
                        CircularProgressIndicator(
                            modifier = Modifier.size(22.dp),
                            color    = Color.White,
                            strokeWidth = 2.dp
                        )
                    } else {
                        Text("Masuk", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                    }
                }
            }
        }
    }
}
