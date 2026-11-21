@extends('layouts.app')

@section('title', __('Login History'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Login History') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('User Management') }}</a></div>
      <div class="breadcrumb-item">{{ __('Login History') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Authentication & Login History') }}</h2>
    <p class="section-lead">
      {{ __('Track successful logins, failed attempts, account lockouts, and logout events across devices.') }}
    </p>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-history"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Events') }}</h4>
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
            <i class="fas fa-sign-in-alt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Successful Logins') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['success']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Failed / Blocked') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['failed']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-calendar-day"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Today') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['today']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4>{{ __('Login Events') }}</h4>
        @can('login-histories.delete')
          @if ($stats['total'] > 0)
            <form action="{{ route('login-histories.clear') }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="button"
                      class="btn btn-danger btn-delete"
                      data-confirm-title="{{ __('Clear All Login History?') }}"
                      data-confirm-text="{{ __('All recorded login history entries will be permanently deleted!') }}">
                <i class="fas fa-trash mr-1"></i> {{ __('Clear All History') }}
              </button>
            </form>
          @endif
        @endcan
      </div>

      <div class="card-body">
        <form method="GET" action="{{ route('login-histories.index') }}" class="row mb-4">
          <div class="col-lg-4 col-md-6 mb-2">
            <input type="text" name="search" class="form-control" placeholder="{{ __('Search email, name, IP, browser...') }}" value="{{ $filters['search'] }}">
          </div>
          <div class="col-lg-2 col-md-3 mb-2">
            <select name="status" class="form-control">
              <option value="">{{ __('All Status') }}</option>
              <option value="success" @selected($filters['status'] === 'success')>{{ __('Success') }}</option>
              <option value="failed" @selected($filters['status'] === 'failed')>{{ __('Failed') }}</option>
              <option value="blocked" @selected($filters['status'] === 'blocked')>{{ __('Blocked') }}</option>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 mb-2">
            <select name="event" class="form-control">
              <option value="">{{ __('All Events') }}</option>
              @foreach ($availableEvents as $key => $label)
                <option value="{{ $key }}" @selected($filters['event'] === $key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-3 col-md-4 mb-2">
            <select name="user_id" class="form-control">
              <option value="">{{ __('All Users') }}</option>
              @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected((int) $filters['user_id'] === $u->id)>
                  {{ $u->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-3 col-md-4 mb-2">
            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}" title="{{ __('From Date') }}">
          </div>
          <div class="col-lg-3 col-md-4 mb-2">
            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}" title="{{ __('To Date') }}">
          </div>
          <div class="col-lg-2 col-md-12 mb-2 d-flex">
            <button type="submit" class="btn btn-primary mr-1 flex-fill">
              <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
            </button>
            @if (array_filter($filters))
              <a href="{{ route('login-histories.index') }}" class="btn btn-secondary" title="{{ __('Reset') }}">
                <i class="fas fa-times"></i>
              </a>
            @endif
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('User / Email') }}</th>
                <th>{{ __('Event') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Device & Browser') }}</th>
                <th>{{ __('IP Address') }}</th>
                <th>{{ __('Notes') }}</th>
                <th>{{ __('Time') }}</th>
                <th class="text-right">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($histories as $history)
                <tr>
                  <td>
                    @if ($history->user)
                      <div class="font-weight-600">{{ $history->user->name }}</div>
                      <div class="text-muted small">{{ $history->email ?: $history->user->email }}</div>
                    @else
                      <div class="font-weight-600 text-muted">{{ $history->email ?: '-' }}</div>
                      <span class="badge badge-light">{{ __('Unregistered / Guest') }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-light">{{ $availableEvents[$history->event] ?? $history->event }}</span>
                  </td>
                  <td>
                    @if ($history->status === 'success')
                      <span class="badge badge-success">{{ __('Success') }}</span>
                    @elseif ($history->status === 'blocked')
                      <span class="badge badge-warning">{{ __('Blocked') }}</span>
                    @else
                      <span class="badge badge-danger">{{ __('Failed') }}</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <i class="fas {{ $history->is_mobile ? 'fa-mobile-alt' : 'fa-desktop' }} text-primary mr-2"></i>
                      <div>
                        <div class="font-weight-600">{{ $history->platform ?: '-' }} &mdash; {{ $history->browser ?: '-' }}</div>
                        <div class="text-muted small text-truncate" style="max-width: 240px;" title="{{ $history->user_agent }}">
                          {{ $history->user_agent ?: '-' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td><code>{{ $history->ip_address ?: '-' }}</code></td>
                  <td>{{ $history->notes ?: '-' }}</td>
                  <td>
                    <div>{{ $history->logged_at?->diffForHumans() }}</div>
                    <div class="text-muted small">{{ $history->logged_at?->format('d M Y, H:i:s') }}</div>
                  </td>
                  <td class="text-right">
                    @can('login-histories.delete')
                      <form action="{{ route('login-histories.destroy', $history) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete" title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">
                    {{ __('No login history records found.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($histories->hasPages())
          <div class="mt-3">
            {{ $histories->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
