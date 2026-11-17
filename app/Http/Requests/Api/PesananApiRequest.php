<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PesananApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catatan' => ['nullable', 'string', 'max:500'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'metode_pembayaran' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'integer', 'exists:barangs,id'],
            'items.*.satuan' => ['required', 'string', 'in:Slop'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Keranjang pesanan tidak boleh kosong.',
            'items.min' => 'Pilih minimal 1 item produk untuk membuat pesanan.',
            'items.*.barang_id.required' => 'ID produk wajib disertakan.',
            'items.*.barang_id.exists' => 'Varian produk yang dipilih tidak ditemukan di sistem.',
            'items.*.satuan.required' => 'Satuan produk wajib ditentukan (Slop).',
            'items.*.satuan.in' => 'Pemesanan online khusus satuan Slop (maksimal 19 Slop). Untuk pembelian Bal, hubungi Admin via WhatsApp.',
            'items.*.jumlah.required' => 'Jumlah pesanan wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah pesanan minimal 1 Slop.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi pesanan gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
