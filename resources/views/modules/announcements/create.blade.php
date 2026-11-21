@extends('layouts.app')

@section('title', __('Create Announcement'))

@push('page-js')
  <script>
    "use strict";

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('announcements.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Announcement') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('announcements.index') }}">{{ __('Announcements') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Announcement') }}</h2>
    <p class="section-lead">
      {{ __('Fill in the form below to create a new announcement.') }}
    </p>

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card">
          <form action="{{ route('announcements.store') }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('Announcement Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="title"
                  name="title"
                  class="form-control @error('title') is-invalid @enderror"
                  value="{{ old('title') }}"
                  required=""
                >
                <div class="invalid-feedback">
                  @error('title')
                    {{ $message }}
                  @else
                    {{ __('Please fill in the announcement title.') }}
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required="">
                  @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', 'info') === $value)>{{ __($label) }}</option>
                  @endforeach
                </select>
                @error('type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="content">{{ __('Content') }} <span class="text-danger">*</span></label>
                <textarea
                  id="content"
                  name="content"
                  rows="5"
                  class="form-control @error('content') is-invalid @enderror"
                  style="height: 140px;"
                  required=""
                >{{ old('content') }}</textarea>
                <div class="invalid-feedback">
                  @error('content')
                    {{ $message }}
                  @else
                    {{ __('Please fill in the announcement content.') }}
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="published_at">{{ __('Published At') }}</label>
                    <input
                      type="datetime-local"
                      id="published_at"
                      name="published_at"
                      class="form-control @error('published_at') is-invalid @enderror"
                      value="{{ old('published_at') }}"
                    >
                    @error('published_at')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="expires_at">{{ __('Expires At') }}</label>
                    <input
                      type="datetime-local"
                      id="expires_at"
                      name="expires_at"
                      class="form-control @error('expires_at') is-invalid @enderror"
                      value="{{ old('expires_at') }}"
                    >
                    @error('expires_at')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="custom-switch pl-0 mt-2">
                      <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', true))>
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">{{ __('Active') }}</span>
                    </label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="custom-switch pl-0 mt-2">
                      <input type="checkbox" name="is_pinned" value="1" class="custom-switch-input" @checked(old('is_pinned', false))>
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">{{ __('Pin Announcement') }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('announcements.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
