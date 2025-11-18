<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'store_id' => 'nullable|string|exists:stores,id',
            'category_id' => 'nullable|string|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'gender' => 'nullable|in:male,female,unisex',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string',
            
            // Скидка
            'discount_type' => 'nullable|in:none,percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            
            // Характеристики
            'attributes' => 'nullable|array',
            'attributes.*.key' => 'required|string',
            'attributes.*.value' => 'required|string',
            
            // Варианты (размеры) - ОБЯЗАТЕЛЬНО
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.quantity' => 'required|integer|min:0',
        ];
    }
}
