package com.keretakencana.mitra.utils

import android.content.Context
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.graphics.Matrix
import android.media.ExifInterface
import android.net.Uri
import androidx.core.content.FileProvider
import java.io.File
import java.io.FileOutputStream

/**
 * Helper terpusat untuk pembuatan URI kamera via FileProvider
 * serta perbaikan rotasi EXIF & kompresi JPEG agar lolos batas ukuran upload server.
 */
object ImageUploadHelper {

    fun cleanupTempFiles(context: Context, maxAgeMs: Long = 3600_000L) {
        try {
            val now = System.currentTimeMillis()
            context.cacheDir.listFiles()?.forEach { file ->
                val name = file.name
                if (file.isFile &&
                    name.endsWith(".jpg", ignoreCase = true) &&
                    (now - file.lastModified()) > maxAgeMs &&
                    (name.startsWith("bukti_") || name.startsWith("avatar_") || name.startsWith("upload_"))
                ) {
                    file.delete()
                }
            }
        } catch (_: Exception) {
        }
    }

    fun createCameraImageUri(context: Context, prefix: String): Uri {
        cleanupTempFiles(context)
        val imageFile = File.createTempFile(prefix, ".jpg", context.cacheDir)
        return FileProvider.getUriForFile(context, "${context.packageName}.fileprovider", imageFile)
    }

    private fun readExifOrientation(context: Context, uri: Uri): Int {
        return try {
            context.contentResolver.openInputStream(uri)?.use { stream ->
                ExifInterface(stream).getAttributeInt(
                    ExifInterface.TAG_ORIENTATION,
                    ExifInterface.ORIENTATION_NORMAL
                )
            } ?: ExifInterface.ORIENTATION_NORMAL
        } catch (_: Exception) {
            ExifInterface.ORIENTATION_NORMAL
        }
    }

    private fun applyExifRotation(bitmap: Bitmap, orientation: Int): Bitmap {
        val matrix = Matrix()
        when (orientation) {
            ExifInterface.ORIENTATION_ROTATE_90 -> matrix.postRotate(90f)
            ExifInterface.ORIENTATION_ROTATE_180 -> matrix.postRotate(180f)
            ExifInterface.ORIENTATION_ROTATE_270 -> matrix.postRotate(270f)
            ExifInterface.ORIENTATION_FLIP_HORIZONTAL -> matrix.postScale(-1f, 1f)
            ExifInterface.ORIENTATION_FLIP_VERTICAL -> matrix.postScale(1f, -1f)
            else -> return bitmap
        }

        return try {
            val rotated = Bitmap.createBitmap(bitmap, 0, 0, bitmap.width, bitmap.height, matrix, true)
            if (rotated != bitmap) {
                bitmap.recycle()
            }
            rotated
        } catch (_: Exception) {
            bitmap
        }
    }

    fun compressUriToJpegFile(
        context: Context,
        uri: Uri,
        prefix: String,
        maxDimension: Int = 1280,
        quality: Int = 85
    ): File? {
        return try {
            cleanupTempFiles(context)
            val orientation = readExifOrientation(context, uri)

            val boundsOpts = BitmapFactory.Options().apply { inJustDecodeBounds = true }
            context.contentResolver.openInputStream(uri)?.use { stream ->
                BitmapFactory.decodeStream(stream, null, boundsOpts)
            }

            var inSampleSize = 1
            val rawW = boundsOpts.outWidth
            val rawH = boundsOpts.outHeight
            if (rawW > maxDimension || rawH > maxDimension) {
                val halfW = rawW / 2
                val halfH = rawH / 2
                while ((halfW / inSampleSize) >= maxDimension && (halfH / inSampleSize) >= maxDimension) {
                    inSampleSize *= 2
                }
            }

            val decodeOpts = BitmapFactory.Options().apply { this.inSampleSize = inSampleSize }
            val decoded = context.contentResolver.openInputStream(uri)?.use { stream ->
                BitmapFactory.decodeStream(stream, null, decodeOpts)
            }

            val tempFile = File.createTempFile(prefix, ".jpg", context.cacheDir)
            if (decoded != null) {
                val oriented = applyExifRotation(decoded, orientation)
                val width = oriented.width
                val height = oriented.height
                val (targetW, targetH) = if (width > maxDimension || height > maxDimension) {
                    val ratio = width.toFloat() / height.toFloat()
                    if (ratio > 1f) {
                        maxDimension to (maxDimension / ratio).toInt().coerceAtLeast(1)
                    } else {
                        (maxDimension * ratio).toInt().coerceAtLeast(1) to maxDimension
                    }
                } else {
                    width to height
                }

                val scaled = if (targetW != width || targetH != height) {
                    Bitmap.createScaledBitmap(oriented, targetW, targetH, true).also { oriented.recycle() }
                } else {
                    oriented
                }

                FileOutputStream(tempFile).use { fos ->
                    scaled.compress(Bitmap.CompressFormat.JPEG, quality, fos)
                    fos.flush()
                }
                scaled.recycle()
                tempFile
            } else {
                val input = context.contentResolver.openInputStream(uri) ?: return null
                input.use { src ->
                    FileOutputStream(tempFile).use { dst ->
                        src.copyTo(dst)
                    }
                }
                tempFile
            }
        } catch (_: Exception) {
            null
        }
    }
}
