<?php

namespace App\Exports;

use App\Models\ProductVendor;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductVendorsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, ProductVendor>
     */
    public function collection(): Collection
    {
        return ProductVendor::query()->withCount('products')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Vendor Name',
            'Phone',
            'Email',
            'Address',
            'Description',
            'Total Products',
            'Created At',
        ];
    }

    /**
     * @param  ProductVendor  $vendor
     */
    public function map($vendor): array
    {
        return [
            $vendor->id,
            $vendor->name,
            $vendor->phone ?? '',
            $vendor->email ?? '',
            $vendor->address ?? '',
            $vendor->description ?? '',
            $vendor->products_count,
            $vendor->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
