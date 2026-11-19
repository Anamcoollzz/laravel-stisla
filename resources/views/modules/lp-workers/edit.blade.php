@extends('layouts.app')

@section('title', __('Edit Kandidat Pekerja'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Edit Kandidat Pekerja') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-workers.index') }}">{{ __('Workers') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-user-edit mr-2 text-warning"></i> {{ __('Edit Pekerja') }}: {{ $worker->name }} ({{ $worker->code }})</h4>
      </div>
      <form action="{{ route('lp-workers.update', $worker) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="code">{{ __('Kode Kandidat') }} <span class="text-danger">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code', $worker->code) }}" class="form-control @error('code') is-invalid @enderror" required>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label for="name">{{ __('Nama Lengkap') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $worker->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="slug">{{ __('Slug') }}</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $worker->slug) }}" class="form-control @error('slug') is-invalid @enderror">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="category">{{ __('Kategori') }} <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                  @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', $worker->category) === $key)>{{ $label }}</option>
                  @endforeach
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                  @foreach (['Available', 'In Training', 'Placed'] as $st)
                    <option value="{{ $st }}" @selected(old('status', $worker->status) === $st)>{{ $st }}</option>
                  @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="age">{{ __('Usia') }} <span class="text-danger">*</span></label>
                <input type="number" name="age" id="age" min="17" max="75" value="{{ old('age', $worker->age) }}" class="form-control @error('age') is-invalid @enderror" required>
                @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="experience_years">{{ __('Pengalaman (Thn)') }} <span class="text-danger">*</span></label>
                <input type="number" step="0.5" min="0" name="experience_years" id="experience_years" value="{{ old('experience_years', $worker->experience_years) }}" class="form-control @error('experience_years') is-invalid @enderror" required>
                @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="d-block">{{ __('Featured') }}</label>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_featured" value="1" class="custom-switch-input" @checked(old('is_featured', $worker->is_featured))>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Ya') }}</span>
                </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="origin">{{ __('Asal Daerah') }}</label>
                <input type="text" name="origin" id="origin" value="{{ old('origin', $worker->origin) }}" class="form-control @error('origin') is-invalid @enderror">
                @error('origin')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="origin_region">{{ __('Provinsi / Region') }}</label>
                <input type="text" name="origin_region" id="origin_region" value="{{ old('origin_region', $worker->origin_region) }}" class="form-control @error('origin_region') is-invalid @enderror">
                @error('origin_region')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="education">{{ __('Pendidikan') }}</label>
                <input type="text" name="education" id="education" value="{{ old('education', $worker->education) }}" class="form-control @error('education') is-invalid @enderror">
                @error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="published_date">{{ __('Tanggal') }}</label>
                <input type="date" name="published_date" id="published_date" value="{{ old('published_date', $worker->published_date?->format('Y-m-d')) }}" class="form-control @error('published_date') is-invalid @enderror">
                @error('published_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="photo_file">{{ __('Upload Foto Baru') }}</label>
                <input type="file" name="photo_file" id="photo_file" class="form-control @error('photo_file') is-invalid @enderror" accept="image/*">
                @error('photo_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="mt-2">
                  <img src="{{ $worker->photo_url }}" alt="{{ $worker->name }}" class="rounded border" style="max-height: 90px;">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="photo">{{ __('Atau Path / URL Foto') }}</label>
                <input type="text" name="photo" id="photo" value="{{ old('photo', $worker->photo) }}" class="form-control @error('photo') is-invalid @enderror">
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="medical_check">{{ __('Medical Check / Kesehatan') }}</label>
            <input type="text" name="medical_check" id="medical_check" value="{{ old('medical_check', $worker->medical_check) }}" class="form-control @error('medical_check') is-invalid @enderror">
            @error('medical_check')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label for="bio">{{ __('Bio / Ringkasan Profil') }}</label>
            <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $worker->bio) }}</textarea>
            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          @php
            $prefLines = collect($worker->placement_preferences ?? [])->map(fn ($v, $k) => "{$k}: {$v}")->implode("\n");
          @endphp

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="skills_text">{{ __('Keahlian (1 per baris)') }}</label>
                <textarea name="skills_text" id="skills_text" rows="4" class="form-control">{{ old('skills_text', implode("\n", $worker->skills ?? [])) }}</textarea>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="languages_text">{{ __('Bahasa (1 per baris)') }}</label>
                <textarea name="languages_text" id="languages_text" rows="4" class="form-control">{{ old('languages_text', implode("\n", $worker->languages ?? [])) }}</textarea>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="training_certifications_text">{{ __('Sertifikasi Pelatihan (1 per baris)') }}</label>
                <textarea name="training_certifications_text" id="training_certifications_text" rows="4" class="form-control">{{ old('training_certifications_text', implode("\n", $worker->training_certifications ?? [])) }}</textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label for="work_history_text">{{ __('Riwayat Kerja (1 per baris)') }}</label>
                <textarea name="work_history_text" id="work_history_text" rows="4" class="form-control">{{ old('work_history_text', implode("\n", $worker->work_history ?? [])) }}</textarea>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label for="placement_preferences_text">{{ __('Preferensi Penempatan (Key: Value per baris)') }}</label>
                <textarea name="placement_preferences_text" id="placement_preferences_text" rows="4" class="form-control">{{ old('placement_preferences_text', $prefLines) }}</textarea>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label for="gallery_text">{{ __('Galeri Foto Path (1 per baris)') }}</label>
                <textarea name="gallery_text" id="gallery_text" rows="4" class="form-control">{{ old('gallery_text', implode("\n", $worker->gallery ?? [])) }}</textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-right">
          <a href="{{ route('lp-workers.index') }}" class="btn btn-secondary mr-1">{{ __('Kembali') }}</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Perbarui') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
