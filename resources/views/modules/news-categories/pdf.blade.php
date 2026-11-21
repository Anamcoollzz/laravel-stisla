@extends('layouts.pdf')

@section('title', __('Daftar Kategori Berita'))
@section('total', $categories->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th style="width: 180px;">{{ __('Nama Kategori') }}</th>
        <th>{{ __('Deskripsi') }}</th>
        <th class="text-center" style="width: 90px;">{{ __('Jumlah Berita') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($categories as $category)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $category->name }}</strong></td>
          <td>{{ $category->description ?: '-' }}</td>
          <td class="text-center">{{ $category->news_count ?? 0 }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
