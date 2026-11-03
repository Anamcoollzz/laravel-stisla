<?php

namespace App\Imports;

use App\Models\SppPayment;
use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SppPaymentsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?SppPayment
    {
        $studentNis = trim((string) ($row['student_nis'] ?? $row['nis'] ?? $row['nis_siswa'] ?? ''));

        if ($studentNis === '') {
            return null;
        }

        $student = Student::where('nis', $studentNis)->first();

        if (! $student) {
            return null;
        }

        $month = (int) ($row['month'] ?? $row['bulan'] ?? now()->month);
        $year = (int) ($row['year'] ?? $row['tahun'] ?? now()->year);
        $amount = (int) ($row['amount'] ?? $row['nominal'] ?? 0);
        $method = strtolower(trim((string) ($row['payment_method'] ?? $row['metode_pembayaran'] ?? 'cash')));
        $status = strtolower(trim((string) ($row['status'] ?? 'paid')));

        $paymentDate = null;
        $rawDate = $row['payment_date'] ?? $row['tanggal_bayar'] ?? null;
        if (! empty($rawDate)) {
            try {
                if (is_numeric($rawDate)) {
                    $paymentDate = Carbon::instance(Date::excelToDateTimeObject((float) $rawDate))->format('Y-m-d');
                } else {
                    $paymentDate = Carbon::parse($rawDate)->format('Y-m-d');
                }
            } catch (\Throwable) {
                $paymentDate = now()->format('Y-m-d');
            }
        } else {
            $paymentDate = now()->format('Y-m-d');
        }

        $invoice = trim((string) ($row['invoice_number'] ?? $row['no_invoice'] ?? ''));
        if ($invoice === '') {
            $invoice = 'INV-'.date('Ym').'-'.strtoupper(bin2hex(random_bytes(3)));
        }

        return SppPayment::create([
            'student_id' => $student->id,
            'invoice_number' => $invoice,
            'month' => $month,
            'year' => $year,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'payment_method' => in_array($method, ['cash', 'transfer']) ? $method : 'cash',
            'status' => in_array($status, ['paid', 'unpaid']) ? $status : 'paid',
            'notes' => trim((string) ($row['notes'] ?? $row['catatan'] ?? '')) ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.student_nis' => ['nullable'],
            '*.nis' => ['nullable'],
        ];
    }
}
