@extends('layouts.app')

@section('title', __('Detail Komentar Berita'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Berhasil')), @json(session('success')), 'success');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('news-comments.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Detail Komentar Berita') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('news-comments.index') }}">{{ __('News Comments') }}</a></div>
      <div class="breadcrumb-item">#{{ $newsComment->id }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-lg-7">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4>{{ __('Isi Komentar') }}</h4>
            @if ($newsComment->is_approved)
              <span class="badge badge-success">{{ __('Terverifikasi') }}</span>
            @else
              <span class="badge badge-warning text-dark">{{ __('Menunggu Verifikasi') }}</span>
            @endif
          </div>
          <div class="card-body">
            <div class="p-3 bg-light rounded border mb-3" style="white-space: pre-line; line-height: 1.7;">{{ $newsComment->comment }}</div>

            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">Dikirim pada {{ $newsComment->created_at?->format('d F Y, H:i') }}</small>
              @can('news-comments.edit')
                <form action="{{ route('news-comments.toggleApproval', $newsComment) }}" method="POST">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn btn-sm {{ $newsComment->is_approved ? 'btn-warning' : 'btn-success' }}">
                    <i class="fas {{ $newsComment->is_approved ? 'fa-times' : 'fa-check' }} mr-1"></i>
                    {{ $newsComment->is_approved ? __('Batalkan Verifikasi') : __('Verifikasi Komentar') }}
                  </button>
                </form>
              @endcan
            </div>
          </div>
        </div>

        <!-- Replies -->
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Balasan Komentar') }} ({{ $newsComment->replies->count() }})</h4>
          </div>
          <div class="card-body">
            @forelse ($newsComment->replies as $reply)
              <div class="border rounded p-3 mb-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <strong>{{ $reply->name }} <small class="text-muted">({{ $reply->email }})</small></strong>
                  <span class="badge {{ $reply->is_approved ? 'badge-success' : 'badge-warning text-dark' }}">
                    {{ $reply->is_approved ? __('Terverifikasi') : __('Pending') }}
                  </span>
                </div>
                <p class="mb-1">{{ $reply->comment }}</p>
                <small class="text-muted">{{ $reply->created_at?->format('d/m/Y H:i') }}</small>
              </div>
            @empty
              <p class="text-muted mb-0">{{ __('Belum ada balasan untuk komentar ini.') }}</p>
            @endforelse
          </div>
        </div>

        @can('news-comments.create')
          <div class="card">
            <form action="{{ route('news-comments.reply', $newsComment) }}" method="POST">
              @csrf
              <div class="card-header">
                <h4><i class="fas fa-reply mr-2 text-primary"></i> {{ __('Kirim Balasan Admin') }}</h4>
              </div>
              <div class="card-body">
                <div class="form-group mb-0">
                  <label for="comment">{{ __('Balasan') }} <span class="text-danger">*</span></label>
                  <textarea id="comment" name="comment" class="form-control @error('comment') is-invalid @enderror" style="height: 120px;" required placeholder="Tulis balasan admin di sini...">{{ old('comment') }}</textarea>
                  @error('comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="card-footer bg-whitesmoke text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> {{ __('Kirim Balasan') }}</button>
              </div>
            </form>
          </div>
        @endcan
      </div>

      <div class="col-12 col-lg-5">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Informasi Pengirim & Berita') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Nama Pengirim') }}</label>
              <div class="font-weight-bold">{{ $newsComment->name }}</div>
            </div>
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Email') }}</label>
              <div>{{ $newsComment->email }}</div>
            </div>
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Website') }}</label>
              <div>{{ $newsComment->website ?: '-' }}</div>
            </div>
            <div class="form-group mb-0">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Artikel Berita') }}</label>
              @if ($newsComment->news)
                <a href="{{ route('admin.news.show', $newsComment->news) }}" class="font-weight-bold">{{ $newsComment->news->title }}</a>
              @else
                <span class="text-muted">-</span>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
