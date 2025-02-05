<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $this->route('supplier'),
            'phone' => 'required|string|max:15',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Nama supplier wajib diisi.',
            'name.string' => 'Nama supplier harus berupa string.',
            'name.max' => 'Nama supplier tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email supplier wajib diisi.',
            'email.email' => 'Email supplier tidak valid.',
            'email.unique' => 'Email supplier sudah terdaftar.',
            'phone.required' => 'Nomor telepon supplier wajib diisi.',
            'phone.string' => 'Nomor telepon harus berupa string.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 15 karakter.',
        ];
    }
}
