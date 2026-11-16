@extends('layouts.app')

@section('title', __('Tambah Rekening Bank'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('payment-banks.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Tambah Rekening Bank') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('payment-banks.index') }}">{{ __('Bank Pembayaran') }}</a></div>
      <div class="breadcrumb-item">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Form Tambah Rekening Bank') }}</h2>
    <p class="section-lead">
      {{ __('Isi data nama bank, nomor rekening, atas nama pemilik rekening, dan upload logo bank.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('payment-banks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
              <h4>{{ __('Informasi Rekening Bank') }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="bank_name">{{ __('Nama Bank') }} <span class="text-danger">*</span></label>
                    <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}" placeholder="Contoh: BCA / Mandiri / BNI / BRI / BSI" required>
                    @error('bank_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bank_code">{{ __('Kode Bank') }}</label>
                    <input type="text" name="bank_code" id="bank_code" class="form-control @error('bank_code') is-invalid @enderror" value="{{ old('bank_code') }}" placeholder="Contoh: 014 / 008">
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
                    <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}" placeholder="Contoh: 1234567890" required>
                    @error('account_number')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="account_name">{{ __('Atas Nama (Pemilik Rekening)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name') }}" placeholder="Contoh: PT Toko Sukses Bersama" required>
                    @error('account_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="branch">{{ __('Kantor Cabang (Opsional)') }}</label>
                <input type="text" name="branch" id="branch" class="form-control @error('branch') is-invalid @enderror" value="{{ old('branch') }}" placeholder="Contoh: KCP Sudirman Jakarta">
                @error('branch')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="logo">{{ __('Logo Bank') }}</label>
                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">{{ __('Format: PNG, JPG, WEBP, SVG. Maks 2MB.') }}</small>
                    @error('logo')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="qr_code_image">{{ __('Gambar QRIS / QR Code (Opsional)') }}</label>
                    <input type="file" name="qr_code_image" id="qr_code_image" class="form-control @error('qr_code_image') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">{{ __('Upload gambar QRIS jika pembayaran mendukung scan QR.') }}</small>
                    @error('qr_code_image')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="order">{{ __('Urutan Tampil') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                  <div class="form-group mb-0 mt-3">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                      <label class="custom-control-label" for="is_active">{{ __('Status Aktif') }}</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('payment-banks.index') }}" class="btn btn-secondary mr-2">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

