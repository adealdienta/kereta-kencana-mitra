<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Format respons sukses standar
     */
    protected function successResponse($data = null, string $message = 'Operasi berhasil', int $code = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'status' => 'success',
            'message' => $message,
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $code);
    }

    /**
     * Format respons error standar
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'status' => 'error',
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }

    /**
     * Normalisasi nomor telepon ke format standar (+628xxxx)
     */
    public static function normalizePhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);

        if ($clean === '') {
            return '';
        }

        if (str_starts_with($clean, '62')) {
            return '+' . $clean;
        }

        if (str_starts_with($clean, '0')) {
            return '+62' . substr($clean, 1);
        }

        return '+62' . $clean;
    }
}
