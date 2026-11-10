@extends('layouts.app')

@section('title', __('Shop › Add Team Member'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('our-teams.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Add Team Member') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('our-teams.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('our-teams.index') }}">{{ __('Our Team') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Team Member') }}</h2>
    <p class="section-lead">
      {{ __('Add a new member to the team showcased on the About Us page.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <form action="{{ route('our-teams.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate="">
            @csrf

            <div class="card-header">
              <h4>{{ __('Member Details') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label>{{ __('Full Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Olivia Smith" required autofocus>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>{{ __('Position / Role') }} <span class="text-danger">*</span></label>
                <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" placeholder="e.g. Founder, Co-Founder, Manager, Marketing" required>
                @error('position')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>{{ __('Profile Photo') }}</label>
                <div class="custom-file">
                  <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="customFile" accept="image/*" onchange="previewImage(event)">
                  <label class="custom-file-label" for="customFile">{{ __('Choose file') }}</label>
                </div>
                <small class="form-text text-muted">{{ __('Recommended size: 400x400 px. Max: 2MB.') }}</small>
                @error('image')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <div class="mt-3" id="imagePreviewContainer" style="display: none;">
                  <img id="imagePreview" src="#" alt="Preview" class="img-thumbnail rounded-circle" style="max-height: 120px; width: 120px; object-fit: cover;">
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label><i class="fab fa-twitter text-info"></i> {{ __('Twitter Profile URL') }}</label>
                    <input type="url" name="twitter_url" class="form-control @error('twitter_url') is-invalid @enderror" value="{{ old('twitter_url') }}" placeholder="https://twitter.com/username">
                    @error('twitter_url')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label><i class="fab fa-facebook text-primary"></i> {{ __('Facebook Profile URL') }}</label>
                    <input type="url" name="facebook_url" class="form-control @error('facebook_url') is-invalid @enderror" value="{{ old('facebook_url') }}" placeholder="https://facebook.com/username">
                    @error('facebook_url')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label><i class="fab fa-linkedin text-primary"></i> {{ __('LinkedIn Profile URL') }}</label>
                    <input type="url" name="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/username">
                    @error('linkedin_url')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>{{ __('Display Order') }}</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}" min="0">
                    <small class="form-text text-muted">{{ __('Lower number appears first.') }}</small>
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="d-block">{{ __('Status') }}</label>
                    <div class="custom-control custom-switch mt-2">
                      <input type="checkbox" name="is_active" class="custom-control-input" id="isActiveSwitch" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                      <label class="custom-control-label" for="isActiveSwitch">{{ __('Active (Visible on page)') }}</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('our-teams.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Save Member') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('page-js')
  <script>
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function(){
        const output = document.getElementById('imagePreview');
        const container = document.getElementById('imagePreviewContainer');
        output.src = reader.result;
        container.style.display = 'block';
      };
      if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
        // Update label
        const fileName = event.target.files[0].name;
        event.target.nextElementSibling.innerText = fileName;
      }
    }
  </script>
@endpush

