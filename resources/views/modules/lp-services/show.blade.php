@extends('layouts.app')

@section('title', __('Detail Layanan Rekrutmen'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Layanan Rekrutmen') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-services.index') }}">{{ __('Services') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle mr-2 text-info"></i> {{ $recruitmentService->title }}</h4>
        <div class="card-header-action">
          @can('lp-services.edit')
            <a href="{{ route('lp-services.edit', $recruitmentService) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-services.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-4">
            <img src="{{ $recruitmentService->image_url }}" alt="{{ $recruitmentService->title }}" class="img-fluid rounded border w-100" style="max-height: 240px; object-fit: cover;">
          </div>
          <div class="col-md-8">
            <table class="table table-bordered mb-0">
              <tr>
                <th width="180">{{ __('Judul Layanan') }}</th>
                <td><strong>{{ $recruitmentService->title }}</strong></td>
              </tr>
              <tr>
                <th>{{ __('Subjudul') }}</th>
                <td>{{ $recruitmentService->subtitle ?: '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Slug') }}</th>
                <td><code>{{ $recruitmentService->slug }}</code></td>
              </tr>
              <tr>
                <th>{{ __('Category Key') }}</th>
                <td><span class="badge badge-info">{{ $recruitmentService->category_key }}</span></td>
              </tr>
              <tr>
                <th>{{ __('Ikon (Lucide)') }}</th>
                <td><code>{{ $recruitmentService->icon }}</code></td>
              </tr>
              <tr>
                <th>{{ __('Urutan') }}</th>
                <td>{{ $recruitmentService->order }}</td>
              </tr>
              <tr>
                <th>{{ __('Status') }}</th>
                <td>
                  @if ($recruitmentService->is_active)
                    <span class="badge badge-success">{{ __('Aktif') }}</span>
                  @else
                    <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
                  @endif
                </td>
              </tr>
            </table>
          </div>
        </div>

        <div class="mb-3">
          <h6>{{ __('Deskripsi Singkat') }}</h6>
          <p class="text-muted">{{ $recruitmentService->short_description ?: '-' }}</p>
        </div>

        <div class="mb-4">
          <h6>{{ __('Deskripsi Lengkap') }}</h6>
          <p class="text-muted mb-0">{{ $recruitmentService->description ?: '-' }}</p>
        </div>

        <div class="row">
          <div class="col-md-4">
            <h6>{{ __('Tanggung Jawab Utama') }}</h6>
            <ul class="pl-3 mb-0">
              @forelse ($recruitmentService->responsibilities ?? [] as $item)
                <li>{{ $item }}</li>
              @empty
                <li class="text-muted">-</li>
              @endforelse
            </ul>
          </div>
          <div class="col-md-4">
            <h6>{{ __('Keahlian') }}</h6>
            <ul class="pl-3 mb-0">
              @forelse ($recruitmentService->skills ?? [] as $item)
                <li>{{ $item }}</li>
              @empty
                <li class="text-muted">-</li>
              @endforelse
            </ul>
          </div>
          <div class="col-md-4">
            <h6>{{ __('Keunggulan') }}</h6>
            <ul class="pl-3 mb-0">
              @forelse ($recruitmentService->benefits ?? [] as $item)
                <li>{{ $item }}</li>
              @empty
                <li class="text-muted">-</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
