<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', Rule::exists('categories', 'id')->where('tenant_id', $this->user()->tenant_id)],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($this->route('product'))],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->where('tenant_id', $this->user()->tenant_id)->ignore($this->route('product'))],
            'product_name' => 'required|string|max:255',
            'type' => 'required|in:product,service',
            'sell_price' => 'required|numeric|min:0|max:100000000',
            'cost_price' => 'nullable|numeric|min:0|max:100000000',
            'stock' => 'nullable|integer|min:0|max:1000000',
            'min_stock' => 'nullable|integer|min:0|max:1000000',
            'manage_stock' => 'sometimes|boolean',
            'requires_preparation' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function catalogData(): array
    {
        $data = $this->safe()->except('image');
        $tracked = $data['type'] === 'product' && $this->boolean('manage_stock');

        return array_merge($data, [
            'requires_preparation' => $data['type'] === 'product' && $this->boolean('requires_preparation'),
            'manage_stock' => $tracked,
            'stock' => $tracked ? ($data['stock'] ?? 0) : 0,
            'min_stock' => $tracked ? ($data['min_stock'] ?? 0) : 0,
            'is_active' => $this->boolean('is_active'),
            'cost_price' => $data['cost_price'] ?? 0,
        ]);
    }
}
