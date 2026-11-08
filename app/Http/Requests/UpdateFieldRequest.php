<?php

namespace App\Http\Requests;

use App\Models\Field;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFieldRequest extends FormRequest
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
        /** @var Field|int|string|null $field */
        $field = $this->route('field');

        return [
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('fields', 'code')->ignore($field),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => __('Field Code'),
            'name' => __('Field Name'),
            'description' => __('Description'),
        ];
    }
}
