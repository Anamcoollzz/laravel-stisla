@extends('layouts.pdf')

@section('title', __('Daftar Tag Berita'))
@section('total', $tags->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th>{{ __('Nama Tag') }}</th>
        <th>{{ __('Slug') }}</th>
        <th class="text-center" style="width: 100px;">{{ __('Jumlah Berita') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($tags as $tag)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $tag->name }}</strong></td>
          <td>{{ $tag->slug }}</td>
          <td class="text-center">{{ $tag->news_count ?? 0 }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
