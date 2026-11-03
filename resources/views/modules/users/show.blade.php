@extends('layouts.app')

@section('title', __('User Management › User Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('users.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('User Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('Users') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $user->name }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this user account.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('User Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="d-block">{{ __('ID') }}</label>
              <div>{{ $user->id }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Name') }}</label>
              <div>{{ $user->name }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Email') }}</label>
              <div>{{ $user->email }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Roles') }}</label>
              <div>
                @forelse ($user->roles as $role)
                  <span class="badge badge-info mr-1">{{ $role->name }}</span>
                @empty
                  <span class="badge badge-light">{{ __('No Role') }}</span>
                @endforelse
              </div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Created At') }}</label>
              <div>{{ $user->created_at?->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="form-group mb-0">
              <label class="d-block">{{ __('Updated At') }}</label>
              <div>{{ $user->updated_at?->format('Y-m-d H:i:s') }}</div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
            @can('users.edit')
              <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm btn-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
            @endcan
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Effective Permissions') }}</h4>
          </div>
          <div class="card-body">
            @php
              $allUserPermissions = $user->getAllPermissions();
            @endphp
            @if ($allUserPermissions->isEmpty())
              <p class="text-muted mb-0">{{ __('No permissions granted.') }}</p>
            @else
              <div class="d-flex flex-wrap gap-1">
                @foreach ($allUserPermissions as $perm)
                  <span class="badge badge-primary mr-1 mb-1">{{ $perm->name }}</span>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

