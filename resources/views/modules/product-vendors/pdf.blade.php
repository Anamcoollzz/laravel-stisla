@extends('layouts.pdf')

@section('title', __('Product Vendors Report'))
@section('total', $vendors->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 30px;">#</th>
        <th>{{ __('Vendor Name') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Address') }}</th>
        <th class="text-center" style="width: 80px;">{{ __('Products') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($vendors as $vendor)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $vendor->name }}</strong></td>
          <td>{{ $vendor->phone ?? '-' }}</td>
          <td>{{ $vendor->email ?? '-' }}</td>
          <td>{{ $vendor->address ?? '-' }}</td>
          <td class="text-center">{{ $vendor->products_count }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">{{ __('No vendors found.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

