@extends('layouts.app')

@section('title', __('Tambah Metode Pembayaran'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('payment-methods.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Tambah Metode Pembayaran') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('payment-methods.index') }}">{{ __('Metode Pembayaran') }}</a></div>
      <div class="breadcrumb-item">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Form Tambah Metode Pembayaran') }}</h2>
    <p class="section-lead">
      {{ __('Isi informasi nama metode pembayaran, tipe, biaya layanan, instruksi panduan bagi pembeli.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('payment-methods.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
              <h4>{{ __('Informasi Pembayaran') }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="name">{{ __('Nama Metode Pembayaran') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Transfer Bank / Virtual Account" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="code">{{ __('Kode Metode') }}</label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Contoh: COD / BANK / VA / QRIS">
                    @error('code')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="type">{{ __('Tipe Pembayaran') }} <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                      <option value="manual" {{ old('type') == 'manual' ? 'selected' : '' }}>{{ __('Manual (Transfer Bank / Konfirmasi)') }}</option>
                      <option value="cod" {{ old('type') == 'cod' ? 'selected' : '' }}>{{ __('Cash On Delivery (COD)') }}</option>
                      <option value="bank_transfer" {{ old('type') == 'bank_transfer' ? 'selected' : '' }}>{{ __('Virtual Account (Otomatis)') }}</option>
                      <option value="ewallet" {{ old('type') == 'ewallet' ? 'selected' : '' }}>{{ __('E-Wallet / QRIS') }}</option>
                      <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>{{ __('Kartu Kredit / Debit') }}</option>
                    </select>
                    @error('type')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="fee">{{ __('Biaya Layanan / Admin (Rp)') }} <span class="text-danger">*</span></label>
                    <input type="number" name="fee" id="fee" class="form-control @error('fee') is-invalid @enderror" value="{{ old('fee', 0) }}" min="0" step="500" placeholder="0 jika bebas biaya" required>
                    @error('fee')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="description">{{ __('Deskripsi Singkat') }}</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="2" placeholder="Deskripsi ringkas yang tampil di opsi checkout...">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="instructions">{{ __('Instruksi / Panduan Pembayaran') }}</label>
                <textarea name="instructions" id="instructions" class="form-control @error('instructions') is-invalid @enderror" rows="3" placeholder="Langkah-langkah pembayaran untuk customer setelah checkout...">{{ old('instructions') }}</textarea>
                @error('instructions')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="logo">{{ __('Logo / Ikon Pembayaran (Opsional)') }}</label>
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
              <a href="{{ route('payment-methods.index') }}" class="btn btn-secondary mr-2">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

