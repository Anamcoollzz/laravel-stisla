<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $brandId = $this->route('product_brand')?->id ?? $this->route('product_brand');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('product_brands', 'name')->ignore($brandId)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('product_brands', 'slug')->ignore($brandId)],
        ];
    }
}
