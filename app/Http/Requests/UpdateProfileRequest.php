<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()?->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Log upload errors if any.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $file = $this->file('avatar');

            if ($file && ! $file->isValid()) {
                $code = $file->getError();
                $errorMsg = $file->getErrorMessage();

                Log::warning("Avatar upload failed [code {$code}]: {$errorMsg}", [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }
        });
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $file = $this->file('avatar');
        $detail = null;

        if ($file && ! $file->isValid()) {
            $detail = match ($file->getError()) {
                \UPLOAD_ERR_INI_SIZE => __('Ukuran file melebihi batas upload server (upload_max_filesize).'),
                \UPLOAD_ERR_FORM_SIZE => __('Ukuran file melebihi batas maksimal formulir.'),
                \UPLOAD_ERR_PARTIAL => __('File hanya terunggah sebagian. Silakan coba lagi.'),
                \UPLOAD_ERR_NO_TMP_DIR => __('Folder temporary server tidak tersedia.'),
                \UPLOAD_ERR_CANT_WRITE => __('Server gagal menulis file ke disk temporary.'),
                \UPLOAD_ERR_EXTENSION => __('Upload file dihentikan oleh ekstensi PHP.'),
                default => $file->getErrorMessage() ?: __('File tidak valid.'),
            };
        }

        return [
            'avatar.uploaded' => $detail
                ? __('Avatar gagal diunggah: :detail', ['detail' => $detail])
                : __('Avatar gagal diunggah. Pastikan ukuran file maksimal 10MB dan format JPG/PNG/WEBP/GIF.'),
            'avatar.max' => __('Ukuran avatar tidak boleh lebih dari :max kilobita (10MB).'),
            'avatar.image' => __('Berkas avatar harus berupa gambar (JPG, PNG, WEBP, GIF).'),
            'avatar.mimes' => __('Format avatar yang diperbolehkan hanya JPG, JPEG, PNG, WEBP, atau GIF.'),
        ];
    }
}
