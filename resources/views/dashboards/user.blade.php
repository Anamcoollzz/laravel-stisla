@extends('layouts.app')

@section('title', __('User Dashboard'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Dashboard') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Overview') }}</div>
    </div>
  </div>

  <div class="section-body">
    @include('partials.dashboard-announcements')

    {{-- Welcome Hero Card --}}
    <div class="row">
      <div class="col-12 mb-4">
        <div class="hero text-white shadow-sm" style="background: linear-gradient(135deg, #6777ef 0%, #3f51b5 100%); border-radius: 12px; padding: 24px 28px;">
          <div class="row align-items-center">
            <div class="col-md-8 d-flex align-items-center">
              <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle mr-3 border border-white" style="width: 64px; height: 64px; object-fit: cover;">
              <div>
                <h2 class="h4 mb-1">{{ __('Hi, :name!', ['name' => $user->name]) }}</h2>
                <p class="mb-0 opacity-75">
                  {{ $user->email }}
                  @if ($user->last_logged_in)
                    &middot; {{ __('Logged in :time', ['time' => $user->last_logged_in->diffForHumans()]) }}
                  @endif
                </p>
              </div>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
              <a href="{{ route('profile.edit') }}" class="btn btn-outline-white btn-lg btn-icon icon-left">
                <i class="far fa-user"></i> {{ __('Profile') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Personal KPI Cards --}}
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-bell"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Unread Notifications') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['unread_notifications'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-comments"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Unread Messages') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['unread_messages'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-ticket-alt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('My Tickets') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['tickets'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-shopping-bag"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('My Orders') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['orders'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      {{-- Recent Notifications --}}
      <div class="col-lg-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Recent Notifications') }}</h4>
          </div>
          <div class="card-body">
            @forelse ($recentNotifications as $notif)
              @php
                $isRead = $notif->isReadBy($user->id);
                $badgeClass = match ($notif->type) {
                    'primary' => 'badge-primary',
                    'success' => 'badge-success',
                    'warning' => 'badge-warning',
                    'danger' => 'badge-danger',
                    default => 'badge-info',
                };
              @endphp
              <a href="{{ route('notifications.read', $notif) }}" class="d-flex justify-content-between align-items-start py-2 border-bottom text-decoration-none text-dark">
                <div>
                  <div class="font-weight-600">
                    <i class="{{ $notif->icon ?: 'fas fa-bell' }} mr-1 text-primary"></i>
                    {{ $notif->title }}
                    @if (! $isRead)
                      <span class="badge badge-primary ml-1">{{ __('New') }}</span>
                    @endif
                  </div>
                  <div class="text-muted small">{{ \Illuminate\Support\Str::limit($notif->message, 90) }}</div>
                </div>
                <span class="badge {{ $badgeClass }} ml-2">{{ ucfirst($notif->type) }}</span>
              </a>
            @empty
              <div class="text-muted text-center py-4">{{ __('No notifications yet.') }}</div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- My Recent Tickets --}}
      <div class="col-lg-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('My Recent Tickets') }}</h4>
            @can('tickets.view')
              <div class="card-header-action">
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-primary">{{ __('View All') }}</a>
              </div>
            @endcan
          </div>
          <div class="card-body">
            @forelse ($recentTickets as $ticket)
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                  <div class="font-weight-600">{{ $ticket->subject ?? $ticket->title ?? ('#'.$ticket->id) }}</div>
                  <div class="text-muted small">{{ $ticket->created_at?->diffForHumans() }}</div>
                </div>
                <span class="badge badge-info">{{ ucfirst((string) ($ticket->status ?? 'open')) }}</span>
              </div>
            @empty
              <div class="text-muted text-center py-4">{{ __('No tickets found.') }}</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
