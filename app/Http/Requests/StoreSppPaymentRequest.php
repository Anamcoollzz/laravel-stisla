<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSppPaymentRequest extends FormRequest
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
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')],
            'invoice_number' => ['required', 'string', 'max:50', Rule::unique('spp_payments', 'invoice_number')],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'digits:4', 'between:2000,2100'],
            'amount' => ['required', 'integer', 'min:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', Rule::in(['cash', 'transfer'])],
            'status' => ['required', 'string', Rule::in(['paid', 'unpaid'])],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'student_id' => __('Student'),
            'invoice_number' => __('Invoice Number'),
            'month' => __('Month'),
            'year' => __('Year'),
            'amount' => __('Amount'),
            'payment_date' => __('Payment Date'),
            'payment_method' => __('Payment Method'),
            'status' => __('Status'),
            'notes' => __('Notes'),
        ];
    }
}
