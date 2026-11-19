@extends('layouts.app')

@section('title', __('Notifications'))

@push('css')
  @if ($notifications->isNotEmpty())
    <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
  @endif
@endpush

@push('js')
  @if ($notifications->isNotEmpty())
    <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
  @endif
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if ($notifications->isNotEmpty())
      $("#table-notifications").dataTable({
        "columnDefs": [
          { "sortable": false, "targets": [5] }
        ]
      });
    @endif

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this notification!')),
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
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Notifications') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Notifications') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Notifications') }}</h2>
    <p class="section-lead">
      {{ __('Manage notifications and broadcast messages to users.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Notification List') }}</h4>
            <div class="card-header-action">
              @can('notifications.create')
                <a href="{{ route('notifications.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            @if ($notifications->isEmpty())
              <div class="text-center text-muted py-4">
                {{ __('No notifications found.') }}
              </div>
            @else
              <div class="table-responsive">
                <table class="table table-striped" id="table-notifications">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th>{{ __('Title') }}</th>
                      <th>{{ __('Type') }}</th>
                      <th>{{ __('Target') }}</th>
                      <th>{{ __('Created At') }}</th>
                      <th>{{ __('Actions') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($notifications as $notification)
                      @php
                        $typeBadge = match ($notification->type) {
                            'primary' => 'badge-primary',
                            'success' => 'badge-success',
                            'warning' => 'badge-warning',
                            'danger' => 'badge-danger',
                            default => 'badge-info',
                        };
                      @endphp
                      <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                          <div class="font-weight-600">
                            <i class="{{ $notification->icon ?: 'fas fa-bell' }} mr-1 text-primary"></i>
                            {{ $notification->title }}
                          </div>
                          <div class="text-muted small">{{ \Illuminate\Support\Str::limit($notification->message, 70) }}</div>
                        </td>
                        <td>
                          <span class="badge {{ $typeBadge }}">{{ ucfirst($notification->type) }}</span>
                        </td>
                        <td>
                          @if ($notification->is_broadcast)
                            <span class="badge badge-primary"><i class="fas fa-broadcast-tower mr-1"></i> {{ __('Broadcast (All Users)') }}</span>
                          @else
                            <span class="badge badge-light">{{ $notification->user?->name ?? '-' }}</span>
                          @endif
                        </td>
                        <td class="small">{{ $notification->created_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                          <div class="d-flex flex-wrap" style="gap: 4px;">
                            <a href="{{ route('notifications.show', $notification) }}" class="btn btn-info btn-sm" title="{{ __('Detail') }}">
                              <i class="fas fa-eye"></i>
                            </a>
                            @can('notifications.edit')
                              <a href="{{ route('notifications.edit', $notification) }}" class="btn btn-warning btn-sm" title="{{ __('Edit') }}">
                                <i class="fas fa-edit"></i>
                              </a>
                            @endcan
                            @can('notifications.delete')
                              <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-delete" title="{{ __('Delete') }}">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </form>
                            @endcan
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
