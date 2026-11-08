@extends('layouts.pdf')

@section('title', __('Product Banners Report'))
@section('total', $banners->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Subtitle') }}</th>
        <th>{{ __('Badge') }}</th>
        <th>{{ __('Button') }}</th>
        <th class="text-center" style="width: 60px;">{{ __('Order') }}</th>
        <th class="text-center" style="width: 70px;">{{ __('Status') }}</th>
        <th class="text-center" style="width: 120px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($banners as $banner)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{!! strip_tags($banner->title) !!}</strong></td>
          <td><small>{{ $banner->subtitle ?: '—' }}</small></td>
          <td>{{ $banner->badge_label ?: '—' }}</td>
          <td>{{ $banner->button_text ? $banner->button_text . ' (' . ($banner->button_url ?: '#') . ')' : '—' }}</td>
          <td class="text-center">{{ $banner->order }}</td>
          <td class="text-center">{{ $banner->is_active ? __('Active') : __('Inactive') }}</td>
          <td class="text-center">{{ $banner->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">{{ __('No banners found.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

