@extends('layouts.pdf')

@section('title', __('Product Colors Report'))
@section('total', $colors->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th>{{ __('Color Name') }}</th>
        <th>{{ __('Hex Code') }}</th>
        <th class="text-center" style="width: 100px;">{{ __('Total Products') }}</th>
        <th class="text-center" style="width: 130px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($colors as $color)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $color->name }}</strong></td>
          <td><code>{{ $color->color_code ?? '-' }}</code></td>
          <td class="text-center">{{ $color->products_count }}</td>
          <td class="text-center">{{ $color->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">{{ __('No colors found.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

