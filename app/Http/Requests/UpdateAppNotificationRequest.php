<?php

namespace App\Http\Requests;

use App\Models\AppNotification;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('notifications.edit') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(array_keys(AppNotification::types()))],
            'icon' => ['nullable', 'string', 'max:100'],
            'action_url' => ['nullable', 'string', 'max:255'],
            'target_type' => ['required', 'string', Rule::in(['all', 'user'])],
            'user_id' => ['nullable', 'required_if:target_type,user', 'exists:users,id'],
        ];
    }
}
