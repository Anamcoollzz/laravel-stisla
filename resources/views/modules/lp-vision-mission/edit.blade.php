@extends('layouts.app')

@section('title', __('Vision & Mission'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Berhasil')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Vision & Mission') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Vision & Mission') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-bullseye mr-2 text-primary"></i> {{ __('Edit Visi & Misi Perusahaan') }}</h4>
      </div>
      <form action="{{ route('lp-vision-mission.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-5">
              <div class="border rounded p-3 mb-3">
                <h6 class="text-primary mb-3"><i class="fas fa-eye mr-1"></i> {{ __('Visi Perusahaan') }}</h6>
                <div class="form-group">
                  <label for="vision_eyebrow">{{ __('Badge Visi') }} <span class="text-danger">*</span></label>
                  <input type="text" name="vision_eyebrow" id="vision_eyebrow" value="{{ old('vision_eyebrow', $visionMission->vision_eyebrow) }}" class="form-control @error('vision_eyebrow') is-invalid @enderror" required>
                  @error('vision_eyebrow')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                  <label for="vision_title">{{ __('Judul Visi') }} <span class="text-danger">*</span></label>
                  <input type="text" name="vision_title" id="vision_title" value="{{ old('vision_title', $visionMission->vision_title) }}" class="form-control @error('vision_title') is-invalid @enderror" required>
                  @error('vision_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group mb-0">
                  <label for="vision_description">{{ __('Deskripsi Visi') }} <span class="text-danger">*</span></label>
                  <textarea name="vision_description" id="vision_description" rows="5" class="form-control @error('vision_description') is-invalid @enderror" required>{{ old('vision_description', $visionMission->vision_description) }}</textarea>
                  @error('vision_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>

            <div class="col-md-7">
              <div class="border rounded p-3 mb-3">
                <h6 class="text-primary mb-3"><i class="fas fa-tasks mr-1"></i> {{ __('Misi Perusahaan') }}</h6>
                <div class="form-group">
                  <label for="mission_eyebrow">{{ __('Badge Misi') }} <span class="text-danger">*</span></label>
                  <input type="text" name="mission_eyebrow" id="mission_eyebrow" value="{{ old('mission_eyebrow', $visionMission->mission_eyebrow) }}" class="form-control @error('mission_eyebrow') is-invalid @enderror" required>
                  @error('mission_eyebrow')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                  <label for="mission_title">{{ __('Judul Misi') }} <span class="text-danger">*</span></label>
                  <input type="text" name="mission_title" id="mission_title" value="{{ old('mission_title', $visionMission->mission_title) }}" class="form-control @error('mission_title') is-invalid @enderror" required>
                  @error('mission_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group mb-0">
                  <label for="mission_points_text">{{ __('Poin-Poin Misi (1 per baris)') }}</label>
                  <textarea name="mission_points_text" id="mission_points_text" rows="5" class="form-control @error('mission_points_text') is-invalid @enderror">{{ old('mission_points_text', implode("\n", $visionMission->mission_points ?? [])) }}</textarea>
                  @error('mission_points_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan Perubahan') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
