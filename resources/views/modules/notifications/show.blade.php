@extends('layouts.app')

@section('title', __('Notification Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('notifications.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Notification Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('notifications.index') }}">{{ __('Notifications') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h4><i class="{{ $notification->icon ?: 'fas fa-bell' }} mr-2 text-primary"></i>{{ $notification->title }}</h4>
            @can('notifications.edit')
              <div class="card-header-action">
                <a href="{{ route('notifications.edit', $notification) }}" class="btn btn-warning">
                  <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                </a>
              </div>
            @endcan
          </div>
          <div class="card-body">
            <div class="mb-3 d-flex flex-wrap align-items-center" style="gap: 8px;">
              @php
                $typeBadge = match ($notification->type) {
                    'primary' => 'badge-primary',
                    'success' => 'badge-success',
                    'warning' => 'badge-warning',
                    'danger' => 'badge-danger',
                    default => 'badge-info',
                };
              @endphp
              <span class="badge {{ $typeBadge }}">{{ ucfirst($notification->type) }}</span>
              @if ($notification->is_broadcast)
                <span class="badge badge-primary"><i class="fas fa-broadcast-tower mr-1"></i> {{ __('Broadcast (All Users)') }}</span>
              @else
                <span class="badge badge-light">{{ $notification->user?->name ?? '-' }}</span>
              @endif
            </div>

            <p class="mb-4" style="white-space: pre-line;">{{ $notification->message }}</p>

            <table class="table table-sm table-bordered mb-0">
              <tr>
                <th style="width: 180px;">{{ __('Action URL') }}</th>
                <td>
                  @if ($notification->action_url)
                    <a href="{{ $notification->action_url }}">{{ $notification->action_url }}</a>
                  @else
                    -
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('Created By') }}</th>
                <td>{{ $notification->creator?->name ?? '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Created At') }}</th>
                <td>{{ $notification->created_at?->format('d M Y H:i') ?? '-' }}</td>
              </tr>
            </table>
          </div>
          <div class="card-footer">
            <a href="{{ route('notifications.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
