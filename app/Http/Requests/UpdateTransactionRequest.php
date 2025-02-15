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
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*' => 'exists:items,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'nullable|date',
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
            'proof.image' => 'Bukti harus berupa gambar.',
            'proof.mimes' => 'Format gambar yang diperbolehkan: jpg, jpeg, png.',
            'proof.max' => 'Ukuran gambar maksimal 2MB.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
            'transaction_date.date' => 'Tanggal transaksi harus berupa tanggal yang valid.',
            'status.required' => 'Status transaksi harus diisi.',
            'status.in' => 'Status transaksi tidak valid.',
        ];
    }
}
