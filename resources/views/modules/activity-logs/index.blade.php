@extends('layouts.app')

@section('title', __('Activity Logs'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This activity log entry will be permanently deleted!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    $(document).on('click', '#btnSubmitClear', function(e) {
      e.preventDefault();
      var form = $('#formClearLogs');
      var scopeText = $('#selectScope option:selected').text();
      swal({
        title: @json(__('Confirm Clear Activity Logs')),
        text: @json(__('Are you sure you want to clear: ')) + scopeText + '?',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Failed')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Activity Logs') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('System & Logs') }}</div>
      <div class="breadcrumb-item">{{ __('Activity Logs') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Activities') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total']) }}
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
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-shopping-bag"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Order Changes') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['orders']) }}
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
              <h4>{{ __('Roles / Permissions / Settings') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['rbac_settings']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-filter mr-1"></i> {{ __('Filter Activity Logs') }}</h4>
        <div class="card-header-action">
          @can('activity-logs.delete')
            <form id="formClearLogs" action="{{ route('activity-logs.clear') }}" method="POST" class="form-inline">
              @csrf
              @method('DELETE')
              <div class="input-group input-group-sm">
                <select name="scope" id="selectScope" class="form-control form-control-sm">
                  <option value="30_days">{{ __('Older than 30 days') }}</option>
                  <option value="7_days">{{ __('Older than 7 days') }}</option>
                  <option value="all">{{ __('All Activity Logs') }}</option>
                </select>
                <div class="input-group-append">
                  <button type="button" id="btnSubmitClear" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash mr-1"></i> {{ __('Clear Logs') }}
                  </button>
                </div>
              </div>
            </form>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('activity-logs.index') }}" method="GET">
          <div class="row">
            <div class="col-md-4 mb-2">
              <label class="small font-weight-bold">{{ __('Search') }}</label>
              <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}"
                placeholder="{{ __('Description, subject, user, IP...') }}">
            </div>
            <div class="col-md-2 mb-2">
              <label class="small font-weight-bold">{{ __('Module') }}</label>
              <select name="module" class="form-control">
                <option value="">{{ __('All Modules') }}</option>
                @foreach ($availableModules as $mod)
                  <option value="{{ $mod }}" {{ $filters['module'] === $mod ? 'selected' : '' }}>
                    {{ ucfirst($mod) }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 mb-2">
              <label class="small font-weight-bold">{{ __('Action') }}</label>
              <select name="action" class="form-control">
                <option value="">{{ __('All Actions') }}</option>
                @foreach ($availableActions as $act)
                  <option value="{{ $act }}" {{ $filters['action'] === $act ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $act)) }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 mb-2">
              <label class="small font-weight-bold">{{ __('From Date') }}</label>
              <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
            </div>
            <div class="col-md-2 mb-2">
              <label class="small font-weight-bold">{{ __('To Date') }}</label>
              <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
            </div>
          </div>
          <div class="mt-2">
            <button type="submit" class="btn btn-primary btn-sm">
              <i class="fas fa-search mr-1"></i> {{ __('Filter') }}
            </button>
            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-sync-alt mr-1"></i> {{ __('Reset') }}
            </a>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h4>{{ __('Activity Log List') }}</h4>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-md mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('Time') }}</th>
                <th>{{ __('Actor') }}</th>
                <th>{{ __('Module') }}</th>
                <th>{{ __('Action') }}</th>
                <th>{{ __('Subject') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Before -> After') }}</th>
                <th class="text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($activityLogs as $log)
                @php
                  $moduleBadge = match ($log->module) {
                      'orders' => 'badge-warning',
                      'roles' => 'badge-primary',
                      'permissions' => 'badge-info',
                      'settings' => 'badge-dark',
                      default => 'badge-secondary',
                  };
                  $actionBadge = match (true) {
                      str_contains($log->action, 'delete') || $log->action === 'reset' => 'badge-danger',
                      str_contains($log->action, 'create') || str_contains($log->action, 'duplicat') => 'badge-success',
                      default => 'badge-primary',
                  };
                  $diffCount = count($log->diffRows());
                @endphp
                <tr>
                  <td>{{ $log->id }}</td>
                  <td class="text-nowrap">
                    <div>{{ $log->created_at?->format('d M Y') }}</div>
                    <small class="text-muted">{{ $log->created_at?->format('H:i:s') }}</small>
                  </td>
                  <td>
                    @if ($log->user)
                      <div class="font-weight-bold">{{ $log->user->name }}</div>
                      <small class="text-muted">{{ $log->user->email }}</small>
                    @else
                      <span class="text-muted">{{ __('System / Guest') }}</span>
                    @endif
                  </td>
                  <td><span class="badge {{ $moduleBadge }}">{{ strtoupper($log->module) }}</span></td>
                  <td><span class="badge {{ $actionBadge }}">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
                  <td>
                    @if ($log->subject_label)
                      <span class="font-weight-bold">{{ $log->subject_label }}</span>
                    @elseif ($log->subject_id)
                      <span>#{{ $log->subject_id }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>{{ $log->description }}</td>
                  <td>
                    <span class="badge badge-light border">
                      {{ $diffCount }} {{ __('field(s)') }}
                    </span>
                  </td>
                  <td class="text-right text-nowrap">
                    <a href="{{ route('activity-logs.show', $log) }}" class="btn btn-info btn-sm" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('activity-logs.delete')
                      <form action="{{ route('activity-logs.destroy', $log) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete" title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-4 text-muted">
                    {{ __('No activity logs recorded yet.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if ($activityLogs->hasPages())
        <div class="card-footer">
          {{ $activityLogs->links() }}
        </div>
      @endif
    </div>
  </div>
@endsection
