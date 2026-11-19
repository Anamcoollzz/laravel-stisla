<?php

namespace App\Imports;

use App\Models\LP\ContactInquiry;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ContactInquiriesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?ContactInquiry
    {
        $fullName = trim((string) ($row['full_name'] ?? $row['nama'] ?? ''));
        $email = trim((string) ($row['email'] ?? ''));
        $message = trim((string) ($row['message'] ?? $row['pesan'] ?? ''));

        if ($fullName === '' || $email === '' || $message === '') {
            return null;
        }

        $rawRead = $row['is_read'] ?? $row['status'] ?? 0;
        $isRead = is_string($rawRead)
            ? in_array(strtolower(trim($rawRead)), ['1', 'true', 'yes', 'read', 'dibaca'], true)
            : (bool) $rawRead;

        return new ContactInquiry([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => trim((string) ($row['phone'] ?? $row['telepon'] ?? '-')),
            'subject' => trim((string) ($row['subject'] ?? $row['subjek'] ?? 'General Inquiry')),
            'message' => $message,
            'is_read' => $isRead,
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            '*.full_name' => ['nullable', 'string', 'max:150'],
            '*.email' => ['nullable', 'email', 'max:150'],
        ];
    }
}
