@extends('layouts.app')

@section('title', __('Detail Kandidat Pekerja'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail Kandidat Pekerja') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-workers.index') }}">{{ __('Workers') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-user mr-2 text-info"></i> {{ $worker->name }} ({{ $worker->code }})</h4>
        <div class="card-header-action">
          @can('lp-workers.edit')
            <a href="{{ route('lp-workers.edit', $worker) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-workers.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-3">
            <img src="{{ $worker->photo_url }}" alt="{{ $worker->name }}" class="img-fluid rounded border w-100" style="max-height: 280px; object-fit: cover;">
          </div>
          <div class="col-md-9">
            <table class="table table-bordered mb-0">
              <tr>
                <th width="180">{{ __('Kode Kandidat') }}</th>
                <td><code>{{ $worker->code }}</code></td>
              </tr>
              <tr>
                <th>{{ __('Nama Lengkap') }}</th>
                <td><strong>{{ $worker->name }}</strong></td>
              </tr>
              <tr>
                <th>{{ __('Kategori') }}</th>
                <td><span class="badge badge-info">{{ $worker->category_label }}</span></td>
              </tr>
              <tr>
                <th>{{ __('Usia / Pengalaman') }}</th>
                <td>{{ $worker->age }} {{ __('thn') }} &bull; {{ $worker->experience_label }}</td>
              </tr>
              <tr>
                <th>{{ __('Asal Daerah') }}</th>
                <td>{{ $worker->origin ?: '-' }} ({{ $worker->origin_region ?: '-' }})</td>
              </tr>
              <tr>
                <th>{{ __('Pendidikan') }}</th>
                <td>{{ $worker->education ?: '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Status') }}</th>
                <td><span class="badge badge-primary">{{ $worker->status }}</span></td>
              </tr>
              <tr>
                <th>{{ __('Featured') }}</th>
                <td>{{ $worker->is_featured ? __('Ya') : __('Tidak') }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="mb-4">
          <h6>{{ __('Bio / Ringkasan Profil') }}</h6>
          <p class="text-muted mb-0">{{ $worker->bio ?: '-' }}</p>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <h6>{{ __('Keahlian') }}</h6>
            <ul class="pl-3 mb-0">
              @forelse ($worker->skills ?? [] as $item)
                <li>{{ $item }}</li>
              @empty
                <li class="text-muted">-</li>
              @endforelse
            </ul>
          </div>
          <div class="col-md-4 mb-3">
            <h6>{{ __('Bahasa') }}</h6>
            <ul class="pl-3 mb-0">
              @forelse ($worker->languages ?? [] as $item)
                <li>{{ $item }}</li>
              @empty
                <li class="text-muted">-</li>
              @endforelse
            </ul>
          </div>
          <div class="col-md-4 mb-3">
            <h6>{{ __('Sertifikasi Pelatihan') }}</h6>
            <ul class="pl-3 mb-0">
              @forelse ($worker->training_certifications ?? [] as $item)
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
