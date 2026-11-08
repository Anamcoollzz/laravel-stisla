@extends('layouts.pdf')

@section('title', __('Product Brands Report'))
@section('total', $brands->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th>{{ __('Brand Name') }}</th>
        <th>{{ __('Slug') }}</th>
        <th class="text-center" style="width: 100px;">{{ __('Total Products') }}</th>
        <th class="text-center" style="width: 130px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($brands as $brand)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $brand->name }}</strong></td>
          <td><code>{{ $brand->slug }}</code></td>
          <td class="text-center">{{ $brand->products_count }}</td>
          <td class="text-center">{{ $brand->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">{{ __('No brands found.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

