@extends('layouts.app')

@section('title', __('Modules › Menu Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('menus.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Menu Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('menus.index') }}">{{ __('Menu Management') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __($menu->name) }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this sidebar menu item.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Menu Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('ID') }}</label>
                <div class="font-weight-600">#{{ $menu->id }}</div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Menu Name') }}</label>
                <div class="font-weight-600">{{ __($menu->name) }}</div>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Menu Type') }}</label>
                <div>
                  <span class="badge {{ $menu->typeBadgeClass() }}">
                    {{ __(ucfirst($menu->type)) }}
                  </span>
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Icon') }}</label>
                <div>
                  @if ($menu->icon)
                    <i class="{{ $menu->icon }} text-primary mr-2"></i>
                    <code>{{ $menu->icon }}</code>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Parent Menu') }}</label>
                <div>
                  @if ($menu->parent)
                    <a href="{{ route('menus.show', $menu->parent) }}" class="font-weight-600">
                      {{ __($menu->parent->name) }}
                    </a>
                  @else
                    <span class="text-muted">{{ __('No Parent (Top Level)') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Status') }}</label>
                <div>
                  @if ($menu->is_active)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                  @else
                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Route') }}</label>
                <div>
                  @if ($menu->route)
                    <code>{{ $menu->route }}</code>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Target URL') }}</label>
                <div>
                  <a href="{{ $menu->resolvedUrl() }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 250px;">
                    {{ $menu->resolvedUrl() }} <i class="fas fa-external-link-alt ml-1 small"></i>
                  </a>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Permission') }}</label>
                <div>
                  @if ($menu->permission)
                    <span class="badge badge-warning text-dark">{{ $menu->permission }}</span>
                  @else
                    <span class="badge badge-light text-muted">{{ __('Public / None') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Order') }}</label>
                <div class="font-weight-bold">{{ $menu->order }}</div>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-6 mb-0">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Created At') }}</label>
                <div class="text-muted">{{ $menu->created_at?->format('Y-m-d H:i:s') }}</div>
              </div>
              <div class="form-group col-md-6 mb-0">
                <label class="d-block text-muted small text-uppercase font-weight-bold">{{ __('Updated At') }}</label>
                <div class="text-muted">{{ $menu->updated_at?->format('Y-m-d H:i:s') }}</div>
              </div>
            </div>

            @if ($menu->children->isNotEmpty())
              <hr>
              <h6 class="font-weight-bold text-uppercase text-muted mb-3" style="font-size: 11px; letter-spacing: 0.5px;">
                {{ __('Submenu Items') }} ({{ $menu->children->count() }})
              </h6>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th style="width: 50px;">{{ __('Order') }}</th>
                      <th>{{ __('Name') }}</th>
                      <th>{{ __('Route') }}</th>
                      <th>{{ __('Permission') }}</th>
                      <th class="text-center" style="width: 70px;">{{ __('Action') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($menu->children as $child)
                      <tr>
                        <td><span class="badge badge-light">{{ $child->order }}</span></td>
                        <td>
                          @if ($child->icon)
                            <i class="{{ $child->icon }} mr-1 text-primary"></i>
                          @endif
                          {{ __($child->name) }}
                        </td>
                        <td><code>{{ $child->route ?: $child->url }}</code></td>
                        <td>
                          @if ($child->permission)
                            <span class="badge badge-warning text-dark">{{ $child->permission }}</span>
                          @else
                            <span class="text-muted">-</span>
                          @endif
                        </td>
                        <td class="text-center">
                          <a href="{{ route('menus.show', $child) }}" class="btn btn-sm btn-icon btn-info" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('menus.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
            @can('menus.edit')
              <a href="{{ route('menus.edit', $menu) }}" class="btn btn-primary btn-sm btn-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

