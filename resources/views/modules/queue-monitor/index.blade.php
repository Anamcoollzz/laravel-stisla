@extends('layouts.app')

@section('title', __('Queue Monitoring'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Queue Monitoring') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Queue Monitoring') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Laravel Queue & Jobs Monitor') }}</h2>
    <p class="section-lead">
      {{ __('Monitor pending jobs, retry or flush failed jobs, inspect job batches, and track processed job history.') }}
    </p>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-plug"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Queue Driver') }}</h4>
            </div>
            <div class="card-body">
              {{ strtoupper($stats['connection']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-hourglass-half"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Pending Jobs') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['pending']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Failed Jobs') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['failed']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-double"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Processed') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['processed']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" href="{{ route('queue-monitor.index', ['tab' => 'pending']) }}">
              <i class="fas fa-hourglass-half mr-1"></i> {{ __('Pending') }}
              <span class="badge badge-light ml-1">{{ $stats['pending'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $tab === 'failed' ? 'active' : '' }}" href="{{ route('queue-monitor.index', ['tab' => 'failed']) }}">
              <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Failed') }}
              <span class="badge badge-light ml-1">{{ $stats['failed'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $tab === 'batches' ? 'active' : '' }}" href="{{ route('queue-monitor.index', ['tab' => 'batches']) }}">
              <i class="fas fa-layer-group mr-1"></i> {{ __('Batches') }}
              <span class="badge badge-light ml-1">{{ $stats['batches'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}" href="{{ route('queue-monitor.index', ['tab' => 'history']) }}">
              <i class="fas fa-history mr-1"></i> {{ __('History') }}
            </a>
          </li>
        </ul>

        <div class="card-header-action mt-2 mt-md-0">
          @can('queue-monitor.create')
            @if ($stats['pending'] > 0)
              <form action="{{ route('queue-monitor.work') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary mr-1">
                  <i class="fas fa-play mr-1"></i> {{ __('Process Pending Queue') }}
                </button>
              </form>
            @endif
          @endcan

          @if ($tab === 'pending' && $stats['pending'] > 0)
            @can('queue-monitor.delete')
              <form action="{{ route('queue-monitor.pending.clear') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-delete" data-confirm-text="{{ __('Clear all pending queue jobs?') }}">
                  <i class="fas fa-trash mr-1"></i> {{ __('Clear Pending') }}
                </button>
              </form>
            @endcan
          @elseif ($tab === 'failed' && $stats['failed'] > 0)
            @can('queue-monitor.create')
              <form action="{{ route('queue-monitor.failed.retry-all') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning mr-1">
                  <i class="fas fa-redo mr-1"></i> {{ __('Retry All') }}
                </button>
              </form>
            @endcan
            @can('queue-monitor.delete')
              <form action="{{ route('queue-monitor.failed.flush') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-delete" data-confirm-text="{{ __('Flush all failed jobs permanently?') }}">
                  <i class="fas fa-trash mr-1"></i> {{ __('Flush Failed') }}
                </button>
              </form>
            @endcan
          @elseif ($tab === 'history')
            @can('queue-monitor.delete')
              <form action="{{ route('queue-monitor.history.clear') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-delete" data-confirm-text="{{ __('Clear all queue monitor history?') }}">
                  <i class="fas fa-trash mr-1"></i> {{ __('Clear History') }}
                </button>
              </form>
            @endcan
          @endif
        </div>
      </div>

      <div class="card-body">
        @if ($tab !== 'batches')
          <form method="GET" action="{{ route('queue-monitor.index') }}" class="row mb-4">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="col-lg-6 col-md-6 mb-2">
              <input type="text" name="search" class="form-control" placeholder="{{ __('Search job name, queue, or exception...') }}" value="{{ $filters['search'] }}">
            </div>
            @if ($tab === 'history')
              <div class="col-lg-3 col-md-4 mb-2">
                <select name="status" class="form-control">
                  <option value="">{{ __('All Status') }}</option>
                  <option value="succeeded" @selected($filters['status'] === 'succeeded')>{{ __('Succeeded') }}</option>
                  <option value="failed" @selected($filters['status'] === 'failed')>{{ __('Failed') }}</option>
                  <option value="running" @selected($filters['status'] === 'running')>{{ __('Running') }}</option>
                </select>
              </div>
            @endif
            <div class="col-lg-3 col-md-2 mb-2 d-flex">
              <button type="submit" class="btn btn-primary mr-1 flex-fill">
                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
              </button>
              @if ($filters['search'] !== '' || $filters['status'] !== '')
                <a href="{{ route('queue-monitor.index', ['tab' => $tab]) }}" class="btn btn-secondary">
                  <i class="fas fa-times"></i>
                </a>
              @endif
            </div>
          </form>
        @endif

        @if ($tab === 'pending')
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead>
                <tr>
                  <th>{{ __('ID') }}</th>
                  <th>{{ __('Job Name') }}</th>
                  <th>{{ __('Queue') }}</th>
                  <th>{{ __('Attempts') }}</th>
                  <th>{{ __('State') }}</th>
                  <th>{{ __('Available At') }}</th>
                  <th class="text-right">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($pendingJobs as $job)
                  <tr>
                    <td>#{{ $job->id }}</td>
                    <td><code>{{ $job->name }}</code></td>
                    <td><span class="badge badge-light">{{ $job->queue }}</span></td>
                    <td>{{ $job->attempts }}</td>
                    <td>
                      @if ($job->is_reserved)
                        <span class="badge badge-info">{{ __('Reserved / Running') }}</span>
                      @else
                        <span class="badge badge-warning">{{ __('Queued') }}</span>
                      @endif
                    </td>
                    <td>
                      <div>{{ $job->available_at }}</div>
                      <div class="text-muted small">{{ __('Created') }} {{ $job->created_at }}</div>
                    </td>
                    <td class="text-right">
                      @can('queue-monitor.delete')
                        <form action="{{ route('queue-monitor.pending.destroy', $job->id) }}" method="POST" class="d-inline">
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
                    <td colspan="7" class="text-center py-4 text-muted">{{ __('No pending jobs in queue.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($pendingJobs->hasPages())
            <div class="mt-3">{{ $pendingJobs->links() }}</div>
          @endif

        @elseif ($tab === 'failed')
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead>
                <tr>
                  <th>{{ __('Job Name') }}</th>
                  <th>{{ __('Queue') }}</th>
                  <th>{{ __('Exception') }}</th>
                  <th>{{ __('Failed At') }}</th>
                  <th class="text-right">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($failedJobs as $job)
                  <tr>
                    <td>
                      <code>{{ $job->name }}</code>
                      <div class="text-muted small">{{ $job->uuid }}</div>
                    </td>
                    <td><span class="badge badge-light">{{ $job->queue }}</span></td>
                    <td class="text-danger small">{{ $job->exception_summary }}</td>
                    <td>
                      <div>{{ $job->failed_human }}</div>
                      <div class="text-muted small">{{ $job->failed_at }}</div>
                    </td>
                    <td class="text-right">
                      <button type="button"
                              class="btn btn-sm btn-secondary btn-view-exception"
                              data-title="{{ $job->name }}"
                              data-exception="{{ $job->exception }}"
                              title="{{ __('View Exception') }}">
                        <i class="fas fa-bug"></i>
                      </button>
                      @can('queue-monitor.create')
                        <form action="{{ route('queue-monitor.failed.retry', $job->uuid) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-warning" title="{{ __('Retry') }}">
                            <i class="fas fa-redo"></i>
                          </button>
                        </form>
                      @endcan
                      @can('queue-monitor.delete')
                        <form action="{{ route('queue-monitor.failed.forget', $job->uuid) }}" method="POST" class="d-inline">
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
                    <td colspan="5" class="text-center py-4 text-muted">{{ __('No failed jobs found.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($failedJobs->hasPages())
            <div class="mt-3">{{ $failedJobs->links() }}</div>
          @endif

        @elseif ($tab === 'batches')
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead>
                <tr>
                  <th>{{ __('Batch Name') }}</th>
                  <th>{{ __('Progress') }}</th>
                  <th>{{ __('Jobs (Total / Pending / Failed)') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th>{{ __('Created') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($batches as $batch)
                  <tr>
                    <td>
                      <div class="font-weight-600">{{ $batch->name }}</div>
                      <code class="small">{{ $batch->id }}</code>
                    </td>
                    <td style="min-width: 160px;">
                      <div class="progress" style="height: 8px;">
                        <div class="progress-bar {{ $batch->failed_jobs > 0 ? 'bg-danger' : 'bg-success' }}" style="width: {{ $batch->progress }}%"></div>
                      </div>
                      <small class="text-muted">{{ $batch->progress }}%</small>
                    </td>
                    <td>{{ $batch->total_jobs }} / {{ $batch->pending_jobs }} / <span class="text-danger">{{ $batch->failed_jobs }}</span></td>
                    <td>
                      @if ($batch->cancelled)
                        <span class="badge badge-secondary">{{ __('Cancelled') }}</span>
                      @elseif ($batch->finished)
                        <span class="badge badge-success">{{ __('Finished') }}</span>
                      @else
                        <span class="badge badge-info">{{ __('Processing') }}</span>
                      @endif
                    </td>
                    <td>{{ $batch->created_at }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted">{{ __('No job batches found.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($batches->hasPages())
            <div class="mt-3">{{ $batches->links() }}</div>
          @endif

        @else
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead>
                <tr>
                  <th>{{ __('Job Name') }}</th>
                  <th>{{ __('Queue') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th>{{ __('Attempts') }}</th>
                  <th>{{ __('Duration') }}</th>
                  <th>{{ __('Started At') }}</th>
                  <th class="text-right">{{ __('Exception') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($histories as $history)
                  <tr>
                    <td><code>{{ $history->job_name }}</code></td>
                    <td><span class="badge badge-light">{{ $history->queue }}</span></td>
                    <td>
                      @if ($history->status === 'succeeded')
                        <span class="badge badge-success">{{ __('Succeeded') }}</span>
                      @elseif ($history->status === 'failed')
                        <span class="badge badge-danger">{{ __('Failed') }}</span>
                      @else
                        <span class="badge badge-warning">{{ __('Running') }}</span>
                      @endif
                    </td>
                    <td>{{ $history->attempts }}</td>
                    <td>{{ $history->duration_ms !== null ? $history->duration_ms.' ms' : '-' }}</td>
                    <td>
                      <div>{{ $history->started_at?->diffForHumans() ?? '-' }}</div>
                      <div class="text-muted small">{{ $history->started_at?->format('d M Y, H:i:s') ?? '-' }}</div>
                    </td>
                    <td class="text-right">
                      @if ($history->exception)
                        <button type="button"
                                class="btn btn-sm btn-secondary btn-view-exception"
                                data-title="{{ $history->job_name }}"
                                data-exception="{{ $history->exception }}">
                          <i class="fas fa-bug"></i>
                        </button>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">{{ __('No processed job history recorded yet.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($histories->hasPages())
            <div class="mt-3">{{ $histories->links() }}</div>
          @endif
        @endif
      </div>
    </div>
  </div>
@endsection

@push('modals')
  <div class="modal fade" id="modalJobException" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="job-exception-title">{{ __('Job Exception') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0">
          <pre id="job-exception-body" class="mb-0 p-3 bg-dark text-light" style="max-height: 60vh; overflow: auto; font-size: 12px;"></pre>
        </div>
      </div>
    </div>
  </div>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-view-exception', function () {
      $('#job-exception-title').text($(this).data('title'));
      $('#job-exception-body').text($(this).data('exception'));
      $('#modalJobException').modal('show');
    });
  </script>
@endpush
