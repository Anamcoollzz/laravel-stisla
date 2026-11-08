@extends('layouts.pdf')

@section('title', __('Product Inventory Report'))
@section('total', $products->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 25px;">#</th>
        <th style="width: 90px;">{{ __('SKU') }}</th>
        <th>{{ __('Product Title') }}</th>
        <th>{{ __('Category') }}</th>
        <th>{{ __('Brand') }}</th>
        <th>{{ __('Vendor') }}</th>
        <th class="text-center" style="width: 70px;">{{ __('Size/Color') }}</th>
        <th class="text-right" style="width: 80px;">{{ __('Price') }}</th>
        <th class="text-center" style="width: 45px;">{{ __('Stock') }}</th>
        <th class="text-center" style="width: 60px;">{{ __('Status') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($products as $product)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><code>{{ $product->sku }}</code></td>
          <td><strong>{{ $product->title }}</strong></td>
          <td>{{ $product->category?->name ?? '-' }}</td>
          <td>{{ $product->brand?->name ?? '-' }}</td>
          <td>{{ $product->vendor?->name ?? '-' }}</td>
          <td class="text-center">
            {{ $product->size?->name ?? '-' }} / {{ $product->color?->name ?? '-' }}
          </td>
          <td class="text-right">
            @if ($product->discount_percent > 0)
              <strong>Rp {{ number_format($product->final_price, 0, ',', '.') }}</strong>
              <small style="color: #e3342f;">(-{{ (int) $product->discount_percent }}%)</small>
            @else
              Rp {{ number_format($product->price, 0, ',', '.') }}
            @endif
          </td>
          <td class="text-center">{{ $product->stock }}</td>
          <td class="text-center">
            @if ($product->is_active)
              <span class="badge badge-success">{{ __('Active') }}</span>
            @else
              <span class="badge badge-secondary">{{ __('Inactive') }}</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center">{{ __('No products found.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

