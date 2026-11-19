@extends('layouts.app')

@section('title', __('Edit Keunggulan'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Edit Keunggulan') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-why-choose-us.index') }}">{{ __('Why Choose Us') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-edit mr-2 text-warning"></i> {{ __('Edit Keunggulan') }}: {{ $whyChooseUs->title }}</h4>
      </div>
      <form action="{{ route('lp-why-choose-us.update', $whyChooseUs) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-group">
            <label for="title">{{ __('Judul') }} <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $whyChooseUs->title) }}" class="form-control @error('title') is-invalid @enderror" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="icon">{{ __('Ikon (Lucide)') }} <span class="text-danger">*</span></label>
                <input type="text" name="icon" id="icon" value="{{ old('icon', $whyChooseUs->icon) }}" class="form-control @error('icon') is-invalid @enderror" required>
                <small class="form-text text-muted">{{ __('Nama ikon Lucide yang digunakan pada tampilan landing page.') }}</small>
                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="order">{{ __('Urutan') }}</label>
                <input type="number" name="order" id="order" min="0" value="{{ old('order', $whyChooseUs->order) }}" class="form-control @error('order') is-invalid @enderror">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="d-block">{{ __('Status') }}</label>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', $whyChooseUs->is_active))>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Aktif') }}</span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-group mb-0">
            <label for="description">{{ __('Deskripsi') }}</label>
            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $whyChooseUs->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <a href="{{ route('lp-why-choose-us.index') }}" class="btn btn-secondary mr-1">{{ __('Kembali') }}</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Perbarui') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
