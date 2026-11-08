@extends('layouts.app')

@section('title', __('Role Management › Edit Role'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('change', '.select-all-module', function() {
      var module = $(this).data('module');
      $('.perm-' + module).prop('checked', $(this).prop('checked'));
    });

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
      <a href="{{ route('roles.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Role') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('Roles') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Role') }}</h2>
    <p class="section-lead">
      {{ __('Update role name and customize its module permissions.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <form action="{{ route('roles.update', $role) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Role Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group col-md-6 pl-0">
                <label for="name">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name', $role->name) }}"
                  required=""
                  @readonly($role->name === 'admin')
                >
                @if ($role->name === 'admin')
                  <small class="text-muted">{{ __('The admin role name cannot be modified.') }}</small>
                @endif
                <div class="invalid-feedback">
                  @error('name')
                    {{ $message }}
                  @else
                    {{ __('Please fill in the role name.') }}
                  @enderror
                </div>
              </div>

              <div class="form-group mb-0">
                <label class="d-block font-weight-bold mb-3">{{ __('Assign Permissions by Module') }}</label>
                @error('permissions')
                  <div class="text-danger small mb-3">{{ $message }}</div>
                @enderror

                <div class="row">
                  @php
                    $selectedPermissions = old('permissions', $rolePermissions);
                  @endphp
                  @foreach ($permissionsByModule as $moduleName => $permissions)
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                      <div class="card card-primary border shadow-none mb-0 h-100">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                          <h4 class="text-capitalize text-primary mb-0 font-weight-bold">{{ __(ucfirst(str_replace('-', ' ', $moduleName))) }}</h4>
                          <div class="custom-control custom-checkbox">
                            <input
                              type="checkbox"
                              class="custom-control-input select-all-module"
                              id="select_all_{{ $moduleName }}"
                              data-module="{{ $moduleName }}"
                            >
                            <label class="custom-control-label small" for="select_all_{{ $moduleName }}">{{ __('All') }}</label>
                          </div>
                        </div>
                        <div class="card-body py-2">
                          @foreach ($permissions as $permission)
                            <div class="custom-control custom-checkbox my-1">
                              <input
                                type="checkbox"
                                class="custom-control-input perm-{{ $moduleName }}"
                                id="perm_{{ $permission->id }}"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                @checked(in_array($permission->name, $selectedPermissions))
                              >
                              <label class="custom-control-label" for="perm_{{ $permission->id }}">
                                {{ $permission->name }}
                              </label>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('roles.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

