@extends('layouts.app')

@section('title', __('Edit Rekening Bank'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('payment-banks.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Rekening Bank') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('payment-banks.index') }}">{{ __('Bank Pembayaran') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Rekening Bank') }}</h2>
    <p class="section-lead">
      {{ __('Perbarui data rekening bank tujuan transfer customer.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('payment-banks.update', $paymentBank->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Informasi Rekening Bank') }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="bank_name">{{ __('Nama Bank') }} <span class="text-danger">*</span></label>
                    <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $paymentBank->bank_name) }}" required>
                    @error('bank_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bank_code">{{ __('Kode Bank') }}</label>
                    <input type="text" name="bank_code" id="bank_code" class="form-control @error('bank_code') is-invalid @enderror" value="{{ old('bank_code', $paymentBank->bank_code) }}">
                    @error('bank_code')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="account_number">{{ __('Nomor Rekening') }} <span class="text-danger">*</span></label>
                    <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number', $paymentBank->account_number) }}" required>
                    @error('account_number')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="account_name">{{ __('Atas Nama (Pemilik Rekening)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $paymentBank->account_name) }}" required>
                    @error('account_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="branch">{{ __('Kantor Cabang (Opsional)') }}</label>
                <input type="text" name="branch" id="branch" class="form-control @error('branch') is-invalid @enderror" value="{{ old('branch', $paymentBank->branch) }}">
                @error('branch')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="logo">{{ __('Ganti Logo Bank') }}</label>
                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">{{ __('Kosongkan jika tidak ingin mengubah logo.') }}</small>
                    @error('logo')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  @if ($paymentBank->logo_url)
                    <div class="mb-3">
                      <span class="d-block small text-muted mb-1">{{ __('Logo saat ini:') }}</span>
                      <img src="{{ $paymentBank->logo_url }}" alt="{{ $paymentBank->bank_name }}" style="max-height: 40px; border: 1px solid #ddd; padding: 4px; border-radius: 4px;">
                    </div>
                  @endif
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="qr_code_image">{{ __('Ganti Gambar QRIS / QR Code') }}</label>
                    <input type="file" name="qr_code_image" id="qr_code_image" class="form-control @error('qr_code_image') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">{{ __('Kosongkan jika tidak ingin mengubah QR code.') }}</small>
                    @error('qr_code_image')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  @if ($paymentBank->qr_code_url)
                    <div class="mb-3">
                      <span class="d-block small text-muted mb-1">{{ __('QR code saat ini:') }}</span>
                      <img src="{{ $paymentBank->qr_code_url }}" alt="QR Code" style="max-height: 80px; border: 1px solid #ddd; padding: 4px; border-radius: 4px;">
                    </div>
                  @endif
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="order">{{ __('Urutan Tampil') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $paymentBank->order) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                  <div class="form-group mb-0 mt-3">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ old('is_active', $paymentBank->is_active) ? 'checked' : '' }}>
                      <label class="custom-control-label" for="is_active">{{ __('Status Aktif') }}</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('payment-banks.index') }}" class="btn btn-secondary mr-2">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Perbarui') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

