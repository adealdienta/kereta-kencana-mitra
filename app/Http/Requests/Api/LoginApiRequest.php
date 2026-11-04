<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required_without_all:login,email,telepon', 'string'],
            'login' => ['sometimes', 'string'],
            'email' => ['sometimes', 'string'],
            'telepon' => ['sometimes', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required_without_all' => 'Email atau nomor WhatsApp wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi masuk gagal. Mohon periksa kembali isian Anda.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
