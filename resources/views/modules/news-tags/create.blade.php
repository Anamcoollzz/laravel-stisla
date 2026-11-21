@extends('layouts.app')

@section('title', __('Tambah Tag Berita'))

@push('page-js')
  <script>
    "use strict";

    $('#name').on('input', function() {
      if (!$('#slug').data('manual')) {
        var slug = $(this).val()
          .toLowerCase()
          .trim()
          .replace(/[^a-z0-9\s-]/g, '')
          .replace(/\s+/g, '-')
          .replace(/-+/g, '-');
        $('#slug').val(slug);
      }
    });

    $('#slug').on('input', function() {
      $(this).data('manual', $(this).val().length > 0);
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('news-tags.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Tambah Tag Berita') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('news-tags.index') }}">{{ __('News Tags') }}</a></div>
      <div class="breadcrumb-item">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
          <form action="{{ route('news-tags.store') }}" method="POST">
            @csrf
            <div class="card-header">
              <h4>{{ __('Form Tag Berita') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Nama Tag') }} <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: AI, Startup, UMKM" required autofocus>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group mb-0">
                <label for="slug">{{ __('Slug') }}</label>
                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="otomatis-dari-nama-jika-kosong">
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('news-tags.index') }}" class="btn btn-secondary mr-1">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
