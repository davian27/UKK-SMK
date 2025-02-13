<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'proofs' => 'sometimes|string',
            'user_id' => 'sometimes|exists:users,id',
            'description' => 'sometimes|string|max:255',
            'total' => 'sometimes|integer|min:1',
            'transaction_date' => 'sometimes|date',
            'status' => 'sometimes|in:pending,success,failed',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages()
    {
        return [
            'proofs.string' => 'Bukti transaksi harus berupa teks',
            'user_id.exists' => 'ID pengguna tidak ditemukan',
            'description.string' => 'Deskripsi harus berupa teks',
            'description.max' => 'Deskripsi harus kurang dari 255 karakter',
            'total.integer' => 'Total harus berupa angka',
            'total.min' => 'Total harus lebih besar atau sama dengan 0',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid',
            'status.in' => 'Status harus salah satu dari berikut: pending, success, failed',
        ];
    }
}
