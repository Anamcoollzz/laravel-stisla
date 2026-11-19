@extends('layouts.app')

@section('title', __('Edit Testimoni'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Edit Testimoni') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-testimonials.index') }}">{{ __('Testimonials') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-edit mr-2 text-warning"></i> {{ __('Edit Testimoni') }}: {{ $recruitmentTestimonial->name }}</h4>
      </div>
      <form action="{{ route('lp-testimonials.update', $recruitmentTestimonial) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="name">{{ __('Nama Klien') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $recruitmentTestimonial->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="location">{{ __('Lokasi') }}</label>
                <input type="text" name="location" id="location" value="{{ old('location', $recruitmentTestimonial->location) }}" class="form-control @error('location') is-invalid @enderror">
                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="service">{{ __('Layanan') }}</label>
                <input type="text" name="service" id="service" value="{{ old('service', $recruitmentTestimonial->service) }}" class="form-control @error('service') is-invalid @enderror">
                @error('service')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="avatar_file">{{ __('Upload Avatar Baru') }}</label>
                <input type="file" name="avatar_file" id="avatar_file" class="form-control @error('avatar_file') is-invalid @enderror" accept="image/*">
                @error('avatar_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="mt-2">
                  <img src="{{ $recruitmentTestimonial->avatar_url }}" alt="{{ $recruitmentTestimonial->name }}" class="rounded-circle border" style="width: 56px; height: 56px; object-fit: cover;">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="avatar">{{ __('Atau Path / URL Avatar') }}</label>
                <input type="text" name="avatar" id="avatar" value="{{ old('avatar', $recruitmentTestimonial->avatar) }}" class="form-control @error('avatar') is-invalid @enderror">
                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="rating">{{ __('Rating') }} <span class="text-danger">*</span></label>
                <select name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror" required>
                  @for ($r = 5; $r >= 1; $r--)
                    <option value="{{ $r }}" @selected((int) old('rating', $recruitmentTestimonial->rating) === $r)>{{ $r }} {{ __('Bintang') }}</option>
                  @endfor
                </select>
                @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="order">{{ __('Urutan') }}</label>
                <input type="number" name="order" id="order" min="0" value="{{ old('order', $recruitmentTestimonial->order) }}" class="form-control @error('order') is-invalid @enderror">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="quote">{{ __('Isi Testimoni') }} <span class="text-danger">*</span></label>
            <textarea name="quote" id="quote" rows="4" class="form-control @error('quote') is-invalid @enderror" required>{{ old('quote', $recruitmentTestimonial->quote) }}</textarea>
            @error('quote')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group mb-0">
            <label class="custom-switch">
              <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', $recruitmentTestimonial->is_active))>
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">{{ __('Aktif') }}</span>
            </label>
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <a href="{{ route('lp-testimonials.index') }}" class="btn btn-secondary mr-1">{{ __('Kembali') }}</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Perbarui') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
