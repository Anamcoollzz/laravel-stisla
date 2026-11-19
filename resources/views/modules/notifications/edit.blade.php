@extends('layouts.app')

@section('title', __('Edit Notification'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    function toggleTargetUser() {
      var isUser = $('#target_type').val() === 'user';
      $('#user-select-group').toggle(isUser);
      $('#user_id').prop('required', isUser);
    }

    $(document).on('change', '#target_type', toggleTargetUser);
    $(document).ready(toggleTargetUser);

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('notifications.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Notification') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('notifications.index') }}">{{ __('Notifications') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Notification') }}</h2>
    <p class="section-lead">
      {{ __('Update the notification details below.') }}
    </p>

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card">
          <form action="{{ route('notifications.update', $notification) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Notification Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="title"
                  name="title"
                  class="form-control @error('title') is-invalid @enderror"
                  value="{{ old('title', $notification->title) }}"
                  required=""
                >
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              @php
                $defaultTargetType = $notification->is_broadcast ? 'all' : 'user';
              @endphp

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="target_type">{{ __('Target') }} <span class="text-danger">*</span></label>
                    <select id="target_type" name="target_type" class="form-control @error('target_type') is-invalid @enderror" required="">
                      <option value="all" @selected(old('target_type', $defaultTargetType) === 'all')>{{ __('Broadcast (All Users)') }}</option>
                      <option value="user" @selected(old('target_type', $defaultTargetType) === 'user')>{{ __('Specific User') }}</option>
                    </select>
                    @error('target_type')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6" id="user-select-group" style="display: none;">
                  <div class="form-group">
                    <label for="user_id">{{ __('Recipient User') }} <span class="text-danger">*</span></label>
                    <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror">
                      <option value="">{{ __('Select User') }}</option>
                      @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('user_id', $notification->user_id) === (string) $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                      @endforeach
                    </select>
                    @error('user_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                    <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required="">
                      @foreach ($types as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $notification->type) === $value)>{{ __($label) }}</option>
                      @endforeach
                    </select>
                    @error('type')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="icon">{{ __('Icon') }}</label>
                    <input
                      type="text"
                      id="icon"
                      name="icon"
                      class="form-control @error('icon') is-invalid @enderror"
                      value="{{ old('icon', $notification->icon) }}"
                    >
                    @error('icon')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="action_url">{{ __('Action URL') }}</label>
                <input
                  type="text"
                  id="action_url"
                  name="action_url"
                  class="form-control @error('action_url') is-invalid @enderror"
                  value="{{ old('action_url', $notification->action_url) }}"
                >
                @error('action_url')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="message">{{ __('Message') }} <span class="text-danger">*</span></label>
                <textarea
                  id="message"
                  name="message"
                  rows="4"
                  class="form-control @error('message') is-invalid @enderror"
                  style="height: 120px;"
                  required=""
                >{{ old('message', $notification->message) }}</textarea>
                @error('message')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('notifications.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
