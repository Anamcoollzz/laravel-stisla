@extends('layouts.app')

@section('title', __('Cache Management'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Cache Management') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Cache Management') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Application & Framework Cache Manager') }}</h2>
    <p class="section-lead">
      {{ __('Inspect, flush, and rebuild Laravel framework and Stisla application caches safely.') }}
    </p>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-bolt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Cache Driver') }}</h4>
            </div>
            <div class="card-body">
              {{ strtoupper($overview['driver']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-database"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('DB Cache Entries') }}</h4>
            </div>
            <div class="card-body">
              {{ $overview['db_cache_count'] !== null ? number_format($overview['db_cache_count']) : '-' }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-file-code"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Compiled Views') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($overview['compiled_views_count']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon {{ $overview['config_cached'] || $overview['routes_cached'] ? 'bg-success' : 'bg-secondary' }}">
            <i class="fas fa-cogs"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Config / Routes') }}</h4>
            </div>
            <div class="card-body" style="font-size: 14px;">
              {{ $overview['config_cached'] ? __('Config Cached') : __('Config Live') }} /
              {{ $overview['routes_cached'] ? __('Routes Cached') : __('Routes Live') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4>{{ __('Stisla Application Caches') }}</h4>
            @can('cache-management.delete')
              <form action="{{ route('cache-management.execute') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="stisla_flush">
                <button type="button"
                        class="btn btn-sm btn-danger btn-delete"
                        data-confirm-title="{{ __('Flush Stisla Caches?') }}"
                        data-confirm-text="{{ __('Sidebar menus, settings, IP blacklist, and permission caches will be cleared.') }}"
                        data-confirm-button-text="{{ __('Flush All') }}">
                  <i class="fas fa-broom mr-1"></i> {{ __('Flush All Stisla') }}
                </button>
              </form>
            @endcan
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Cache Group') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($stislaCaches as $item)
                    <tr>
                      <td>
                        <div class="font-weight-600">{{ $item['label'] }}</div>
                        <div class="text-muted small">{{ $item['description'] }}</div>
                        <code>{{ $item['key'] }}</code>
                      </td>
                      <td>
                        @if ($item['is_cached'])
                          <span class="badge badge-success">{{ __('Cached') }}</span>
                        @else
                          <span class="badge badge-light">{{ __('Empty') }}</span>
                        @endif
                      </td>
                      <td class="text-right">
                        @can('cache-management.delete')
                          <form action="{{ route('cache-management.forget-key') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="key" value="{{ $item['key'] }}">
                            <button type="submit" class="btn btn-sm btn-warning" title="{{ __('Flush Key') }}">
                              <i class="fas fa-sync-alt mr-1"></i> {{ __('Flush') }}
                            </button>
                          </form>
                        @endcan
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Laravel Optimization & Cache Actions') }}</h4>
          </div>
          <div class="card-body">
            <div class="row">
              @can('cache-management.delete')
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="optimize_clear">
                    <button type="button"
                            class="btn btn-danger btn-block btn-delete"
                            data-confirm-title="{{ __('Run optimize:clear?') }}"
                            data-confirm-text="{{ __('This clears config, route, view, event, and application caches.') }}"
                            data-confirm-button-text="{{ __('Clear All') }}">
                      <i class="fas fa-bomb mr-1"></i> {{ __('Optimize Clear (All)') }}
                    </button>
                  </form>
                </div>
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="cache_clear">
                    <button type="button"
                            class="btn btn-warning btn-block btn-delete"
                            data-confirm-title="{{ __('Flush Application Cache?') }}"
                            data-confirm-text="{{ __('All items in the default cache store will be removed.') }}"
                            data-confirm-button-text="{{ __('Flush Cache') }}">
                      <i class="fas fa-trash mr-1"></i> {{ __('Flush App Cache') }}
                    </button>
                  </form>
                </div>
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="view_clear">
                    <button type="submit" class="btn btn-info btn-block">
                      <i class="fas fa-eye-slash mr-1"></i> {{ __('Clear Compiled Views') }}
                    </button>
                  </form>
                </div>
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="permission_reset">
                    <button type="submit" class="btn btn-primary btn-block">
                      <i class="fas fa-key mr-1"></i> {{ __('Reset Permission Cache') }}
                    </button>
                  </form>
                </div>
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="config_clear">
                    <button type="submit" class="btn btn-outline-secondary btn-block">
                      <i class="fas fa-sliders-h mr-1"></i> {{ __('Clear Config Cache') }}
                    </button>
                  </form>
                </div>
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="route_clear">
                    <button type="submit" class="btn btn-outline-secondary btn-block">
                      <i class="fas fa-route mr-1"></i> {{ __('Clear Route Cache') }}
                    </button>
                  </form>
                </div>
              @endcan

              @can('cache-management.edit')
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="config_cache">
                    <button type="submit" class="btn btn-success btn-block">
                      <i class="fas fa-check-circle mr-1"></i> {{ __('Rebuild Config Cache') }}
                    </button>
                  </form>
                </div>
                <div class="col-sm-6 mb-2">
                  <form action="{{ route('cache-management.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="route_cache">
                    <button type="submit" class="btn btn-success btn-block">
                      <i class="fas fa-bolt mr-1"></i> {{ __('Rebuild Route Cache') }}
                    </button>
                  </form>
                </div>
              @endcan
            </div>

            @can('cache-management.delete')
              <hr>
              <form action="{{ route('cache-management.forget-key') }}" method="POST">
                @csrf
                <label class="font-weight-600">{{ __('Forget Specific Cache Key') }}</label>
                <div class="input-group">
                  <input type="text" name="key" class="form-control" placeholder="{{ __('e.g. stisla.settings.all') }}" required>
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-danger">
                      <i class="fas fa-eraser mr-1"></i> {{ __('Forget') }}
                    </button>
                  </div>
                </div>
              </form>
            @endcan
          </div>
        </div>
      </div>
    </div>

    @if ($overview['db_cache_exists'])
      <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
          <h4>{{ __('Database Cache Entries') }} (<code>{{ $overview['db_cache_table'] }}</code>)</h4>
          <form method="GET" action="{{ route('cache-management.index') }}" class="form-inline mt-2 mt-md-0">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="{{ __('Search cache key...') }}" value="{{ $search }}">
              <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                @if ($search !== '')
                  <a href="{{ route('cache-management.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                @endif
              </div>
            </div>
          </form>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead>
                <tr>
                  <th>{{ __('Cache Key') }}</th>
                  <th>{{ __('Expires At') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th class="text-right">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($entries as $entry)
                  <tr>
                    <td><code class="text-break">{{ $entry->key }}</code></td>
                    <td>
                      <div>{{ $entry->expires_human }}</div>
                      <div class="text-muted small">{{ $entry->expires_at }}</div>
                    </td>
                    <td>
                      @if ($entry->is_expired)
                        <span class="badge badge-warning">{{ __('Expired') }}</span>
                      @else
                        <span class="badge badge-success">{{ __('Active') }}</span>
                      @endif
                    </td>
                    <td class="text-right">
                      @can('cache-management.delete')
                        <form action="{{ route('cache-management.forget-key') }}" method="POST" class="d-inline">
                          @csrf
                          <input type="hidden" name="key" value="{{ $entry->key }}">
                          <input type="hidden" name="raw" value="1">
                          <button type="button"
                                  class="btn btn-sm btn-danger btn-delete"
                                  data-confirm-text="{{ __('Delete cache key :key?', ['key' => $entry->key]) }}"
                                  title="{{ __('Delete') }}">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      @endcan
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                      {{ __('No database cache entries found.') }}
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        @if ($entries->hasPages())
          <div class="card-footer">
            {{ $entries->links() }}
          </div>
        @endif
      </div>
    @endif
  </div>
@endsection
