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
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*' => 'exists:items,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'proofs' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date|after_or_equal:today',
            'status' => 'required|in:pending,completed,canceled',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Pengguna wajib diisi.',
            'user_id.exists' => 'Pengguna tidak ditemukan.',
            'items.required' => 'Minimal satu item harus dipilih.',
            'items.*.exists' => 'Item yang dipilih tidak valid.',
            'quantities.required' => 'Jumlah item harus diisi.',
            'quantities.*.integer' => 'Jumlah item harus berupa angka.',
            'quantities.*.min' => 'Jumlah item minimal 1.',
            'proofs.image' => 'Bukti harus berupa gambar.',
            'proofs.required' => 'Bukti Transaksi Wajib Di Isi',
            'proofs.mimes' => 'Format gambar yang diperbolehkan: jpg, jpeg, png.',
            'proofs.max' => 'Ukuran gambar maksimal 2MB.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
            'transaction_date.required' => 'Tanggal transaksi harus diisi.',
            'transaction_date.date' => 'Tanggal transaksi harus berupa tanggal yang valid.',
            'transaction_date.after_or_equal' => 'Tanggal transaksi tidak boleh kurang dari hari ini.',
            'status.required' => 'Status transaksi harus diisi.',
            'status.in' => 'Status transaksi tidak valid.',
        ];
    }
}
