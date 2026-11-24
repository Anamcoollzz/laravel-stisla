@extends('layouts.auth')

@section('title', $file->title ?: $file->name)

@section('content')
  <section class="section">
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-7">
          <div class="card card-primary shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4 class="mb-0">{{ $file->title ?: $file->name }}</h4>
              <span class="badge badge-info">v{{ $file->current_version }}</span>
            </div>
            <div class="card-body text-center">
              @if ($file->isImage())
                <img src="{{ $file->url() }}" alt="{{ $file->alt_text ?: $file->name }}" class="img-fluid rounded mb-3" style="max-height: 60vh;">
              @else
                <div class="py-4 text-muted">
                  <i class="fas fa-file fa-4x mb-2 text-primary"></i>
                  <div class="font-weight-bold">{{ $file->name }}</div>
                </div>
              @endif

              @if ($file->description)
                <p class="text-muted mb-3">{{ $file->description }}</p>
              @endif

              <div class="small text-muted mb-4">
                {{ $file->formattedSize() }}
                @if ($file->width && $file->height)
                  &bull; {{ $file->width }}x{{ $file->height }}px
                @endif
                @if ($file->share_expires_at)
                  &bull; {{ __('Expires') }}: {{ $file->share_expires_at->format('d M Y H:i') }}
                @endif
              </div>

              <a href="{{ route('media.shared', ['token' => $file->share_token, 'download' => 1]) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-download mr-1"></i> {{ __('Download File') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
