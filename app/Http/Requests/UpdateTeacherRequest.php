<?php

namespace App\Http\Requests;

use App\Models\Teacher;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
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
        /** @var Teacher|int|string|null $teacher */
        $teacher = $this->route('teacher');

        return [
            'nip' => [
                'required',
                'string',
                'max:30',
                Rule::unique('teachers', 'nip')->ignore($teacher),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('teachers', 'email')->ignore($teacher),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['required', 'string', Rule::in(['L', 'P'])],
            'address' => ['nullable', 'string', 'max:1000'],
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
            'nip' => __('NIP'),
            'name' => __('Teacher Name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'gender' => __('Gender'),
            'address' => __('Address'),
        ];
    }
}
