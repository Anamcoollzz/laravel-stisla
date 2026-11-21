@extends('layouts.app')

@section('title', __('Detail Statistik Perusahaan'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Statistik Perusahaan') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-company-stats.index') }}">{{ __('Company Stats') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle mr-2 text-info"></i> {{ $companyStat->label }}</h4>
        <div class="card-header-action">
          @can('lp-company-stats.edit')
            <a href="{{ route('lp-company-stats.edit', $companyStat) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-company-stats.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered mb-0">
          <tr>
            <th width="220">{{ __('Nilai') }}</th>
            <td><strong class="text-primary">{{ $companyStat->value }}</strong></td>
          </tr>
          <tr>
            <th>{{ __('Label') }}</th>
            <td>{{ $companyStat->label }}</td>
          </tr>
          <tr>
            <th>{{ __('Ikon (Lucide)') }}</th>
            <td><code>{{ $companyStat->icon }}</code></td>
          </tr>
          <tr>
            <th>{{ __('Urutan') }}</th>
            <td>{{ $companyStat->order }}</td>
          </tr>
          <tr>
            <th>{{ __('Status') }}</th>
            <td>
              @if ($companyStat->is_active)
                <span class="badge badge-success">{{ __('Aktif') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('Deskripsi') }}</th>
            <td>{{ $companyStat->description ?: '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('Dibuat Pada') }}</th>
            <td>{{ $companyStat->created_at?->format('d M Y H:i') }}</td>
          </tr>
          <tr>
            <th>{{ __('Diperbarui Pada') }}</th>
            <td>{{ $companyStat->updated_at?->format('d M Y H:i') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
@endsection
