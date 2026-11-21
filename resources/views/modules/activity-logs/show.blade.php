@extends('layouts.app')

@section('title', __('Activity Log Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('activity-logs.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Activity Log') }} #{{ $activityLog->id }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">{{ __('Activity Logs') }}</a></div>
      <div class="breadcrumb-item">#{{ $activityLog->id }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-md-5">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Activity Metadata') }}</h4>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped mb-0">
              <tbody>
                <tr>
                  <th style="width: 35%;">{{ __('ID') }}</th>
                  <td>#{{ $activityLog->id }}</td>
                </tr>
                <tr>
                  <th>{{ __('Module') }}</th>
                  <td><span class="badge badge-primary">{{ strtoupper($activityLog->module) }}</span></td>
                </tr>
                <tr>
                  <th>{{ __('Action') }}</th>
                  <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $activityLog->action)) }}</span></td>
                </tr>
                <tr>
                  <th>{{ __('Subject') }}</th>
                  <td>
                    {{ $activityLog->subject_label ?? '-' }}
                    @if ($activityLog->subject_type)
                      <div><small class="text-muted">{{ class_basename($activityLog->subject_type) }} ({{ $activityLog->subject_id ?? '-' }})</small></div>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Description') }}</th>
                  <td>{{ $activityLog->description }}</td>
                </tr>
                <tr>
                  <th>{{ __('Actor') }}</th>
                  <td>
                    @if ($activityLog->user)
                      <div class="font-weight-bold">{{ $activityLog->user->name }}</div>
                      <small class="text-muted">{{ $activityLog->user->email }}</small>
                    @else
                      <span class="text-muted">{{ __('System / Guest') }}</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('IP Address') }}</th>
                  <td><code>{{ $activityLog->ip ?? '-' }}</code></td>
                </tr>
                <tr>
                  <th>{{ __('User Agent') }}</th>
                  <td><small class="text-muted">{{ $activityLog->user_agent ?? '-' }}</small></td>
                </tr>
                <tr>
                  <th>{{ __('Timestamp') }}</th>
                  <td>{{ $activityLog->created_at?->format('d M Y, H:i:s') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-7">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-exchange-alt mr-1"></i> {{ __('Changes (Before vs After)') }}</h4>
          </div>
          <div class="card-body p-0">
            @php
              $diffRows = $activityLog->diffRows();
            @endphp
            @if (count($diffRows) > 0)
              <div class="table-responsive">
                <table class="table table-bordered mb-0">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 24%;">{{ __('Field') }}</th>
                      <th style="width: 38%;">{{ __('Before') }}</th>
                      <th style="width: 38%;">{{ __('After') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($diffRows as $row)
                      @php
                        $formatVal = static function ($val): string {
                            if ($val === null) {
                                return 'null';
                            }
                            if (is_bool($val)) {
                                return $val ? 'true' : 'false';
                            }
                            if (is_array($val)) {
                                return json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '[]';
                            }

                            return (string) $val;
                        };
                        $beforeStr = $formatVal($row['before']);
                        $afterStr = $formatVal($row['after']);
                      @endphp
                      <tr>
                        <td class="font-weight-bold"><code>{{ $row['field'] }}</code></td>
                        <td class="bg-light">
                          @if ($row['before'] === null)
                            <span class="text-muted font-italic">null</span>
                          @elseif (is_array($row['before']))
                            <pre class="mb-0 small text-danger" style="white-space: pre-wrap;">{{ $beforeStr }}</pre>
                          @else
                            <span class="text-danger">{{ $beforeStr }}</span>
                          @endif
                        </td>
                        <td>
                          @if ($row['after'] === null)
                            <span class="text-muted font-italic">null</span>
                          @elseif (is_array($row['after']))
                            <pre class="mb-0 small text-success" style="white-space: pre-wrap;">{{ $afterStr }}</pre>
                          @else
                            <span class="text-success font-weight-bold">{{ $afterStr }}</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="p-4 text-center text-muted">
                {{ __('No before/after payload recorded for this activity.') }}
              </div>
            @endif
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
