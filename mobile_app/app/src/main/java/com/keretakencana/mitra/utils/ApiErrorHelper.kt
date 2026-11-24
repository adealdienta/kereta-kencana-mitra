package com.keretakencana.mitra.utils

import org.json.JSONObject
import retrofit2.Response

/**
 * Helper parsing pesan error JSON standar API Laravel (message + validation errors).
 */
object ApiErrorHelper {

    fun parseMessage(response: Response<*>?, fallback: String): String {
        val raw = try {
            response?.errorBody()?.string()
        } catch (_: Exception) {
            null
        }
        return parseRawMessage(raw) ?: fallback
    }

    fun parseRawMessage(errorJson: String?): String? {
        if (errorJson.isNullOrBlank()) return null
        return try {
            val obj = JSONObject(errorJson)
            val errorsObj = obj.optJSONObject("errors")
            if (errorsObj != null) {
                val keys = errorsObj.keys()
                if (keys.hasNext()) {
                    val firstKey = keys.next()
                    val firstArr = errorsObj.optJSONArray(firstKey)
                    if (firstArr != null && firstArr.length() > 0) {
                        val firstErr = firstArr.optString(0)
                        if (firstErr.isNotBlank()) return firstErr
                    }
                }
            }
            obj.optString("message").takeIf { it.isNotBlank() }
        } catch (_: Exception) {
            null
        }
    }
}
