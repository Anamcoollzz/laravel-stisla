@extends('layouts.app')

@section('title', __('Scheduled Task Monitoring'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Scheduled Task Monitoring') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Scheduled Tasks') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Cron Jobs & Scheduler Monitoring') }}</h2>
    <p class="section-lead">
      {{ __('Inspect registered Laravel scheduled tasks, monitor cron execution history, and trigger tasks on demand.') }}
    </p>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Registered Tasks') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['registered']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-history"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Executions') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total_runs']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Successful') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['success_runs']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Failed') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['failed_runs']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4>{{ __('Registered Scheduled Tasks') }}</h4>
        @can('scheduled-tasks.create')
          <form action="{{ route('scheduled-tasks.run-due') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-play mr-1"></i> {{ __('Run Due Schedule Now') }}
            </button>
          </form>
        @endcan
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('Command / Task') }}</th>
                <th>{{ __('Cron Expression') }}</th>
                <th>{{ __('Next Run') }}</th>
                <th>{{ __('Last Run') }}</th>
                <th>{{ __('Flags') }}</th>
                <th class="text-right">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tasks as $task)
                <tr>
                  <td>
                    <code class="font-weight-bold">{{ $task['command'] }}</code>
                    @if ($task['description'] !== '-')
                      <div class="text-muted small mt-1">{{ $task['description'] }}</div>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-light font-weight-bold">{{ $task['expression'] }}</span>
                    <div class="text-muted small">{{ $task['timezone'] }}</div>
                  </td>
                  <td>
                    <div>{{ $task['next_run_human'] }}</div>
                    <div class="text-muted small">{{ $task['next_run_at'] }}</div>
                  </td>
                  <td>
                    @if ($task['last_run'])
                      @if ($task['last_run']->status === 'success')
                        <span class="badge badge-success">{{ __('Success') }}</span>
                      @elseif ($task['last_run']->status === 'failed')
                        <span class="badge badge-danger">{{ __('Failed') }}</span>
                      @else
                        <span class="badge badge-warning">{{ __('Running') }}</span>
                      @endif
                      <div class="text-muted small mt-1">
                        {{ $task['last_run']->started_at?->diffForHumans() }}
                        @if ($task['last_run']->duration_ms !== null)
                          &bull; {{ $task['last_run']->duration_ms }} ms
                        @endif
                      </div>
                    @else
                      <span class="badge badge-light">{{ __('Never Run') }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($task['without_overlapping'])
                      <span class="badge badge-info" title="withoutOverlapping">{{ __('No Overlap') }}</span>
                    @endif
                    @if ($task['run_in_background'])
                      <span class="badge badge-secondary" title="runInBackground">{{ __('Background') }}</span>
                    @endif
                    @if ($task['on_one_server'])
                      <span class="badge badge-dark" title="onOneServer">{{ __('One Server') }}</span>
                    @endif
                    @if (! $task['without_overlapping'] && ! $task['run_in_background'] && ! $task['on_one_server'])
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-right">
                    @can('scheduled-tasks.create')
                      @if (! $task['is_closure'] && $task['artisan_command'])
                        <form action="{{ route('scheduled-tasks.run-now') }}" method="POST" class="d-inline">
                          @csrf
                          <input type="hidden" name="task_key" value="{{ $task['task_key'] }}">
                          <button type="submit" class="btn btn-sm btn-success" title="{{ __('Run Now') }}">
                            <i class="fas fa-play mr-1"></i> {{ __('Run Now') }}
                          </button>
                        </form>
                      @endif
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">
                    {{ __('No scheduled tasks registered in routes/console.php.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4>{{ __('Execution History') }}</h4>
        @can('scheduled-tasks.delete')
          @if ($stats['total_runs'] > 0)
            <form action="{{ route('scheduled-tasks.clear') }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="button"
                      class="btn btn-danger btn-sm btn-delete"
                      data-confirm-title="{{ __('Clear Execution History?') }}"
                      data-confirm-text="{{ __('All recorded scheduled task execution logs will be permanently deleted!') }}">
                <i class="fas fa-trash mr-1"></i> {{ __('Clear History') }}
              </button>
            </form>
          @endif
        @endcan
      </div>

      <div class="card-body">
        <form method="GET" action="{{ route('scheduled-tasks.index') }}" class="row mb-4">
          <div class="col-lg-5 col-md-6 mb-2">
            <input type="text" name="search" class="form-control" placeholder="{{ __('Search command, description, or output...') }}" value="{{ $filters['search'] }}">
          </div>
          <div class="col-lg-2 col-md-3 mb-2">
            <select name="status" class="form-control">
              <option value="">{{ __('All Status') }}</option>
              <option value="success" @selected($filters['status'] === 'success')>{{ __('Success') }}</option>
              <option value="failed" @selected($filters['status'] === 'failed')>{{ __('Failed') }}</option>
              <option value="running" @selected($filters['status'] === 'running')>{{ __('Running') }}</option>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 mb-2">
            <select name="triggered_by" class="form-control">
              <option value="">{{ __('All Triggers') }}</option>
              <option value="scheduler" @selected($filters['triggered_by'] === 'scheduler')>{{ __('Scheduler (Cron)') }}</option>
              <option value="manual" @selected($filters['triggered_by'] === 'manual')>{{ __('Manual (UI)') }}</option>
            </select>
          </div>
          <div class="col-lg-2 col-md-12 mb-2 d-flex">
            <button type="submit" class="btn btn-primary mr-1 flex-fill">
              <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
            </button>
            @if (array_filter($filters))
              <a href="{{ route('scheduled-tasks.index') }}" class="btn btn-secondary" title="{{ __('Reset') }}">
                <i class="fas fa-times"></i>
              </a>
            @endif
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('Command') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Trigger') }}</th>
                <th>{{ __('Duration') }}</th>
                <th>{{ __('Started At') }}</th>
                <th class="text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($history as $run)
                <tr>
                  <td>
                    <code>{{ $run->command }}</code>
                    <div class="text-muted small">{{ $run->expression }}</div>
                  </td>
                  <td>
                    @if ($run->status === 'success')
                      <span class="badge badge-success">{{ __('Success') }}</span>
                    @elseif ($run->status === 'failed')
                      <span class="badge badge-danger">{{ __('Failed') }} ({{ $run->exit_code }})</span>
                    @else
                      <span class="badge badge-warning">{{ __('Running') }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($run->triggered_by === 'manual')
                      <span class="badge badge-info">{{ __('Manual') }}</span>
                      @if ($run->user)
                        <div class="text-muted small">{{ $run->user->name }}</div>
                      @endif
                    @else
                      <span class="badge badge-light">{{ __('Scheduler') }}</span>
                    @endif
                  </td>
                  <td>{{ $run->duration_ms !== null ? $run->duration_ms.' ms' : '-' }}</td>
                  <td>
                    <div>{{ $run->started_at?->diffForHumans() ?? '-' }}</div>
                    <div class="text-muted small">{{ $run->started_at?->format('d M Y, H:i:s') ?? '-' }}</div>
                  </td>
                  <td class="text-right">
                    @if ($run->output)
                      <button type="button"
                              class="btn btn-sm btn-secondary btn-view-output"
                              data-command="{{ $run->command }}"
                              data-output="{{ $run->output }}"
                              title="{{ __('View Output') }}">
                        <i class="fas fa-terminal"></i>
                      </button>
                    @endif
                    @can('scheduled-tasks.delete')
                      <form action="{{ route('scheduled-tasks.destroy', $run) }}" method="POST" class="d-inline">
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
                  <td colspan="6" class="text-center py-4 text-muted">
                    {{ __('No execution history recorded yet.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($history->hasPages())
          <div class="mt-3">
            {{ $history->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('modals')
  <div class="modal fade" id="modalTaskOutput" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="task-output-title">{{ __('Command Output') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0">
          <pre id="task-output-body" class="mb-0 p-3 bg-dark text-light" style="max-height: 60vh; overflow: auto; font-size: 13px;"></pre>
        </div>
      </div>
    </div>
  </div>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-view-output', function () {
      $('#task-output-title').text($(this).data('command'));
      $('#task-output-body').text($(this).data('output'));
      $('#modalTaskOutput').modal('show');
    });
  </script>
@endpush
