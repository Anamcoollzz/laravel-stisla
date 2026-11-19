@extends('layouts.pdf')

@section('title', __('Daftar Berita'))
@section('total', $newsList->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th>{{ __('Judul') }}</th>
        <th style="width: 110px;">{{ __('Kategori') }}</th>
        <th style="width: 100px;">{{ __('Publisher') }}</th>
        <th style="width: 80px;">{{ __('Status') }}</th>
        <th style="width: 120px;">{{ __('Published At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($newsList as $item)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td>
            <strong>{{ $item->title }}</strong>
            <div style="font-size: 10px; color: #666;">{{ $item->slug }}</div>
          </td>
          <td>{{ $item->category?->name ?? '-' }}</td>
          <td>{{ $item->publisher?->name ?? '-' }}</td>
          <td>{{ ucfirst($item->status) }}</td>
          <td>{{ $item->published_at?->format('Y-m-d H:i') ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
