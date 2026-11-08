@extends('layouts.pdf')

@section('title', __('Product Sizes Report'))
@section('total', $sizes->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th>{{ __('Size Name') }}</th>
        <th>{{ __('Code') }}</th>
        <th class="text-center" style="width: 100px;">{{ __('Total Products') }}</th>
        <th class="text-center" style="width: 130px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($sizes as $size)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $size->name }}</strong></td>
          <td><code>{{ $size->code ?? '-' }}</code></td>
          <td class="text-center">{{ $size->products_count }}</td>
          <td class="text-center">{{ $size->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">{{ __('No sizes found.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

