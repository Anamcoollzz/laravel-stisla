@extends('layouts.app')

@section('title', __('Detail Galeri'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Galeri') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-galleries.index') }}">{{ __('Galleries') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle mr-2 text-info"></i> {{ $lpGallery->title }}</h4>
        <div class="card-header-action">
          @can('lp-galleries.edit')
            <a href="{{ route('lp-galleries.edit', $lpGallery) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-galleries.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <img src="{{ $lpGallery->image_url }}" alt="{{ $lpGallery->title }}" class="img-fluid rounded border w-100" style="max-height: 260px; object-fit: cover;">
          </div>
          <div class="col-md-8">
            <table class="table table-bordered mb-0">
              <tr>
                <th width="180">{{ __('Judul') }}</th>
                <td><strong>{{ $lpGallery->title }}</strong></td>
              </tr>
              <tr>
                <th>{{ __('Kategori') }}</th>
                <td><span class="badge badge-info">{{ $lpGallery->category }}</span></td>
              </tr>
              <tr>
                <th>{{ __('Tanggal / Periode') }}</th>
                <td>{{ $lpGallery->date ?: '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Urutan') }}</th>
                <td>{{ $lpGallery->order }}</td>
              </tr>
              <tr>
                <th>{{ __('Status') }}</th>
                <td>
                  @if ($lpGallery->is_active)
                    <span class="badge badge-success">{{ __('Aktif') }}</span>
                  @else
                    <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('Deskripsi') }}</th>
                <td>{{ $lpGallery->description ?: '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Dibuat Pada') }}</th>
                <td>{{ $lpGallery->created_at?->format('d M Y H:i') }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
