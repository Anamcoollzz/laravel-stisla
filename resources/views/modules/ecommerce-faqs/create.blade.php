@extends('layouts.app')

@section('title', __('Shop › Add FAQ'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('ecommerce-faqs.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Add FAQ') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('ecommerce-faqs.index') }}">{{ __('FAQs') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create New FAQ') }}</h2>
    <p class="section-lead">
      {{ __('Add a new question and answer to the storefront FAQ page.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('ecommerce-faqs.store') }}" method="POST">
            @csrf
            <div class="card-header">
              <h4>{{ __('FAQ Information') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="question">{{ __('Question') }} <span class="text-danger">*</span></label>
                <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question') }}" placeholder="e.g. What is the multi vendor services?" required>
                @error('question')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="column">{{ __('Display Column') }} <span class="text-danger">*</span></label>
                    <select name="column" id="column" class="form-control @error('column') is-invalid @enderror" required>
                      <option value="left" {{ old('column', 'left') === 'left' ? 'selected' : '' }}>{{ __('Left Column') }}</option>
                      <option value="right" {{ old('column') === 'right' ? 'selected' : '' }}>{{ __('Right Column') }}</option>
                    </select>
                    @error('column')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="order">{{ __('Display Order') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="answer">{{ __('Answer') }} <span class="text-danger">*</span></label>
                <textarea name="answer" id="answer" rows="5" class="form-control @error('answer') is-invalid @enderror" required style="height: 150px;" placeholder="Enter detailed answer...">{{ old('answer') }}</textarea>
                @error('answer')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <div class="control-label">{{ __('Status') }}</div>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ old('is_active', true) ? 'checked' : '' }}>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Active (Visible on Storefront)') }}</span>
                </label>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('ecommerce-faqs.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save FAQ') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
