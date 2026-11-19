@extends('layouts.app')

@section('title', __('Detail Tahapan Rekrutmen'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Tahapan Rekrutmen') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-recruitment-steps.index') }}">{{ __('Recruitment Steps') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle mr-2 text-info"></i> {{ $recruitmentStep->step }} - {{ $recruitmentStep->title }}</h4>
        <div class="card-header-action">
          @can('lp-recruitment-steps.edit')
            <a href="{{ route('lp-recruitment-steps.edit', $recruitmentStep) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-recruitment-steps.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered mb-0">
          <tr>
            <th width="220">{{ __('Nomor Step') }}</th>
            <td><span class="badge badge-primary">{{ $recruitmentStep->step }}</span></td>
          </tr>
          <tr>
            <th>{{ __('Judul') }}</th>
            <td><strong>{{ $recruitmentStep->title }}</strong></td>
          </tr>
          <tr>
            <th>{{ __('Ikon (Lucide)') }}</th>
            <td><code>{{ $recruitmentStep->icon }}</code></td>
          </tr>
          <tr>
            <th>{{ __('Urutan') }}</th>
            <td>{{ $recruitmentStep->order }}</td>
          </tr>
          <tr>
            <th>{{ __('Status') }}</th>
            <td>
              @if ($recruitmentStep->is_active)
                <span class="badge badge-success">{{ __('Aktif') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('Deskripsi') }}</th>
            <td>{{ $recruitmentStep->description ?: '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('Dibuat Pada') }}</th>
            <td>{{ $recruitmentStep->created_at?->format('d M Y H:i') }}</td>
          </tr>
          <tr>
            <th>{{ __('Diperbarui Pada') }}</th>
            <td>{{ $recruitmentStep->updated_at?->format('d M Y H:i') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
@endsection
