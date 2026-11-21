@extends('layouts.app')

@section('title', __('IP Blacklist'))

@section('content')
  <div class="section-header">
    <h1>{{ __('IP Blacklist') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('User Management') }}</a></div>
      <div class="breadcrumb-item">{{ __('IP Blacklist') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('IP Blacklist & Access Control') }}</h2>
    <p class="section-lead">
      {{ __('Block suspicious IP addresses from accessing the application. Your current IP: :ip', ['ip' => $currentIp]) }}
    </p>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-list"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Listed') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-ban"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Active Bans') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['active']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-secondary">
            <i class="fas fa-pause-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Inactive / Expired') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['inactive']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Blocked Requests') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['hits']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4>{{ __('Blacklisted IP Addresses') }}</h4>
        @can('ip-blacklists.create')
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalAddIpBlacklist">
            <i class="fas fa-plus mr-1"></i> {{ __('Block IP Address') }}
          </button>
        @endcan
      </div>

      <div class="card-body">
        <form method="GET" action="{{ route('ip-blacklists.index') }}" class="row mb-4">
          <div class="col-lg-5 col-md-6 mb-2">
            <input type="text" name="search" class="form-control" placeholder="{{ __('Search IP address or reason...') }}" value="{{ $filters['search'] }}">
          </div>
          <div class="col-lg-4 col-md-4 mb-2">
            <select name="status" class="form-control">
              <option value="">{{ __('All Status') }}</option>
              <option value="active" @selected($filters['status'] === 'active')>{{ __('Active') }}</option>
              <option value="inactive" @selected($filters['status'] === 'inactive')>{{ __('Inactive') }}</option>
              <option value="expired" @selected($filters['status'] === 'expired')>{{ __('Expired') }}</option>
            </select>
          </div>
          <div class="col-lg-3 col-md-2 mb-2 d-flex">
            <button type="submit" class="btn btn-primary mr-1 flex-fill">
              <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
            </button>
            @if ($filters['search'] !== '' || $filters['status'] !== '')
              <a href="{{ route('ip-blacklists.index') }}" class="btn btn-secondary" title="{{ __('Reset') }}">
                <i class="fas fa-times"></i>
              </a>
            @endif
          </div>
        </form>

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('IP Address') }}</th>
                <th>{{ __('Reason') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Expires At') }}</th>
                <th>{{ __('Blocked Hits') }}</th>
                <th>{{ __('Added By') }}</th>
                <th class="text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($blacklists as $item)
                <tr>
                  <td><code>{{ $item->ip_address }}</code></td>
                  <td>{{ $item->reason ?: '-' }}</td>
                  <td>
                    @if (! $item->is_active)
                      <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                    @elseif ($item->expires_at && $item->expires_at->isPast())
                      <span class="badge badge-warning">{{ __('Expired') }}</span>
                    @else
                      <span class="badge badge-danger">{{ __('Active') }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($item->expires_at)
                      <div>{{ $item->expires_at->format('d M Y, H:i') }}</div>
                      <div class="text-muted small">{{ $item->expires_at->diffForHumans() }}</div>
                    @else
                      <span class="badge badge-light">{{ __('Permanent') }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="font-weight-600">{{ number_format($item->blocked_hits) }}</span>
                    @if ($item->last_hit_at)
                      <div class="text-muted small">{{ $item->last_hit_at->diffForHumans() }}</div>
                    @endif
                  </td>
                  <td>{{ $item->creator?->name ?? '-' }}</td>
                  <td class="text-right">
                    @can('ip-blacklists.edit')
                      <form action="{{ route('ip-blacklists.toggle-active', $item) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="btn btn-sm {{ $item->is_active ? 'btn-secondary' : 'btn-success' }}"
                                title="{{ $item->is_active ? __('Deactivate') : __('Activate') }}">
                          <i class="fas {{ $item->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                        </button>
                      </form>
                      <button type="button"
                              class="btn btn-sm btn-warning btn-edit-blacklist"
                              data-action="{{ route('ip-blacklists.update', $item) }}"
                              data-ip="{{ $item->ip_address }}"
                              data-reason="{{ $item->reason }}"
                              data-expires-at="{{ $item->expires_at?->format('Y-m-d\TH:i') }}"
                              data-active="{{ $item->is_active ? '1' : '0' }}"
                              title="{{ __('Edit') }}">
                        <i class="fas fa-edit"></i>
                      </button>
                    @endcan
                    @can('ip-blacklists.delete')
                      <form action="{{ route('ip-blacklists.destroy', $item) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-confirm-text="{{ __('Remove :ip from blacklist?', ['ip' => $item->ip_address]) }}"
                                title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    {{ __('No blacklisted IP addresses found.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($blacklists->hasPages())
          <div class="mt-3">
            {{ $blacklists->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('modals')
  @can('ip-blacklists.create')
    <div class="modal fade" id="modalAddIpBlacklist" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('ip-blacklists.store') }}" method="POST" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Block IP Address') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>{{ __('IP Address') }}</label>
              <input type="text" name="ip_address" class="form-control" placeholder="e.g. 192.168.1.50" required>
            </div>
            <div class="form-group">
              <label>{{ __('Reason (Optional)') }}</label>
              <input type="text" name="reason" class="form-control" placeholder="{{ __('e.g. Brute force login attempts') }}">
            </div>
            <div class="form-group">
              <label>{{ __('Expires At (Optional, leave empty for permanent)') }}</label>
              <input type="datetime-local" name="expires_at" class="form-control">
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="addIsActive" name="is_active" value="1" checked>
              <label class="custom-control-label" for="addIsActive">{{ __('Active immediately') }}</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-danger">{{ __('Block IP') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endcan

  @can('ip-blacklists.edit')
    <div class="modal fade" id="modalEditIpBlacklist" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form id="formEditIpBlacklist" action="" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Edit IP Blacklist') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>{{ __('IP Address') }}</label>
              <input type="text" name="ip_address" id="edit-ip-address" class="form-control" required>
            </div>
            <div class="form-group">
              <label>{{ __('Reason (Optional)') }}</label>
              <input type="text" name="reason" id="edit-ip-reason" class="form-control">
            </div>
            <div class="form-group">
              <label>{{ __('Expires At (Optional)') }}</label>
              <input type="datetime-local" name="expires_at" id="edit-ip-expires-at" class="form-control">
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="editIsActive" name="is_active" value="1">
              <label class="custom-control-label" for="editIsActive">{{ __('Active') }}</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endcan
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-edit-blacklist', function () {
      $('#formEditIpBlacklist').attr('action', $(this).data('action'));
      $('#edit-ip-address').val($(this).data('ip'));
      $('#edit-ip-reason').val($(this).data('reason') || '');
      $('#edit-ip-expires-at').val($(this).data('expires-at') || '');
      $('#editIsActive').prop('checked', String($(this).data('active')) === '1');
      $('#modalEditIpBlacklist').modal('show');
    });
  </script>
@endpush
