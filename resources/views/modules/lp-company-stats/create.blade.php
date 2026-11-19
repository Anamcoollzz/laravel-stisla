@extends('layouts.app')

@section('title', __('Tambah Statistik Perusahaan'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Tambah Statistik Perusahaan') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-company-stats.index') }}">{{ __('Company Stats') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-plus-circle mr-2 text-primary"></i> {{ __('Form Statistik Perusahaan') }}</h4>
      </div>
      <form action="{{ route('lp-company-stats.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="value">{{ __('Nilai') }} <span class="text-danger">*</span></label>
                <input type="text" name="value" id="value" value="{{ old('value') }}" class="form-control @error('value') is-invalid @enderror" placeholder="{{ __('Contoh: 10+, 850+, 2,100+') }}" required>
                @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label for="label">{{ __('Label') }} <span class="text-danger">*</span></label>
                <input type="text" name="label" id="label" value="{{ old('label') }}" class="form-control @error('label') is-invalid @enderror" placeholder="{{ __('Contoh: Years of Experience') }}" required>
                @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="icon">{{ __('Ikon (Lucide)') }} <span class="text-danger">*</span></label>
                <input type="text" name="icon" id="icon" value="{{ old('icon', 'award') }}" class="form-control @error('icon') is-invalid @enderror" placeholder="award, users, check-circle-2, heart-handshake" required>
                <small class="form-text text-muted">{{ __('Nama ikon Lucide yang digunakan pada tampilan landing page.') }}</small>
                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="order">{{ __('Urutan') }}</label>
                <input type="number" name="order" id="order" min="0" value="{{ old('order', 0) }}" class="form-control @error('order') is-invalid @enderror">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="d-block">{{ __('Status') }}</label>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', true))>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Aktif') }}</span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-group mb-0">
            <label for="description">{{ __('Deskripsi') }}</label>
            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('Deskripsi singkat statistik perusahaan...') }}">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <a href="{{ route('lp-company-stats.index') }}" class="btn btn-secondary mr-1">{{ __('Kembali') }}</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
