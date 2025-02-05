<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Maks 2MB, foto boleh kosong
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
        ];
    }
    public function messages(): array
    {
        return [
            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus berupa jpg, jpeg, atau png.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
            'name.required' => 'Nama item wajib diisi.',
            'name.max' => 'Nama item tidak boleh lebih dari 255 karakter.',
            'description.required' => 'Deskripsi item wajib diisi.',
            'description.max' => 'Deskripsi item tidak boleh lebih dari 1000 karakter.',
            'price.required' => 'Harga item wajib diisi.',
            'price.integer' => 'Harga item harus berupa angka.',
            'price.min' => 'Harga item tidak boleh kurang dari 0.',
            'stock.required' => 'Stok item wajib diisi.',
            'stock.integer' => 'Stok item harus berupa angka.',
            'stock.min' => 'Stok item tidak boleh kurang dari 0.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak valid.',
        ];
    }
}
