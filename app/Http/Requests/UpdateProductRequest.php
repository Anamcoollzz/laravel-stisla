<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'short_description' => ['nullable', 'string'],
            'description_detail' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'brand_id' => ['nullable', 'exists:product_brands,id'],
            'vendor_id' => ['nullable', 'exists:product_vendors,id'],
            'size_id' => ['nullable', 'exists:product_sizes,id'],
            'color_id' => ['nullable', 'exists:product_colors,id'],
            'spesifications' => ['nullable', 'array'],
            'tags' => ['nullable'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'product_images' => ['nullable', 'array'],
            'product_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'delete_image_ids' => ['nullable', 'array'],
            'delete_image_ids.*' => ['integer', 'exists:product_images,id'],
            'is_active' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
            'is_new' => ['nullable', 'boolean'],
            'is_sale' => ['nullable', 'boolean'],
            'is_deal' => ['nullable', 'boolean'],
        ];
    }
}
