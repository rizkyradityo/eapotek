<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $medicineId = $this->route('medicine');

        return [
            'code' => ['required', 'string', 'max:50', 'unique:medicines,code,' . $medicineId],
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'generic_name' => ['nullable', 'string', 'max:200'],
            'manufacturer' => ['nullable', 'string', 'max:200'],
            'price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'description' => ['nullable', 'string'],
            'composition' => ['nullable', 'string'],
            'side_effects' => ['nullable', 'string'],
            'usage_instruction' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode obat wajib diisi',
            'code.unique' => 'Kode obat sudah digunakan',
            'name.required' => 'Nama obat wajib diisi',
            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'unit_id.required' => 'Satuan wajib dipilih',
            'unit_id.exists' => 'Satuan tidak valid',
            'price.required' => 'Harga wajib diisi',
            'price.min' => 'Harga tidak boleh negatif',
            'min_stock.min' => 'Stok minimum tidak boleh negatif',
        ];
    }
}
