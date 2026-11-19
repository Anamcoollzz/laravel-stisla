@extends('layouts.app')

@section('title', __('Tambah Kategori Berita'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('news-categories.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Tambah Kategori Berita') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('news-categories.index') }}">{{ __('News Categories') }}</a></div>
      <div class="breadcrumb-item">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
          <form action="{{ route('news-categories.store') }}" method="POST">
            @csrf
            <div class="card-header">
              <h4>{{ __('Form Kategori Berita') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Nama Kategori') }} <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Teknologi, Bisnis, Olahraga" required autofocus>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group mb-0">
                <label for="description">{{ __('Deskripsi') }}</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" style="height: 120px;" placeholder="Deskripsi singkat kategori berita...">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('news-categories.index') }}" class="btn btn-secondary mr-1">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
