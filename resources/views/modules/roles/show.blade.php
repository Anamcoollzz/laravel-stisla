@extends('layouts.app')

@section('title', __('Role Management › Role Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('roles.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Role Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('Roles') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $role->name }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this role and assigned permissions.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-4">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Role Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="d-block">{{ __('ID') }}</label>
              <div>{{ $role->id }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Role Name') }}</label>
              <div class="font-weight-bold">{{ $role->name }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Guard Name') }}</label>
              <div>{{ $role->guard_name }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Total Users Assigned') }}</label>
              <div><span class="badge badge-info">{{ $role->users->count() }}</span></div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Created At') }}</label>
              <div>{{ $role->created_at?->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="form-group mb-0">
              <label class="d-block">{{ __('Updated At') }}</label>
              <div>{{ $role->updated_at?->format('Y-m-d H:i:s') }}</div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
            @can('roles.edit')
              <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm btn-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
            @endcan
          </div>
        </div>
      </div>

      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Assigned Permissions') }} ({{ $role->permissions->count() }})</h4>
          </div>
          <div class="card-body">
            @if ($role->permissions->isEmpty())
              <p class="text-muted mb-0">{{ __('No permissions assigned to this role.') }}</p>
            @else
              @php
                $groupedPermissions = $role->permissions->groupBy(function ($permission) {
                    return explode('.', $permission->name)[0] ?? 'general';
                });
              @endphp
              <div class="row">
                @foreach ($groupedPermissions as $moduleName => $permissions)
                  <div class="col-12 col-md-6 mb-3">
                    <div class="border rounded p-2">
                      <h6 class="text-capitalize text-primary mb-2 font-weight-bold">{{ __(ucfirst(str_replace('-', ' ', $moduleName))) }}</h6>
                      <div class="d-flex flex-wrap gap-1">
                        @foreach ($permissions as $permission)
                          <span class="badge badge-light border mr-1 mb-1">{{ $permission->name }}</span>
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

