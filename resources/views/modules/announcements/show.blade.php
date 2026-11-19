@extends('layouts.app')

@section('title', __('Announcement Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('announcements.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Announcement Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('announcements.index') }}">{{ __('Announcements') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ $announcement->title }}</h4>
            <div class="card-header-action">
              @can('announcements.edit')
                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-warning">
                  <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="mb-3 d-flex flex-wrap align-items-center" style="gap: 8px;">
              @php
                $typeBadge = match ($announcement->type) {
                    'warning' => 'badge-warning',
                    'urgent' => 'badge-danger',
                    'event' => 'badge-primary',
                    default => 'badge-info',
                };
              @endphp
              <span class="badge {{ $typeBadge }}">{{ ucfirst($announcement->type) }}</span>
              @if ($announcement->is_active)
                <span class="badge badge-success">{{ __('Active') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
              @endif
              @if ($announcement->is_pinned)
                <span class="badge badge-warning"><i class="fas fa-thumbtack mr-1"></i> {{ __('Pinned') }}</span>
              @endif
            </div>

            <div class="mb-4" style="white-space: pre-line;">{{ $announcement->content }}</div>

            <table class="table table-sm table-bordered mb-0">
              <tr>
                <th style="width: 180px;">{{ __('Slug') }}</th>
                <td><code>{{ $announcement->slug }}</code></td>
              </tr>
              <tr>
                <th>{{ __('Published At') }}</th>
                <td>{{ $announcement->published_at?->format('d M Y H:i') ?? '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Expires At') }}</th>
                <td>{{ $announcement->expires_at?->format('d M Y H:i') ?? '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Author') }}</th>
                <td>{{ $announcement->author?->name ?? '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Created At') }}</th>
                <td>{{ $announcement->created_at?->format('d M Y H:i') ?? '-' }}</td>
              </tr>
            </table>
          </div>
          <div class="card-footer">
            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
