@extends('layouts.app')

@section('title', __('Tambah Metode Pengiriman'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('shipping-methods.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Tambah Metode Pengiriman') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('shipping-methods.index') }}">{{ __('Metode Pengiriman') }}</a></div>
      <div class="breadcrumb-item">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Form Tambah Metode Pengiriman') }}</h2>
    <p class="section-lead">
      {{ __('Isi data nama kurir/opsi pengiriman, tarif ongkos kirim, dan estimasi waktu pengiriman.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('shipping-methods.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
              <h4>{{ __('Informasi Pengiriman') }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="name">{{ __('Nama Metode Pengiriman') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Reguler (JNE, J&T, SiCepat)" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="code">{{ __('Kode Pengiriman') }}</label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Contoh: REG / EXP / FREE">
                    @error('code')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="cost">{{ __('Biaya / Ongkir (Rp)') }} <span class="text-danger">*</span></label>
                    <input type="number" name="cost" id="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', 0) }}" min="0" step="1000" placeholder="0 jika gratis ongkir" required>
                    <small class="form-text text-muted">{{ __('Masukkan 0 untuk opsi Gratis Ongkir.') }}</small>
                    @error('cost')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="estimated_delivery">{{ __('Estimasi Waktu Tiba') }}</label>
                    <input type="text" name="estimated_delivery" id="estimated_delivery" class="form-control @error('estimated_delivery') is-invalid @enderror" value="{{ old('estimated_delivery') }}" placeholder="Contoh: 2 - 3 hari kerja">
                    @error('estimated_delivery')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="description">{{ __('Deskripsi / Keterangan') }}</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsi kurir atau ketentuan pengiriman...">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="logo">{{ __('Logo / Ikon Kurir (Opsional)') }}</label>
                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">{{ __('Format: PNG, JPG, WEBP, SVG. Maks 2MB.') }}</small>
                    @error('logo')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="order">{{ __('Urutan Tampil') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group mb-0">
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                  <label class="custom-control-label" for="is_active">{{ __('Status Aktif') }}</label>
                </div>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('shipping-methods.index') }}" class="btn btn-secondary mr-2">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

