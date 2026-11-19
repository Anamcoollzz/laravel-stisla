@extends('layouts.app')

@section('title', __('Detail Pesan Kontak'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Pesan Kontak') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-contact-inquiries.index') }}">{{ __('Contact Inquiries') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-envelope-open-text mr-2 text-info"></i> {{ $contactInquiry->subject }}</h4>
        <div class="card-header-action">
          <a href="{{ route('lp-contact-inquiries.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered mb-4">
          <tr>
            <th width="200">{{ __('Nama Pengirim') }}</th>
            <td><strong>{{ $contactInquiry->full_name }}</strong></td>
          </tr>
          <tr>
            <th>{{ __('Email') }}</th>
            <td><a href="mailto:{{ $contactInquiry->email }}">{{ $contactInquiry->email }}</a></td>
          </tr>
          <tr>
            <th>{{ __('Telepon') }}</th>
            <td>{{ $contactInquiry->phone }}</td>
          </tr>
          <tr>
            <th>{{ __('Subjek') }}</th>
            <td>{{ $contactInquiry->subject }}</td>
          </tr>
          <tr>
            <th>{{ __('IP Address') }}</th>
            <td><code>{{ $contactInquiry->ip_address ?: '-' }}</code></td>
          </tr>
          <tr>
            <th>{{ __('Status') }}</th>
            <td>
              @if ($contactInquiry->is_read)
                <span class="badge badge-success">{{ __('Sudah Dibaca') }}</span>
              @else
                <span class="badge badge-warning">{{ __('Belum Dibaca') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('Dikirim Pada') }}</th>
            <td>{{ $contactInquiry->created_at?->format('d M Y H:i') }}</td>
          </tr>
        </table>

        <h6>{{ __('Isi Pesan') }}</h6>
        <div class="p-3 bg-light rounded border">
          {!! nl2br(e($contactInquiry->message)) !!}
        </div>
      </div>
    </div>
  </div>
@endsection
