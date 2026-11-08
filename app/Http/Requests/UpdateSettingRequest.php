<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('settings.edit') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fields = config('settings.groups.'.$this->route('group').'.fields', []);
        $rules = [];

        foreach ($fields as $key => $field) {
            $rules[$key] = match ($field['type']) {
                'image' => ['nullable', 'image', 'max:1024'],
                'switch' => ['nullable', 'boolean'],
                default => array_merge(['nullable'], $field['rules'] ?? ['string']),
            };

            if ($field['type'] === 'image') {
                $rules['remove_'.$key] = ['nullable', 'boolean'];
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $fields = config('settings.groups.'.$this->route('group').'.fields', []);

        return collect($fields)->map(fn (array $field): string => __($field['label']))->all();
    }
}
