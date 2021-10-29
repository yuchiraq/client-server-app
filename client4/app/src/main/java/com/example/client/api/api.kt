package com.example.client.api

import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import com.example.client.model.Group
import com.example.client.model.Database
import retrofit2.Call
import retrofit2.http.GET
import retrofit2.http.Query

const val BASE_URL = "server.local"

interface BSTUApi {

    @GET("group.php")
    fun group(@Query("group") group: String): Call<List<Group>>

    @GET("index.php")
    fun database(@Query("database") database: String): Call<List<Database>>

}

val api: BSTUApi =
    Retrofit.Builder()
        .baseUrl(BASE_URL)
        .addConverterFactory(GsonConverterFactory.create())
        .build()
        .create(BSTUApi::class.java)

