@extends('layouts.app')

@section('title', __('API Token Management'))

@section('content')
  <div class="section-header">
    <h1>{{ __('API Token Management') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('User Management') }}</a></div>
      <div class="breadcrumb-item">{{ __('API Tokens') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Bearer API Tokens') }}</h2>
    <p class="section-lead">
      {{ __('Issue, inspect, scope, and revoke personal API bearer tokens for external integrations (use middleware api.token).') }}
    </p>

    @if (session('new_plain_token'))
      <div class="alert alert-success alert-has-icon">
        <div class="alert-icon"><i class="fas fa-key"></i></div>
        <div class="alert-body">
          <div class="alert-title">{{ __('New API Token Created') }}: {{ session('new_token_name') }}</div>
          <p class="mb-2">{{ __('Please copy your new API token now. For your security, it will not be shown again.') }}</p>
          <div class="input-group" style="max-width: 620px;">
            <input type="text" id="new-plain-token-input" class="form-control bg-white text-dark font-weight-bold" value="{{ session('new_plain_token') }}" readonly>
            <div class="input-group-append">
              <button type="button" id="btn-copy-new-token" class="btn btn-dark">
                <i class="fas fa-copy mr-1"></i> {{ __('Copy Token') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    @endif

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-key"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Tokens') }}</h4>
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
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Active Tokens') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['active']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-hourglass-end"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Expired') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['expired']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-bolt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Used Today') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['used_today']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4>{{ __('Issued API Tokens') }}</h4>
        @can('api-tokens.create')
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateApiToken">
            <i class="fas fa-plus mr-1"></i> {{ __('Create API Token') }}
          </button>
        @endcan
      </div>

      <div class="card-body">
        <form method="GET" action="{{ route('api-tokens.index') }}" class="row mb-4">
          <div class="col-lg-4 col-md-6 mb-2">
            <input type="text" name="search" class="form-control" placeholder="{{ __('Search token name, prefix, or user...') }}" value="{{ $filters['search'] }}">
          </div>
          <div class="col-lg-3 col-md-3 mb-2">
            <select name="user_id" class="form-control">
              <option value="">{{ __('All Users') }}</option>
              @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected((int) $filters['user_id'] === $u->id)>
                  {{ $u->name }} ({{ $u->email }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-3 col-md-3 mb-2">
            <select name="status" class="form-control">
              <option value="">{{ __('All Status') }}</option>
              <option value="active" @selected($filters['status'] === 'active')>{{ __('Active') }}</option>
              <option value="expired" @selected($filters['status'] === 'expired')>{{ __('Expired') }}</option>
            </select>
          </div>
          <div class="col-lg-2 col-md-12 mb-2 d-flex">
            <button type="submit" class="btn btn-primary mr-1 flex-fill">
              <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
            </button>
            @if ($filters['search'] !== '' || $filters['user_id'] !== null || $filters['status'] !== '')
              <a href="{{ route('api-tokens.index') }}" class="btn btn-secondary" title="{{ __('Reset') }}">
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
                <th>{{ __('Token Name') }}</th>
                <th>{{ __('Owner') }}</th>
                <th>{{ __('Abilities') }}</th>
                <th>{{ __('Last Used') }}</th>
                <th>{{ __('Expires At') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tokens as $token)
                <tr>
                  <td>
                    <div class="font-weight-600">{{ $token->name }}</div>
                    <code class="small text-muted">{{ $token->token_prefix }}••••••••</code>
                  </td>
                  <td>
                    @if ($token->user)
                      <div class="font-weight-600">{{ $token->user->name }}</div>
                      <div class="text-muted small">{{ $token->user->email }}</div>
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    @foreach (($token->abilities ?? ['*']) as $ability)
                      <span class="badge badge-light">{{ $ability }}</span>
                    @endforeach
                  </td>
                  <td>
                    @if ($token->last_used_at)
                      <div>{{ $token->last_used_at->diffForHumans() }}</div>
                      <div class="text-muted small">{{ $token->last_used_ip ?: '-' }}</div>
                    @else
                      <span class="text-muted">{{ __('Never') }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($token->expires_at)
                      <div>{{ $token->expires_at->format('d M Y, H:i') }}</div>
                      <div class="text-muted small">{{ $token->expires_at->diffForHumans() }}</div>
                    @else
                      <span class="badge badge-light">{{ __('Never') }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($token->isExpired())
                      <span class="badge badge-danger">{{ __('Expired') }}</span>
                    @else
                      <span class="badge badge-success">{{ __('Active') }}</span>
                    @endif
                  </td>
                  <td class="text-right">
                    @can('api-tokens.edit')
                      <button type="button"
                              class="btn btn-sm btn-warning btn-edit-api-token"
                              data-action="{{ route('api-tokens.update', $token) }}"
                              data-name="{{ $token->name }}"
                              data-abilities='@json($token->abilities ?? ["*"])'
                              data-expires-at="{{ $token->expires_at?->format('Y-m-d\TH:i') }}"
                              title="{{ __('Edit') }}">
                        <i class="fas fa-edit"></i>
                      </button>
                    @endcan
                    @can('api-tokens.delete')
                      <form action="{{ route('api-tokens.destroy', $token) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-confirm-title="{{ __('Revoke API Token?') }}"
                                data-confirm-text="{{ __('Any application using :name will immediately lose API access.', ['name' => $token->name]) }}"
                                data-confirm-button-text="{{ __('Revoke') }}"
                                title="{{ __('Revoke') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    {{ __('No API tokens found.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($tokens->hasPages())
          <div class="mt-3">
            {{ $tokens->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('modals')
  @can('api-tokens.create')
    <div class="modal fade" id="modalCreateApiToken" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('api-tokens.store') }}" method="POST" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Create API Token') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>{{ __('Token Name') }}</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Mobile App Integration" required>
            </div>
            <div class="form-group">
              <label>{{ __('Assign to User') }}</label>
              <select name="user_id" class="form-control" required>
                @foreach ($users as $u)
                  <option value="{{ $u->id }}" @selected(auth()->id() === $u->id)>
                    {{ $u->name }} ({{ $u->email }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>{{ __('Abilities / Scopes') }}</label>
              <div class="d-flex flex-wrap">
                @foreach ($availableAbilities as $ability)
                  <div class="custom-control custom-checkbox mr-3 mb-1">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="create-ability-{{ $loop->index }}"
                           name="abilities[]"
                           value="{{ $ability }}"
                           @checked($ability === '*')>
                    <label class="custom-control-label" for="create-ability-{{ $loop->index }}">
                      {{ $ability === '*' ? __('* (Full Access)') : ucfirst($ability) }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="form-group mb-0">
              <label>{{ __('Expires At (Optional)') }}</label>
              <input type="datetime-local" name="expires_at" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Generate Token') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endcan

  @can('api-tokens.edit')
    <div class="modal fade" id="modalEditApiToken" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form id="formEditApiToken" action="" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Edit API Token') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>{{ __('Token Name') }}</label>
              <input type="text" name="name" id="edit-token-name" class="form-control" required>
            </div>
            <div class="form-group">
              <label>{{ __('Abilities / Scopes') }}</label>
              <div class="d-flex flex-wrap">
                @foreach ($availableAbilities as $ability)
                  <div class="custom-control custom-checkbox mr-3 mb-1">
                    <input type="checkbox"
                           class="custom-control-input edit-ability-checkbox"
                           id="edit-ability-{{ $loop->index }}"
                           name="abilities[]"
                           value="{{ $ability }}">
                    <label class="custom-control-label" for="edit-ability-{{ $loop->index }}">
                      {{ $ability === '*' ? __('* (Full Access)') : ucfirst($ability) }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="form-group mb-0">
              <label>{{ __('Expires At (Optional)') }}</label>
              <input type="datetime-local" name="expires_at" id="edit-token-expires-at" class="form-control">
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

    $(document).on('click', '#btn-copy-new-token', function () {
      var input = document.getElementById('new-plain-token-input');
      if (!input) {
        return;
      }
      input.select();
      input.setSelectionRange(0, 99999);
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value);
      } else {
        document.execCommand('copy');
      }
      $(this).html('<i class="fas fa-check mr-1"></i> ' + @json(__('Copied!')));
    });

    $(document).on('click', '.btn-edit-api-token', function () {
      var abilities = $(this).data('abilities') || [];
      $('#formEditApiToken').attr('action', $(this).data('action'));
      $('#edit-token-name').val($(this).data('name'));
      $('#edit-token-expires-at').val($(this).data('expires-at') || '');
      $('.edit-ability-checkbox').each(function () {
        $(this).prop('checked', abilities.indexOf($(this).val()) !== -1);
      });
      $('#modalEditApiToken').modal('show');
    });
  </script>
@endpush
