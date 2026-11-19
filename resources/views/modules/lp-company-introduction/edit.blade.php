@extends('layouts.app')

@section('title', __('Company Introduction'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Berhasil')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Company Introduction') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Company Introduction') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-home mr-2 text-primary"></i> {{ __('Edit Section Company Introduction (Homepage)') }}</h4>
      </div>
      <form action="{{ route('lp-company-introduction.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
          <h6 class="text-primary mb-3"><i class="fas fa-image mr-1"></i> {{ __('Gambar & Kartu Misi') }}</h6>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="image_file">{{ __('Upload Gambar') }}</label>
                <input type="file" name="image_file" id="image_file" class="form-control @error('image_file') is-invalid @enderror" accept="image/*">
                @error('image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="mt-2">
                  <img src="{{ $intro->image_url }}" alt="{{ $intro->image_alt }}" class="rounded border" style="max-height: 90px;">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="image">{{ __('Atau Path / URL Gambar') }}</label>
                <input type="text" name="image" id="image" value="{{ old('image', $intro->image) }}" class="form-control @error('image') is-invalid @enderror">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="image_alt">{{ __('Alt Text Gambar') }}</label>
                <input type="text" name="image_alt" id="image_alt" value="{{ old('image_alt', $intro->image_alt) }}" class="form-control @error('image_alt') is-invalid @enderror">
                @error('image_alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="mission_label">{{ __('Label Misi') }} <span class="text-danger">*</span></label>
                <input type="text" name="mission_label" id="mission_label" value="{{ old('mission_label', $intro->mission_label) }}" class="form-control @error('mission_label') is-invalid @enderror" required>
                @error('mission_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label for="mission_text">{{ __('Teks Misi') }} <span class="text-danger">*</span></label>
                <textarea name="mission_text" id="mission_text" rows="2" class="form-control @error('mission_text') is-invalid @enderror" required>{{ old('mission_text', $intro->mission_text) }}</textarea>
                @error('mission_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-heading mr-1"></i> {{ __('Judul & Deskripsi Utama') }}</h6>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="eyebrow">{{ __('Eyebrow / Badge') }} <span class="text-danger">*</span></label>
                <input type="text" name="eyebrow" id="eyebrow" value="{{ old('eyebrow', $intro->eyebrow) }}" class="form-control @error('eyebrow') is-invalid @enderror" required>
                @error('eyebrow')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label for="title">{{ __('Judul Utama') }} <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $intro->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="paragraph_1">{{ __('Paragraf 1') }} <span class="text-danger">*</span></label>
            <textarea name="paragraph_1" id="paragraph_1" rows="3" class="form-control @error('paragraph_1') is-invalid @enderror" required>{{ old('paragraph_1', $intro->paragraph_1) }}</textarea>
            @error('paragraph_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label for="paragraph_2">{{ __('Paragraf 2') }}</label>
            <textarea name="paragraph_2" id="paragraph_2" rows="3" class="form-control @error('paragraph_2') is-invalid @enderror">{{ old('paragraph_2', $intro->paragraph_2) }}</textarea>
            @error('paragraph_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-th-large mr-1"></i> {{ __('2 Kartu Keunggulan Singkat') }}</h6>
          <div class="row">
            <div class="col-md-6">
              <div class="border rounded p-3 mb-3">
                <h6 class="font-weight-bold mb-3">{{ __('Kartu 1') }}</h6>
                <div class="form-group">
                  <label for="feature_1_icon">{{ __('Ikon (Lucide)') }} <span class="text-danger">*</span></label>
                  <input type="text" name="feature_1_icon" id="feature_1_icon" value="{{ old('feature_1_icon', $intro->feature_1_icon) }}" class="form-control @error('feature_1_icon') is-invalid @enderror" required>
                  @error('feature_1_icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                  <label for="feature_1_title">{{ __('Judul Kartu 1') }} <span class="text-danger">*</span></label>
                  <input type="text" name="feature_1_title" id="feature_1_title" value="{{ old('feature_1_title', $intro->feature_1_title) }}" class="form-control @error('feature_1_title') is-invalid @enderror" required>
                  @error('feature_1_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group mb-0">
                  <label for="feature_1_description">{{ __('Deskripsi Kartu 1') }} <span class="text-danger">*</span></label>
                  <textarea name="feature_1_description" id="feature_1_description" rows="2" class="form-control @error('feature_1_description') is-invalid @enderror" required>{{ old('feature_1_description', $intro->feature_1_description) }}</textarea>
                  @error('feature_1_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 mb-3">
                <h6 class="font-weight-bold mb-3">{{ __('Kartu 2') }}</h6>
                <div class="form-group">
                  <label for="feature_2_icon">{{ __('Ikon (Lucide)') }} <span class="text-danger">*</span></label>
                  <input type="text" name="feature_2_icon" id="feature_2_icon" value="{{ old('feature_2_icon', $intro->feature_2_icon) }}" class="form-control @error('feature_2_icon') is-invalid @enderror" required>
                  @error('feature_2_icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                  <label for="feature_2_title">{{ __('Judul Kartu 2') }} <span class="text-danger">*</span></label>
                  <input type="text" name="feature_2_title" id="feature_2_title" value="{{ old('feature_2_title', $intro->feature_2_title) }}" class="form-control @error('feature_2_title') is-invalid @enderror" required>
                  @error('feature_2_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group mb-0">
                  <label for="feature_2_description">{{ __('Deskripsi Kartu 2') }} <span class="text-danger">*</span></label>
                  <textarea name="feature_2_description" id="feature_2_description" rows="2" class="form-control @error('feature_2_description') is-invalid @enderror" required>{{ old('feature_2_description', $intro->feature_2_description) }}</textarea>
                  @error('feature_2_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
          </div>

          <hr>
          <h6 class="text-primary mb-3"><i class="fas fa-mouse-pointer mr-1"></i> {{ __('Tombol Aksi (CTA)') }}</h6>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group mb-0">
                <label for="primary_button_label">{{ __('Label Tombol Utama') }} <span class="text-danger">*</span></label>
                <input type="text" name="primary_button_label" id="primary_button_label" value="{{ old('primary_button_label', $intro->primary_button_label) }}" class="form-control @error('primary_button_label') is-invalid @enderror" required>
                @error('primary_button_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-0">
                <label for="primary_button_url">{{ __('URL Tombol Utama') }}</label>
                <input type="text" name="primary_button_url" id="primary_button_url" value="{{ old('primary_button_url', $intro->primary_button_url) }}" class="form-control @error('primary_button_url') is-invalid @enderror" placeholder="{{ __('Default: halaman About Us') }}">
                @error('primary_button_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-0">
                <label for="whatsapp_button_label">{{ __('Label Tombol WhatsApp') }} <span class="text-danger">*</span></label>
                <input type="text" name="whatsapp_button_label" id="whatsapp_button_label" value="{{ old('whatsapp_button_label', $intro->whatsapp_button_label) }}" class="form-control @error('whatsapp_button_label') is-invalid @enderror" required>
                @error('whatsapp_button_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-0">
                <label for="whatsapp_button_message">{{ __('Pesan Khusus WhatsApp') }}</label>
                <input type="text" name="whatsapp_button_message" id="whatsapp_button_message" value="{{ old('whatsapp_button_message', $intro->whatsapp_button_message) }}" class="form-control @error('whatsapp_button_message') is-invalid @enderror" placeholder="{{ __('Default: pesan perusahaan') }}">
                @error('whatsapp_button_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan Perubahan') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
