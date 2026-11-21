<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
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
        return [
            'app' => ['required', 'array', 'min:1'],
            'app.*' => ['required', 'string', 'max:50'],
            'parent_id' => ['nullable', 'integer', Rule::exists('menus', 'id')],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', Rule::in(['header', 'item', 'dropdown'])],
            'icon' => ['nullable', 'string', 'max:100'],
            'route' => ['nullable', 'string', 'max:150'],
            'url' => ['nullable', 'string', 'max:255'],
            'permission' => ['nullable', 'string', 'max:150'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $rawApp = $this->input('app');
        $apps = is_array($rawApp)
            ? $rawApp
            : (filled($rawApp) ? explode(',', (string) $rawApp) : [config('stisla.menu_app', 'online-shop')]);

        $normalizedApps = array_values(array_unique(array_filter(array_map(
            fn (mixed $item): string => trim((string) $item),
            $apps
        ))));

        $this->merge([
            'app' => $normalizedApps !== [] ? $normalizedApps : [config('stisla.menu_app', 'online-shop')],
            'is_active' => $this->boolean('is_active'),
            'order' => $this->filled('order') ? (int) $this->order : 0,
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'app' => __('App'),
            'parent_id' => __('Parent Menu'),
            'name' => __('Menu Name'),
            'type' => __('Menu Type'),
            'icon' => __('Icon'),
            'route' => __('Route'),
            'url' => __('URL'),
            'permission' => __('Permission'),
            'order' => __('Order'),
            'is_active' => __('Status'),
        ];
    }
}
