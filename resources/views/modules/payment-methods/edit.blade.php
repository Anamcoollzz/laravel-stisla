@extends('layouts.app')

@section('title', __('Edit Metode Pembayaran'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('payment-methods.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Metode Pembayaran') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('payment-methods.index') }}">{{ __('Metode Pembayaran') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Metode Pembayaran') }}</h2>
    <p class="section-lead">
      {{ __('Perbarui data metode pembayaran dan biaya layanannya.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('payment-methods.update', $paymentMethod->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Informasi Pembayaran') }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="name">{{ __('Nama Metode Pembayaran') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $paymentMethod->name) }}" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="code">{{ __('Kode Metode') }}</label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $paymentMethod->code) }}">
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
                      <option value="manual" {{ old('type', $paymentMethod->type) == 'manual' ? 'selected' : '' }}>{{ __('Manual (Transfer Bank / Konfirmasi)') }}</option>
                      <option value="cod" {{ old('type', $paymentMethod->type) == 'cod' ? 'selected' : '' }}>{{ __('Cash On Delivery (COD)') }}</option>
                      <option value="bank_transfer" {{ old('type', $paymentMethod->type) == 'bank_transfer' ? 'selected' : '' }}>{{ __('Virtual Account (Otomatis)') }}</option>
                      <option value="ewallet" {{ old('type', $paymentMethod->type) == 'ewallet' ? 'selected' : '' }}>{{ __('E-Wallet / QRIS') }}</option>
                      <option value="credit_card" {{ old('type', $paymentMethod->type) == 'credit_card' ? 'selected' : '' }}>{{ __('Kartu Kredit / Debit') }}</option>
                    </select>
                    @error('type')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="fee">{{ __('Biaya Layanan / Admin (Rp)') }} <span class="text-danger">*</span></label>
                    <input type="number" name="fee" id="fee" class="form-control @error('fee') is-invalid @enderror" value="{{ old('fee', $paymentMethod->fee) }}" min="0" step="500" required>
                    @error('fee')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="description">{{ __('Deskripsi Singkat') }}</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $paymentMethod->description) }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="instructions">{{ __('Instruksi / Panduan Pembayaran') }}</label>
                <textarea name="instructions" id="instructions" class="form-control @error('instructions') is-invalid @enderror" rows="3">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                @error('instructions')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="logo">{{ __('Ganti Logo / Ikon') }}</label>
                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">{{ __('Kosongkan jika tidak ingin mengubah logo.') }}</small>
                    @error('logo')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  @if ($paymentMethod->logo_url)
                    <div class="mb-3">
                      <span class="d-block small text-muted mb-1">{{ __('Logo saat ini:') }}</span>
                      <img src="{{ $paymentMethod->logo_url }}" alt="{{ $paymentMethod->name }}" style="max-height: 48px; border: 1px solid #ddd; padding: 4px; border-radius: 6px;">
                    </div>
                  @endif
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="order">{{ __('Urutan Tampil') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $paymentMethod->order) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group mb-0">
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                  <label class="custom-control-label" for="is_active">{{ __('Status Aktif') }}</label>
                </div>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('payment-methods.index') }}" class="btn btn-secondary mr-2">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Perbarui') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

