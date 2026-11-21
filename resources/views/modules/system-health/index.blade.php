@extends('layouts.app')

@section('title', __('System Health Check'))

@section('content')
  <div class="section-header">
    <h1>{{ __('System Health Check') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('System Health') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Infrastructure & Runtime Diagnostics') }}</h2>
    <p class="section-lead">
      {{ __('Real-time health verification for database, cache, storage, queue, security configuration, and PHP runtime.') }}
    </p>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon {{ $report['overall_status'] === 'healthy' ? 'bg-success' : ($report['overall_status'] === 'warning' ? 'bg-warning' : 'bg-danger') }}">
            <i class="fas {{ $report['overall_status'] === 'healthy' ? 'fa-heartbeat' : ($report['overall_status'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle') }}"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Overall Status') }}</h4>
            </div>
            <div class="card-body text-capitalize">
              {{ __($report['overall_status']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Healthy Checks') }}</h4>
            </div>
            <div class="card-body">
              {{ $report['counts']['healthy'] }} / {{ $report['counts']['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-exclamation"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Warnings') }}</h4>
            </div>
            <div class="card-body">
              {{ $report['counts']['warning'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-skull-crossbones"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Critical') }}</h4>
            </div>
            <div class="card-body">
              {{ $report['counts']['critical'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h4>{{ __('Health Checks') }}</h4>
            <div class="card-header-action">
              <a href="{{ route('system-health.index', ['format' => 'json']) }}" target="_blank" class="btn btn-outline-secondary mr-1">
                <i class="fas fa-code mr-1"></i> {{ __('JSON') }}
              </a>
              <a href="{{ route('system-health.index') }}" class="btn btn-primary">
                <i class="fas fa-sync-alt mr-1"></i> {{ __('Re-run Checks') }}
              </a>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Check') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Result / Details') }}</th>
                    <th class="text-right">{{ __('Time') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($report['checks'] as $check)
                    <tr>
                      <td>
                        <div class="font-weight-600">{{ $check['name'] }}</div>
                        <div class="text-muted small">{{ $check['category'] }}</div>
                      </td>
                      <td>
                        @if ($check['status'] === 'healthy')
                          <span class="badge badge-success">{{ __('Healthy') }}</span>
                        @elseif ($check['status'] === 'warning')
                          <span class="badge badge-warning">{{ __('Warning') }}</span>
                        @else
                          <span class="badge badge-danger">{{ __('Critical') }}</span>
                        @endif
                      </td>
                      <td>
                        <div class="font-weight-600">{{ $check['value'] }}</div>
                        <div class="text-muted small">{{ $check['message'] }}</div>
                      </td>
                      <td class="text-right text-muted small">{{ $check['duration_ms'] }} ms</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer text-muted small d-flex justify-content-between">
            <span>{{ __('Checked at') }}: {{ $report['checked_at'] }}</span>
            <span>{{ __('Total check duration') }}: {{ $report['total_duration_ms'] }} ms</span>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Server & Runtime Info') }}</h4>
          </div>
          <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
              <tbody>
                <tr>
                  <th class="pl-3">{{ __('PHP Version') }}</th>
                  <td class="pr-3 text-right"><code>{{ $report['environment']['php_version'] }}</code></td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('Laravel Version') }}</th>
                  <td class="pr-3 text-right"><code>{{ $report['environment']['laravel_version'] }}</code></td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('OS / Arch') }}</th>
                  <td class="pr-3 text-right">{{ $report['environment']['os'] }}</td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('Server / SAPI') }}</th>
                  <td class="pr-3 text-right">{{ $report['environment']['server_software'] }}</td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('Memory Usage') }}</th>
                  <td class="pr-3 text-right">{{ $report['environment']['memory_usage'] }} / {{ $report['environment']['memory_limit'] }}</td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('Upload Max') }}</th>
                  <td class="pr-3 text-right">{{ $report['environment']['upload_max_filesize'] }}</td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('Post Max') }}</th>
                  <td class="pr-3 text-right">{{ $report['environment']['post_max_size'] }}</td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('Max Execution') }}</th>
                  <td class="pr-3 text-right">{{ $report['environment']['max_execution_time'] }}</td>
                </tr>
                <tr>
                  <th class="pl-3">{{ __('Timezone') }}</th>
                  <td class="pr-3 text-right"><code>{{ $report['environment']['timezone'] }}</code></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
