@extends('layouts.pdf')

@section('title', __('Daftar Komentar Berita'))
@section('total', $comments->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th style="width: 130px;">{{ __('Pengirim') }}</th>
        <th style="width: 150px;">{{ __('Berita') }}</th>
        <th>{{ __('Komentar') }}</th>
        <th style="width: 85px;">{{ __('Status') }}</th>
        <th style="width: 110px;">{{ __('Waktu') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($comments as $comment)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td>
            <strong>{{ $comment->name }}</strong>
            <div style="font-size: 10px; color: #666;">{{ $comment->email }}</div>
          </td>
          <td>{{ $comment->news?->title ?? '-' }}</td>
          <td>{{ $comment->comment }}</td>
          <td>{{ $comment->is_approved ? __('Terverifikasi') : __('Pending') }}</td>
          <td>{{ $comment->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
