@extends('layouts.app')

@section('title', __('Modules › Create Student'))

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
      <a href="{{ route('students.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Student') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('index.html') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('students.index') }}">{{ __('Students') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Student') }}</h2>
    <p class="section-lead">
      {{ __('Fill in the form below to create a new student.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <form action="{{ route('students.store') }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('Student Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="nis">{{ __('NIS') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="nis"
                    name="nis"
                    class="form-control @error('nis') is-invalid @enderror"
                    value="{{ old('nis') }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('nis')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the NIS.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="name">{{ __('Student Name') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('name')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the student name.') }}
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="class_id">{{ __('Class') }} <span class="text-danger">*</span></label>
                  <select
                    id="class_id"
                    name="class_id"
                    class="form-control @error('class_id') is-invalid @enderror"
                    required=""
                  >
                    <option value="">-- {{ __('Select Class') }} --</option>
                    @foreach ($classes as $schoolClass)
                      <option value="{{ $schoolClass->id }}" @selected((string) old('class_id') === (string) $schoolClass->id)>
                        {{ $schoolClass->class_name }}
                      </option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">
                    @error('class_id')
                      {{ $message }}
                    @else
                      {{ __('Please select a class.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="gender">{{ __('Gender') }} <span class="text-danger">*</span></label>
                  <select
                    id="gender"
                    name="gender"
                    class="form-control @error('gender') is-invalid @enderror"
                    required=""
                  >
                    <option value="">-- {{ __('Select Gender') }} --</option>
                    <option value="L" @selected(old('gender') === 'L')>{{ __('Male') }}</option>
                    <option value="P" @selected(old('gender') === 'P')>{{ __('Female') }}</option>
                  </select>
                  <div class="invalid-feedback">
                    @error('gender')
                      {{ $message }}
                    @else
                      {{ __('Please select a gender.') }}
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="email">{{ __('Email') }}</label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                  >
                  <div class="invalid-feedback">
                    @error('email')
                      {{ $message }}
                    @else
                      {{ __('Please fill in a valid email.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-4">
                  <label for="phone">{{ __('Phone') }}</label>
                  <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}"
                  >
                  @error('phone')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>

                <div class="form-group col-md-4">
                  <label for="birth_date">{{ __('Birth Date') }}</label>
                  <input
                    type="date"
                    id="birth_date"
                    name="birth_date"
                    class="form-control @error('birth_date') is-invalid @enderror"
                    value="{{ old('birth_date') }}"
                  >
                  @error('birth_date')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
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
                >{{ old('address') }}</textarea>
                @error('address')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('students.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

