@extends('layouts.app')

@section('title', __('Tambah Milestone'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Tambah Milestone') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-milestones.index') }}">{{ __('Milestones') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-plus-circle mr-2 text-primary"></i> {{ __('Form Milestone Perusahaan') }}</h4>
      </div>
      <form action="{{ route('lp-milestones.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="year">{{ __('Tahun') }} <span class="text-danger">*</span></label>
                <input type="text" name="year" id="year" value="{{ old('year') }}" class="form-control @error('year') is-invalid @enderror" placeholder="2026" required>
                @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label for="title">{{ __('Judul') }} <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="order">{{ __('Urutan') }}</label>
                <input type="number" name="order" id="order" min="0" value="{{ old('order', 0) }}" class="form-control @error('order') is-invalid @enderror">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="d-block">{{ __('Status') }}</label>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', true))>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Aktif') }}</span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-group mb-0">
            <label for="description">{{ __('Deskripsi') }}</label>
            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <a href="{{ route('lp-milestones.index') }}" class="btn btn-secondary mr-1">{{ __('Kembali') }}</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
