package com.keretakencana.mitra.data.model

import com.google.gson.annotations.SerializedName

data class PesananListResponse(
    @SerializedName("items")
    val items: List<Transaksi> = emptyList(),

    @SerializedName("pagination")
    val pagination: PaginationData? = null
)

data class PaginationData(
    @SerializedName("current_page")
    val currentPage: Int = 1,

    @SerializedName("last_page")
    val lastPage: Int = 1,

    @SerializedName("per_page")
    val perPage: Int = 10,

    @SerializedName("total")
    val total: Int = 0
)
