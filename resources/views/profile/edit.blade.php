@extends('layouts.app')

@section('title', __('Profile'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-social/bootstrap-social.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    function previewAvatar(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#avatar-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    $("#avatar-input").on('change', function() {
      previewAvatar(this);
    });

    function copyToClipboard(text) {
      if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
      } else {
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        return Promise.resolve();
      }
    }

    function generateStrongPassword(length = 16) {
      var uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
      var lowercase = 'abcdefghijkmnopqrstuvwxyz';
      var numbers = '23456789';
      var symbols = '!@#$%^&*()-_=+[]{}';
      var all = uppercase + lowercase + numbers + symbols;

      var password = '';
      password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
      password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
      password += numbers.charAt(Math.floor(Math.random() * numbers.length));
      password += symbols.charAt(Math.floor(Math.random() * symbols.length));

      for (var i = password.length; i < length; i++) {
        password += all.charAt(Math.floor(Math.random() * all.length));
      }

      return password.split('').sort(function() { return 0.5 - Math.random(); }).join('');
    }

    $('#btn-generate-password').on('click', function() {
      var generated = generateStrongPassword(16);
      $('#password').val(generated).attr('type', 'text');
      $('#password_confirmation').val(generated).attr('type', 'text');

      $('[data-target="#password"] i, [data-target="#password_confirmation"] i')
        .removeClass('fa-eye')
        .addClass('fa-eye-slash');

      copyToClipboard(generated).then(function() {
        swal(@json(__('Password Generated!')), @json(__('A strong password has been generated and copied to your clipboard:')) + '\n\n' + generated, 'success');
      }).catch(function() {
        swal(@json(__('Password Generated!')), @json(__('A strong password has been generated:')) + '\n\n' + generated, 'success');
      });
    });

    $('#btn-copy-password').on('click', function() {
      var val = $('#password').val();
      if (!val) {
        swal(@json(__('Notice')), @json(__('Please enter or generate a password first.')), 'info');
        return;
      }
      copyToClipboard(val).then(function() {
        swal(@json(__('Copied!')), @json(__('Password copied to clipboard!')), 'success');
      });
    });

    $(document).on('click', '.btn-toggle-pw', function() {
      var targetSelector = $(this).data('target');
      var input = $(targetSelector);
      var icon = $(this).find('i');

      if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
    });

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Profile') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Profile') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Hi, :name!', ['name' => $user->name]) }}</h2>
    <p class="section-lead">
      {{ __('Manage your profile identity, avatar picture, and account security.') }}
    </p>

    <div class="row mt-sm-4">
      {{-- Left Column: User Identity Widget --}}
      <div class="col-12 col-md-12 col-lg-5">
        <div class="card profile-widget">
          <div class="profile-widget-header">
            <img alt="{{ $user->name }}" src="{{ $user->avatar_url }}" class="rounded-circle profile-widget-picture" style="width: 100px; height: 100px; object-fit: cover;">
            <div class="profile-widget-items">
              <div class="profile-widget-item">
                <div class="profile-widget-item-label">{{ __('Role') }}</div>
                <div class="profile-widget-item-value">
                  @forelse ($user->roles as $role)
                    <span class="badge badge-primary">{{ ucfirst($role->name) }}</span>
                  @empty
                    <span class="badge badge-secondary">{{ ucfirst($user->role ?? 'User') }}</span>
                  @endforelse
                </div>
              </div>
              <div class="profile-widget-item">
                <div class="profile-widget-item-label">{{ __('Permissions') }}</div>
                <div class="profile-widget-item-value">
                  <span class="badge badge-info">{{ $user->getAllPermissions()->count() }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="profile-widget-description">
            <div class="profile-widget-name">{{ $user->name }} <div class="text-muted d-inline font-weight-normal"><div class="slash"></div> {{ $user->email }}</div></div>
            <ul class="list-group list-group-flush mt-3">
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted"><i class="fas fa-envelope mr-2"></i>{{ __('Email Address') }}</span>
                <span class="font-weight-600">{{ $user->email }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted"><i class="fas fa-shield-alt mr-2"></i>{{ __('Assigned Roles') }}</span>
                <span>
                  @forelse ($user->roles as $role)
                    <span class="badge badge-light border">{{ $role->name }}</span>
                  @empty
                    <span class="badge badge-light border">{{ $user->role ?? 'user' }}</span>
                  @endforelse
                </span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted"><i class="fas fa-calendar-check mr-2"></i>{{ __('Account Created') }}</span>
                <span class="font-weight-600">{{ $user->created_at?->format('d M Y, H:i') }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted"><i class="fas fa-sign-in-alt mr-2"></i>{{ __('Last Logged In') }}</span>
                <span class="font-weight-600">
                  @if ($user->last_logged_in)
                    {{ $user->last_logged_in->format('d M Y, H:i') }} <small class="text-muted">({{ $user->last_logged_in->diffForHumans() }})</small>
                  @else
                    {{ __('Never') }}
                  @endif
                </span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted"><i class="fas fa-clock mr-2"></i>{{ __('Last Updated') }}</span>
                <span class="font-weight-600">{{ $user->updated_at?->diffForHumans() }}</span>
              </li>
            </ul>
          </div>
          @if ($user->facebook_url || $user->twitter_url || $user->github_url || $user->instagram_url)
            <div class="card-footer text-center">
              <div class="font-weight-bold mb-2">{{ __('Social Links') }}</div>
              @if ($user->facebook_url)
                <a href="{{ $user->facebook_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-social-icon btn-facebook mr-1" title="Facebook">
                  <i class="fab fa-facebook-f"></i>
                </a>
              @endif
              @if ($user->twitter_url)
                <a href="{{ $user->twitter_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-social-icon btn-twitter mr-1" title="Twitter / X">
                  <i class="fab fa-twitter"></i>
                </a>
              @endif
              @if ($user->github_url)
                <a href="{{ $user->github_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-social-icon btn-github mr-1" title="GitHub">
                  <i class="fab fa-github"></i>
                </a>
              @endif
              @if ($user->instagram_url)
                <a href="{{ $user->instagram_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-social-icon btn-instagram" title="Instagram">
                  <i class="fab fa-instagram"></i>
                </a>
              @endif
            </div>
          @endif
        </div>
      </div>

      {{-- Right Column: Edit Profile & Update Password Cards --}}
      <div class="col-12 col-md-12 col-lg-7">
        {{-- Card 1: Profile Information --}}
        <div class="card">
          <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Edit Profile') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group row align-items-center">
                <label class="col-sm-3 col-form-label">{{ __('Avatar') }}</label>
                <div class="col-sm-9">
                  <div class="d-flex align-items-center mb-2">
                    <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar Preview" class="rounded-circle mr-3 border" style="width: 70px; height: 70px; object-fit: cover;">
                    <div>
                      <input type="file" name="avatar" id="avatar-input" class="form-control-file @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
                      <small class="form-text text-muted">
                        {{ __('Allowed formats: JPG, PNG, WEBP, GIF. Max size: 10MB.') }}
                      </small>
                      @error('avatar')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  @if ($user->avatar)
                    <div class="custom-control custom-checkbox mt-1">
                      <input type="checkbox" name="remove_avatar" value="1" class="custom-control-input" id="remove_avatar">
                      <label class="custom-control-label text-danger" for="remove_avatar">
                        <i class="fas fa-trash-alt mr-1"></i> {{ __('Remove custom avatar and restore default') }}
                      </label>
                    </div>
                  @endif
                </div>
              </div>

              <div class="form-group row">
                <label for="name" class="col-sm-3 col-form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                <label for="facebook_url" class="col-sm-3 col-form-label"><i class="fab fa-facebook-f mr-1 text-primary"></i> {{ __('Facebook') }}</label>
                <div class="col-sm-9">
                  <input type="url" name="facebook_url" id="facebook_url" class="form-control @error('facebook_url') is-invalid @enderror" value="{{ old('facebook_url', $user->facebook_url) }}" placeholder="https://facebook.com/username">
                  @error('facebook_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                <label for="twitter_url" class="col-sm-3 col-form-label"><i class="fab fa-twitter mr-1 text-info"></i> {{ __('Twitter / X') }}</label>
                <div class="col-sm-9">
                  <input type="url" name="twitter_url" id="twitter_url" class="form-control @error('twitter_url') is-invalid @enderror" value="{{ old('twitter_url', $user->twitter_url) }}" placeholder="https://x.com/username">
                  @error('twitter_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                <label for="github_url" class="col-sm-3 col-form-label"><i class="fab fa-github mr-1 text-dark"></i> {{ __('GitHub') }}</label>
                <div class="col-sm-9">
                  <input type="url" name="github_url" id="github_url" class="form-control @error('github_url') is-invalid @enderror" value="{{ old('github_url', $user->github_url) }}" placeholder="https://github.com/username">
                  @error('github_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                <label for="instagram_url" class="col-sm-3 col-form-label"><i class="fab fa-instagram mr-1 text-danger"></i> {{ __('Instagram') }}</label>
                <div class="col-sm-9">
                  <input type="url" name="instagram_url" id="instagram_url" class="form-control @error('instagram_url') is-invalid @enderror" value="{{ old('instagram_url', $user->instagram_url) }}" placeholder="https://instagram.com/username">
                  @error('instagram_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
            <div class="card-footer text-right bg-whitesmoke">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
              </button>
            </div>
          </form>
        </div>

        {{-- Card 2: Dedicated Password Update Card --}}
        <div class="card">
          <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Update Password') }}</h4>
              <div class="card-header-action">
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-generate-password" title="{{ __('Generate a strong random password') }}">
                  <i class="fas fa-magic mr-1"></i> {{ __('Generate Password') }}
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group row">
                <label for="current_password" class="col-sm-4 col-form-label">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="#current_password" title="{{ __('Show/Hide Password') }}">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                  @error('current_password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                <label for="password" class="col-sm-4 col-form-label">{{ __('New Password') }} <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="#password" title="{{ __('Show/Hide Password') }}">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-outline-secondary" type="button" id="btn-copy-password" title="{{ __('Copy Password') }}">
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                  </div>
                  <small class="form-text text-muted">
                    {{ __('Minimum 8 characters. Click "Generate Password" above to create a secure one instantly.') }}
                  </small>
                  @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                <label for="password_confirmation" class="col-sm-4 col-form-label">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="#password_confirmation" title="{{ __('Show/Hide Password') }}">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right bg-whitesmoke">
              <button type="submit" class="btn btn-warning">
                <i class="fas fa-key mr-1"></i> {{ __('Update Password') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

