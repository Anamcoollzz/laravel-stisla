<?php

namespace App\Exports;

use App\Models\SppPayment;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SppPaymentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, SppPayment>
     */
    public function collection(): Collection
    {
        return SppPayment::query()->with('student.schoolClass')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'NIS Siswa',
            'Nama Siswa',
            'Kelas',
            'Bulan',
            'Tahun',
            'Nominal',
            'Tanggal Bayar',
            'Metode Pembayaran',
            'Status',
            'Catatan',
            'Dibuat Pada',
        ];
    }

    /**
     * @param  SppPayment  $payment
     */
    public function map($payment): array
    {
        return [
            $payment->invoice_number,
            $payment->student?->nis ?? '-',
            $payment->student?->name ?? '-',
            $payment->student?->schoolClass?->class_name ?? '-',
            $payment->monthName(),
            $payment->year,
            $payment->amount,
            $payment->payment_date?->format('Y-m-d') ?? '-',
            $payment->payment_method === 'cash' ? 'Tunai' : 'Transfer Bank',
            $payment->status === 'paid' ? 'Lunas' : 'Belum Lunas',
            $payment->notes ?? '-',
            $payment->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
