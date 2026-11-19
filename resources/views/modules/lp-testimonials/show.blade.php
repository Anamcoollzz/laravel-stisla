@extends('layouts.app')

@section('title', __('Detail Testimoni'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Testimoni') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-testimonials.index') }}">{{ __('Testimonials') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle mr-2 text-info"></i> {{ $recruitmentTestimonial->name }}</h4>
        <div class="card-header-action">
          @can('lp-testimonials.edit')
            <a href="{{ route('lp-testimonials.edit', $recruitmentTestimonial) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-testimonials.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center mb-4">
          <img src="{{ $recruitmentTestimonial->avatar_url }}" alt="{{ $recruitmentTestimonial->name }}" class="rounded-circle border mr-3" style="width: 72px; height: 72px; object-fit: cover;">
          <div>
            <h5 class="mb-1">{{ $recruitmentTestimonial->name }}</h5>
            <div class="text-muted">{{ $recruitmentTestimonial->location ?: '-' }} &bull; {{ $recruitmentTestimonial->service ?: '-' }}</div>
          </div>
        </div>

        <table class="table table-bordered mb-0">
          <tr>
            <th width="220">{{ __('Rating') }}</th>
            <td>
              <span class="text-warning">
                @for ($i = 0; $i < $recruitmentTestimonial->rating; $i++)
                  <i class="fas fa-star"></i>
                @endfor
              </span>
              ({{ $recruitmentTestimonial->rating }}/5)
            </td>
          </tr>
          <tr>
            <th>{{ __('Urutan') }}</th>
            <td>{{ $recruitmentTestimonial->order }}</td>
          </tr>
          <tr>
            <th>{{ __('Status') }}</th>
            <td>
              @if ($recruitmentTestimonial->is_active)
                <span class="badge badge-success">{{ __('Aktif') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('Isi Testimoni') }}</th>
            <td>"{{ $recruitmentTestimonial->quote }}"</td>
          </tr>
          <tr>
            <th>{{ __('Dibuat Pada') }}</th>
            <td>{{ $recruitmentTestimonial->created_at?->format('d M Y H:i') }}</td>
          </tr>
          <tr>
            <th>{{ __('Diperbarui Pada') }}</th>
            <td>{{ $recruitmentTestimonial->updated_at?->format('d M Y H:i') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
@endsection
