<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Models\Role;

class UsersImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?User
    {
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));
        $email = trim((string) ($row['email'] ?? ''));

        if ($name === '' || $email === '') {
            return null;
        }

        $password = trim((string) ($row['password'] ?? 'password123'));
        $roleName = trim((string) ($row['role'] ?? 'user')) ?: 'user';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => $roleName,
            ]
        );

        if (Role::where('name', $roleName)->exists()) {
            $user->syncRoles([$roleName]);
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            '*.email' => ['nullable'],
            '*.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
