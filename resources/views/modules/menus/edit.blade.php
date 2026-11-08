@extends('layouts.app')

@section('title', __('Modules › Edit Menu'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    function updateTypeVisibility() {
      var selectedType = $('#type').val();
      if (selectedType === 'header') {
        $('#group-parent, #group-icon, #group-route, #group-url').hide();
      } else if (selectedType === 'dropdown') {
        $('#group-parent, #group-route, #group-url').hide();
        $('#group-icon').show();
      } else {
        $('#group-parent, #group-icon, #group-route, #group-url').show();
      }
    }

    $('#type').on('change', updateTypeVisibility);
    $(document).ready(updateTypeVisibility);

    $('#icon').on('input', function() {
      var iconClass = $(this).val();
      $('#icon-preview').attr('class', iconClass || 'fas fa-question');
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
      <a href="{{ route('menus.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Menu') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('menus.index') }}">{{ __('Menu Management') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Menu') }}</h2>
    <p class="section-lead">
      {{ __('Update the sidebar menu information below.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="card">
          <form action="{{ route('menus.update', $menu) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Menu Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-8">
                  <label for="name">{{ __('Menu Name') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $menu->name) }}"
                    required
                  >
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @else
                    <div class="invalid-feedback">{{ __('Please fill in the menu name.') }}</div>
                  @enderror
                </div>

                <div class="form-group col-md-4">
                  <label for="type">{{ __('Menu Type') }} <span class="text-danger">*</span></label>
                  <select
                    id="type"
                    name="type"
                    class="form-control @error('type') is-invalid @enderror"
                    required
                  >
                    <option value="item" @selected(old('type', $menu->type) === 'item')>{{ __('Item (Link)') }}</option>
                    <option value="dropdown" @selected(old('type', $menu->type) === 'dropdown')>{{ __('Dropdown (Parent)') }}</option>
                    <option value="header" @selected(old('type', $menu->type) === 'header')>{{ __('Header (Section)') }}</option>
                  </select>
                  @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group" id="group-parent">
                <label for="parent_id">{{ __('Parent Menu') }}</label>
                <select
                  id="parent_id"
                  name="parent_id"
                  class="form-control @error('parent_id') is-invalid @enderror"
                >
                  <option value="">-- {{ __('No Parent (Top Level)') }} --</option>
                  @foreach ($parentMenus as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $menu->parent_id) == $parent->id)>
                      {{ __($parent->name) }}
                    </option>
                  @endforeach
                </select>
                @error('parent_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('Select a dropdown parent if this menu belongs inside a sub-menu.') }}</small>
              </div>

              <div class="form-group" id="group-icon">
                <label for="icon">{{ __('Icon') }} (Font Awesome)</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i id="icon-preview" class="{{ old('icon', $menu->icon ?: 'fas fa-question') }}"></i>
                    </div>
                  </div>
                  <input
                    type="text"
                    id="icon"
                    name="icon"
                    class="form-control @error('icon') is-invalid @enderror"
                    value="{{ old('icon', $menu->icon) }}"
                  >
                  @error('icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <small class="form-text text-muted">{{ __('Example: fas fa-fire, fas fa-plug, fas fa-users, fas fa-book') }}</small>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6" id="group-route">
                  <label for="route">{{ __('Route') }}</label>
                  <input
                    type="text"
                    id="route"
                    name="route"
                    list="routes-list"
                    class="form-control @error('route') is-invalid @enderror"
                    value="{{ old('route', $menu->route) }}"
                  >
                  <datalist id="routes-list">
                    @foreach ($availableRoutes as $routeName)
                      <option value="{{ $routeName }}"></option>
                    @endforeach
                  </datalist>
                  @error('route')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group col-md-6" id="group-url">
                  <label for="url">{{ __('Custom URL') }}</label>
                  <input
                    type="text"
                    id="url"
                    name="url"
                    class="form-control @error('url') is-invalid @enderror"
                    value="{{ old('url', $menu->url) }}"
                  >
                  @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-8">
                  <label for="permission">{{ __('Permission') }}</label>
                  <select
                    id="permission"
                    name="permission"
                    class="form-control @error('permission') is-invalid @enderror"
                  >
                    <option value="">-- {{ __('Public / No Permission Required') }} --</option>
                    @foreach ($permissions as $perm)
                      <option value="{{ $perm }}" @selected(old('permission', $menu->permission) === $perm)>
                        {{ $perm }}
                      </option>
                    @endforeach
                  </select>
                  @error('permission')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group col-md-4">
                  <label for="order">{{ __('Order') }}</label>
                  <input
                    type="number"
                    id="order"
                    name="order"
                    class="form-control @error('order') is-invalid @enderror"
                    value="{{ old('order', $menu->order) }}"
                    min="0"
                  >
                  @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    class="custom-control-input"
                    @checked(old('is_active', $menu->is_active))
                  >
                  <label class="custom-control-label font-weight-bold" for="is_active">{{ __('Active') }}</label>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('menus.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

