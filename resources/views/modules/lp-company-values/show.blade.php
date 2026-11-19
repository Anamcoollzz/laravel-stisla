@extends('layouts.app')

@section('title', __('Detail Nilai Perusahaan'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Nilai Perusahaan') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-company-values.index') }}">{{ __('Company Values') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle mr-2 text-info"></i> {{ $companyValue->title }}</h4>
        <div class="card-header-action">
          @can('lp-company-values.edit')
            <a href="{{ route('lp-company-values.edit', $companyValue) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-company-values.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered mb-0">
          <tr>
            <th width="220">{{ __('Judul') }}</th>
            <td><strong>{{ $companyValue->title }}</strong></td>
          </tr>
          <tr>
            <th>{{ __('Ikon (Lucide)') }}</th>
            <td><code>{{ $companyValue->icon }}</code></td>
          </tr>
          <tr>
            <th>{{ __('Urutan') }}</th>
            <td>{{ $companyValue->order }}</td>
          </tr>
          <tr>
            <th>{{ __('Status') }}</th>
            <td>
              @if ($companyValue->is_active)
                <span class="badge badge-success">{{ __('Aktif') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('Deskripsi') }}</th>
            <td>{{ $companyValue->description ?: '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('Dibuat Pada') }}</th>
            <td>{{ $companyValue->created_at?->format('d M Y H:i') }}</td>
          </tr>
          <tr>
            <th>{{ __('Diperbarui Pada') }}</th>
            <td>{{ $companyValue->updated_at?->format('d M Y H:i') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
@endsection
