<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proofs' => 'required',
            'user_id' => 'required|exists:users,id',
            'transaction_date' => 'required|date',
            'status' => 'required|in:pending,success,failed',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'proofs.required' => 'Bukti transaksi diperlukan',
            'user_id.required' => 'ID pengguna diperlukan',
            'user_id.exists' => 'ID pengguna tidak ditemukan',
            'description.required' => 'Deskripsi diperlukan',
            'description.max' => 'Deskripsi harus kurang dari 255 karakter',
            'transaction_date.required' => 'Tanggal transaksi diperlukan',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid',
            'status.required' => 'Status diperlukan',
            'status.in' => 'Status harus salah satu dari berikut: pending, success, failed',
            'items.array' => 'Format items tidak valid',
            'items.min' => 'Setidaknya satu item harus dipilih',
            'items.*.item_id.required' => 'ID item diperlukan',
            'items.*.item_id.exists' => 'ID item tidak ditemukan',
            'items.*.quantity.required' => 'Jumlah item diperlukan',
            'items.*.quantity.integer' => 'Jumlah item harus berupa angka',
            'items.*.quantity.min' => 'Jumlah item tidak boleh kurang dari 1',
        ];
    }
}
