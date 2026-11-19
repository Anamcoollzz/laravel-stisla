@extends('layouts.app')

@section('title', __('Tambah FAQ'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Tambah FAQ') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-faqs.index') }}">{{ __('FAQs') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Tambah') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-plus-circle mr-2 text-primary"></i> {{ __('Form FAQ') }}</h4>
      </div>
      <form action="{{ route('lp-faqs.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label for="question">{{ __('Pertanyaan') }} <span class="text-danger">*</span></label>
            <input type="text" name="question" id="question" value="{{ old('question') }}" class="form-control @error('question') is-invalid @enderror" required>
            @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label for="answer">{{ __('Jawaban') }} <span class="text-danger">*</span></label>
            <textarea name="answer" id="answer" rows="4" class="form-control @error('answer') is-invalid @enderror" required>{{ old('answer') }}</textarea>
            @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label for="order">{{ __('Urutan') }}</label>
                <input type="number" name="order" id="order" min="0" value="{{ old('order', 0) }}" class="form-control @error('order') is-invalid @enderror">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label class="d-block">{{ __('Status') }}</label>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', true))>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Aktif') }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <a href="{{ route('lp-faqs.index') }}" class="btn btn-secondary mr-1">{{ __('Kembali') }}</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
