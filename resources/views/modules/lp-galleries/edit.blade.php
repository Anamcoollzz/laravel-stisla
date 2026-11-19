@extends('layouts.app')

@section('title', __('Edit Galeri'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Edit Galeri') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-galleries.index') }}">{{ __('Galleries') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-edit mr-2 text-warning"></i> {{ __('Edit Galeri') }}: {{ $lpGallery->title }}</h4>
      </div>
      <form action="{{ route('lp-galleries.update', $lpGallery) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="title">{{ __('Judul') }} <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $lpGallery->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="category">{{ __('Kategori') }} <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat }}" @selected(old('category', $lpGallery->category) === $cat)>{{ $cat }}</option>
                  @endforeach
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="date">{{ __('Tanggal / Periode') }}</label>
                <input type="text" name="date" id="date" value="{{ old('date', $lpGallery->date) }}" class="form-control @error('date') is-invalid @enderror">
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="image_file">{{ __('Upload Gambar Baru') }}</label>
                <input type="file" name="image_file" id="image_file" class="form-control @error('image_file') is-invalid @enderror" accept="image/*">
                @error('image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="mt-2">
                  <img src="{{ $lpGallery->image_url }}" alt="{{ $lpGallery->title }}" class="rounded border" style="max-height: 90px;">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="image">{{ __('Atau Path / URL Gambar') }}</label>
                <input type="text" name="image" id="image" value="{{ old('image', $lpGallery->image) }}" class="form-control @error('image') is-invalid @enderror">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="order">{{ __('Urutan') }}</label>
                <input type="number" name="order" id="order" min="0" value="{{ old('order', $lpGallery->order) }}" class="form-control @error('order') is-invalid @enderror">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="d-block">{{ __('Status') }}</label>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', $lpGallery->is_active))>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Aktif') }}</span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-group mb-0">
            <label for="description">{{ __('Deskripsi') }}</label>
            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $lpGallery->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <a href="{{ route('lp-galleries.index') }}" class="btn btn-secondary mr-1">{{ __('Kembali') }}</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Perbarui') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
