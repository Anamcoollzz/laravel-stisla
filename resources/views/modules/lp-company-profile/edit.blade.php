@extends('layouts.app')

@section('title', __('Contact & Company Info'))

@push('page-js')
  <script>
    "use strict";

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Contact & Company Info') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Contact & Company Info') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-address-card mr-2 text-primary"></i> {{ __('Edit Informasi Kontak & Perusahaan') }}</h4>
      </div>
      @php
        $identityError = $errors->hasAny(['name', 'legal_name', 'license_number', 'logo_file', 'logo', 'copyright_year', 'tagline', 'footer_description', 'instagram_url', 'facebook_url', 'youtube_url']);
        $hoursError = $errors->hasAny(['operating_hours_weekday', 'operating_hours_saturday', 'operating_hours_sunday', 'map_embed_url']);
        $activeTab = $identityError ? 'identity' : ($hoursError ? 'hours' : 'contact');
      @endphp
      <form action="{{ route('lp-company-profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
          <ul class="nav nav-tabs mb-4" id="companyProfileTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link {{ $activeTab === 'contact' ? 'active' : '' }}" id="contact-tab" data-toggle="tab" href="#tab-contact" role="tab" aria-controls="tab-contact" aria-selected="{{ $activeTab === 'contact' ? 'true' : 'false' }}">
                <i class="fas fa-phone-alt mr-1"></i> {{ __('Informasi Kontak') }}
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeTab === 'identity' ? 'active' : '' }}" id="identity-tab" data-toggle="tab" href="#tab-identity" role="tab" aria-controls="tab-identity" aria-selected="{{ $activeTab === 'identity' ? 'true' : 'false' }}">
                <i class="fas fa-building mr-1"></i> {{ __('Identitas & Sosial Media') }}
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeTab === 'hours' ? 'active' : '' }}" id="hours-tab" data-toggle="tab" href="#tab-hours" role="tab" aria-controls="tab-hours" aria-selected="{{ $activeTab === 'hours' ? 'true' : 'false' }}">
                <i class="fas fa-clock mr-1"></i> {{ __('Jam Operasional & Peta') }}
              </a>
            </li>
          </ul>

          <div class="tab-content" id="companyProfileTabContent">
            <div class="tab-pane fade {{ $activeTab === 'contact' ? 'show active' : '' }}" id="tab-contact" role="tabpanel" aria-labelledby="contact-tab">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="phone_display">{{ __('Telepon Utama') }} <span class="text-danger">*</span></label>
                    <input type="text" name="phone_display" id="phone_display" value="{{ old('phone_display', $profile->phone_display) }}" class="form-control @error('phone_display') is-invalid @enderror" required>
                    @error('phone_display')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="secondary_phone">{{ __('Telepon Sekunder') }}</label>
                    <input type="text" name="secondary_phone" id="secondary_phone" value="{{ old('secondary_phone', $profile->secondary_phone) }}" class="form-control @error('secondary_phone') is-invalid @enderror">
                    @error('secondary_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $profile->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="whatsapp_number">{{ __('Nomor WhatsApp (Format 62...)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number) }}" class="form-control @error('whatsapp_number') is-invalid @enderror" required>
                    @error('whatsapp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="default_whatsapp_message">{{ __('Pesan Default WhatsApp') }}</label>
                    <input type="text" name="default_whatsapp_message" id="default_whatsapp_message" value="{{ old('default_whatsapp_message', $profile->default_whatsapp_message) }}" class="form-control @error('default_whatsapp_message') is-invalid @enderror">
                    @error('default_whatsapp_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group mb-0">
                    <label for="address">{{ __('Alamat Lengkap') }} <span class="text-danger">*</span></label>
                    <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $profile->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group mb-0">
                    <label for="short_address">{{ __('Alamat Singkat') }}</label>
                    <input type="text" name="short_address" id="short_address" value="{{ old('short_address', $profile->short_address) }}" class="form-control @error('short_address') is-invalid @enderror">
                    @error('short_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade {{ $activeTab === 'identity' ? 'show active' : '' }}" id="tab-identity" role="tabpanel" aria-labelledby="identity-tab">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="name">{{ __('Nama Brand') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $profile->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="legal_name">{{ __('Nama Legal') }} <span class="text-danger">*</span></label>
                    <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name', $profile->legal_name) }}" class="form-control @error('legal_name') is-invalid @enderror" required>
                    @error('legal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="license_number">{{ __('Nomor Lisensi') }}</label>
                    <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $profile->license_number) }}" class="form-control @error('license_number') is-invalid @enderror">
                    @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="logo_file">{{ __('Upload Logo Perusahaan') }}</label>
                    <input type="file" name="logo_file" id="logo_file" class="form-control @error('logo_file') is-invalid @enderror" accept="image/*">
                    @error('logo_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="mt-2">
                      <img src="{{ $profile->logo_url }}" alt="{{ $profile->legal_name }}" class="rounded border p-1 bg-light" style="max-height: 56px;">
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="logo">{{ __('Atau Path / URL Logo') }}</label>
                    <input type="text" name="logo" id="logo" value="{{ old('logo', $profile->logo) }}" class="form-control @error('logo') is-invalid @enderror" placeholder="lp-assets/images/general/logo.svg">
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="copyright_year">{{ __('Tahun Copyright') }}</label>
                    <input type="text" name="copyright_year" id="copyright_year" value="{{ old('copyright_year', $profile->copyright_year ?: date('Y')) }}" class="form-control @error('copyright_year') is-invalid @enderror" placeholder="{{ date('Y') }}">
                    @error('copyright_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="tagline">{{ __('Tagline') }}</label>
                <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $profile->tagline) }}" class="form-control @error('tagline') is-invalid @enderror">
                @error('tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="form-group">
                <label for="footer_description">{{ __('Deskripsi Footer') }}</label>
                <textarea name="footer_description" id="footer_description" rows="2" class="form-control @error('footer_description') is-invalid @enderror">{{ old('footer_description', $profile->footer_description) }}</textarea>
                @error('footer_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group mb-0">
                    <label for="instagram_url">{{ __('Instagram URL') }}</label>
                    <input type="url" name="instagram_url" id="instagram_url" value="{{ old('instagram_url', $profile->instagram_url) }}" class="form-control @error('instagram_url') is-invalid @enderror">
                    @error('instagram_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group mb-0">
                    <label for="facebook_url">{{ __('Facebook URL') }}</label>
                    <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $profile->facebook_url) }}" class="form-control @error('facebook_url') is-invalid @enderror">
                    @error('facebook_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group mb-0">
                    <label for="youtube_url">{{ __('YouTube URL') }}</label>
                    <input type="url" name="youtube_url" id="youtube_url" value="{{ old('youtube_url', $profile->youtube_url) }}" class="form-control @error('youtube_url') is-invalid @enderror">
                    @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade {{ $activeTab === 'hours' ? 'show active' : '' }}" id="tab-hours" role="tabpanel" aria-labelledby="hours-tab">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="operating_hours_weekday">{{ __('Senin - Jumat') }}</label>
                    <input type="text" name="operating_hours_weekday" id="operating_hours_weekday" value="{{ old('operating_hours_weekday', $profile->operating_hours_weekday) }}" class="form-control @error('operating_hours_weekday') is-invalid @enderror">
                    @error('operating_hours_weekday')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="operating_hours_saturday">{{ __('Sabtu') }}</label>
                    <input type="text" name="operating_hours_saturday" id="operating_hours_saturday" value="{{ old('operating_hours_saturday', $profile->operating_hours_saturday) }}" class="form-control @error('operating_hours_saturday') is-invalid @enderror">
                    @error('operating_hours_saturday')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="operating_hours_sunday">{{ __('Minggu & Hari Libur') }}</label>
                    <input type="text" name="operating_hours_sunday" id="operating_hours_sunday" value="{{ old('operating_hours_sunday', $profile->operating_hours_sunday) }}" class="form-control @error('operating_hours_sunday') is-invalid @enderror">
                    @error('operating_hours_sunday')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
              </div>

              <div class="form-group mb-0">
                <label for="map_embed_url">{{ __('Google Maps Embed URL') }}</label>
                <input type="text" name="map_embed_url" id="map_embed_url" value="{{ old('map_embed_url', $profile->map_embed_url) }}" class="form-control @error('map_embed_url') is-invalid @enderror">
                @error('map_embed_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
