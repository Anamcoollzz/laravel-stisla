@extends('layouts.app')

@section('title', __('Modules › Edit Teacher'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('teachers.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Teacher') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('index.html') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('teachers.index') }}">{{ __('Teachers') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Teacher') }}</h2>
    <p class="section-lead">
      {{ __('Update the teacher information below.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <form action="{{ route('teachers.update', $teacher) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Teacher Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="nip">{{ __('NIP') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="nip"
                    name="nip"
                    class="form-control @error('nip') is-invalid @enderror"
                    value="{{ old('nip', $teacher->nip) }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('nip')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the NIP.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="name">{{ __('Teacher Name') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $teacher->name) }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('name')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the teacher name.') }}
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $teacher->email) }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('email')
                      {{ $message }}
                    @else
                      {{ __('Please fill in a valid email.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="phone">{{ __('Phone') }}</label>
                  <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $teacher->phone) }}"
                  >
                  @error('phone')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <label for="gender">{{ __('Gender') }} <span class="text-danger">*</span></label>
                <select
                  id="gender"
                  name="gender"
                  class="form-control @error('gender') is-invalid @enderror"
                  required=""
                >
                  <option value="">-- {{ __('Select Gender') }} --</option>
                  <option value="L" @selected(old('gender', $teacher->gender) === 'L')>{{ __('Male') }}</option>
                  <option value="P" @selected(old('gender', $teacher->gender) === 'P')>{{ __('Female') }}</option>
                </select>
                <div class="invalid-feedback">
                  @error('gender')
                    {{ $message }}
                  @else
                    {{ __('Please select a gender.') }}
                  @enderror
                </div>
              </div>

              <div class="form-group mb-0">
                <label for="address">{{ __('Address') }}</label>
                <textarea
                  id="address"
                  name="address"
                  class="form-control @error('address') is-invalid @enderror"
                  rows="3"
                >{{ old('address', $teacher->address) }}</textarea>
                @error('address')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('teachers.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

