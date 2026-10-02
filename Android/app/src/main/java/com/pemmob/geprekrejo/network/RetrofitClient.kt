package com.pemmob.geprekrejo.network

import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

/**
 * RetrofitClient — singleton yang menyediakan instance ApiService.
 *
 * BASE_URL diatur ke IP Address Wi-Fi laptop (192.168.100.10) agar
 * bisa diakses dari HP fisik yang tersambung ke Wi-Fi yang sama.
 */
object RetrofitClient {

    const val BASE_URL = "http://10.120.17.194:8000/api/v1/"



    var authToken: String = ""

    private val loggingInterceptor = HttpLoggingInterceptor().apply {
        level = HttpLoggingInterceptor.Level.BODY
    }

    private val okHttpClient: OkHttpClient by lazy {
        OkHttpClient.Builder()
            .connectTimeout(15, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .writeTimeout(30, TimeUnit.SECONDS)
            .addInterceptor(loggingInterceptor)
            .addInterceptor { chain ->
                val builder = chain.request().newBuilder()
                    .header("Accept", "application/json")
                if (authToken.isNotBlank()) {
                    builder.header("Authorization", "Bearer $authToken")
                }
                chain.proceed(builder.build())
            }
            .build()
    }

    val apiService: ApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
}
