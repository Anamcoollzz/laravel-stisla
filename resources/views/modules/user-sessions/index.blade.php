@extends('layouts.app')

@section('title', __('User Session Management'))

@section('content')
  <div class="section-header">
    <h1>{{ __('User Session Management') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('User Management') }}</a></div>
      <div class="breadcrumb-item">{{ __('User Sessions') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Active Browser & Device Sessions') }}</h2>
    <p class="section-lead">
      {{ __('Monitor logged-in users, inspect device details, and force-logout suspicious or stale sessions.') }}
    </p>

    @if ($stats['driver'] !== 'database' || ! $stats['table_exists'])
      <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        {{ __('Current session driver is ":driver". Set SESSION_DRIVER=database to track all active user sessions in the database.', ['driver' => $stats['driver']]) }}
      </div>
    @endif

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-network-wired"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Sessions') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-user-check"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Authenticated') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['authenticated']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-user-secret"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Guest Sessions') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['guests']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-server"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Session Driver') }}</h4>
            </div>
            <div class="card-body">
              {{ strtoupper($stats['driver']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4>{{ __('Session List') }}</h4>
        @can('user-sessions.delete')
          <form action="{{ route('user-sessions.destroy-others') }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="button"
                    class="btn btn-danger btn-delete"
                    data-confirm-title="{{ __('Force Logout All Other Sessions?') }}"
                    data-confirm-text="{{ __('Every active user and guest session except your current session will be logged out immediately.') }}">
              <i class="fas fa-sign-out-alt mr-1"></i> {{ __('Force Logout All Others') }}
            </button>
          </form>
        @endcan
      </div>

      <div class="card-body">
        <form method="GET" action="{{ route('user-sessions.index') }}" class="row mb-4">
          <div class="col-md-4 mb-2 mb-md-0">
            <input type="text" name="search" class="form-control" placeholder="{{ __('Search user, email, IP, or browser...') }}" value="{{ $filters['search'] }}">
          </div>
          <div class="col-md-3 mb-2 mb-md-0">
            <select name="user_id" class="form-control">
              <option value="">{{ __('All Users') }}</option>
              @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected((int) $filters['user_id'] === $u->id)>
                  {{ $u->name }} ({{ $u->email }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-2 mb-md-0">
            <select name="type" class="form-control">
              <option value="all" @selected($filters['type'] === 'all')>{{ __('All Session Types') }}</option>
              <option value="authenticated" @selected($filters['type'] === 'authenticated')>{{ __('Authenticated Only') }}</option>
              <option value="guest" @selected($filters['type'] === 'guest')>{{ __('Guests Only') }}</option>
            </select>
          </div>
          <div class="col-md-2 d-flex">
            <button type="submit" class="btn btn-primary mr-1 flex-fill">
              <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
            </button>
            @if ($filters['search'] !== '' || $filters['user_id'] !== null || $filters['type'] !== 'all')
              <a href="{{ route('user-sessions.index') }}" class="btn btn-secondary" title="{{ __('Reset') }}">
                <i class="fas fa-times"></i>
              </a>
            @endif
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('User') }}</th>
                <th>{{ __('Device & Browser') }}</th>
                <th>{{ __('IP Address') }}</th>
                <th>{{ __('Last Activity') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($sessions as $session)
                <tr>
                  <td>
                    @if ($session->user_id)
                      <div class="font-weight-600">{{ $session->user_name }}</div>
                      <div class="text-muted small">{{ $session->user_email }}</div>
                    @else
                      <span class="badge badge-light">{{ __('Guest') }}</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <i class="fas {{ $session->is_mobile ? 'fa-mobile-alt' : 'fa-desktop' }} text-primary mr-2"></i>
                      <div>
                        <div class="font-weight-600">{{ $session->platform }} &mdash; {{ $session->browser }}</div>
                        <div class="text-muted small text-truncate" style="max-width: 280px;" title="{{ $session->user_agent }}">
                          {{ $session->user_agent ?: '-' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td><code>{{ $session->ip_address }}</code></td>
                  <td>
                    <div>{{ $session->last_active_human }}</div>
                    <div class="text-muted small">{{ $session->last_active_at }}</div>
                  </td>
                  <td>
                    @if ($session->is_current)
                      <span class="badge badge-success">{{ __('Current Session') }}</span>
                    @else
                      <span class="badge badge-info">{{ __('Active') }}</span>
                    @endif
                  </td>
                  <td class="text-right">
                    @can('user-sessions.delete')
                      @if (! $session->is_current)
                        <form action="{{ route('user-sessions.destroy', $session->id) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="button"
                                  class="btn btn-sm btn-danger btn-delete"
                                  data-confirm-title="{{ __('Force Logout Session?') }}"
                                  data-confirm-text="{{ __('Force logout this device (:ip)?', ['ip' => $session->ip_address]) }}"
                                  title="{{ __('Force Logout') }}">
                            <i class="fas fa-sign-out-alt mr-1"></i> {{ __('Force Logout') }}
                          </button>
                        </form>
                      @endif

                      @if ($session->user_id)
                        <form action="{{ route('user-sessions.destroy-user', $session->user_id) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="button"
                                  class="btn btn-sm btn-warning btn-delete"
                                  data-confirm-title="{{ __('Force Logout User?') }}"
                                  data-confirm-text="{{ __('Force logout all active sessions for :name?', ['name' => $session->user_name]) }}"
                                  title="{{ __('Force Logout User') }}">
                            <i class="fas fa-user-slash mr-1"></i> {{ __('Force Logout User') }}
                          </button>
                        </form>
                      @endif
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">
                    {{ __('No active sessions found.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($sessions->hasPages())
          <div class="mt-3">
            {{ $sessions->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
