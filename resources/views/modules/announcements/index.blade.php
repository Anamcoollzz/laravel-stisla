@extends('layouts.app')

@section('title', __('Announcements'))

@push('css')
  @if ($announcements->isNotEmpty())
    <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
  @endif
@endpush

@push('js')
  @if ($announcements->isNotEmpty())
    <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
  @endif
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if ($announcements->isNotEmpty())
      $("#table-announcements").dataTable({
        "columnDefs": [
          { "sortable": false, "targets": [6] }
        ]
      });
    @endif

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this announcement!')),
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
    <h1>{{ __('Announcements') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Announcements') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Announcements') }}</h2>
    <p class="section-lead">
      {{ __('Manage all announcements and notifications.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Announcement List') }}</h4>
            <div class="card-header-action">
              @can('announcements.create')
                <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            @if ($announcements->isEmpty())
              <div class="text-center text-muted py-4">
                {{ __('No announcements found.') }}
              </div>
            @else
              <div class="table-responsive">
                <table class="table table-striped" id="table-announcements">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th>{{ __('Title') }}</th>
                      <th>{{ __('Type') }}</th>
                      <th>{{ __('Schedule') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th>{{ __('Author') }}</th>
                      <th>{{ __('Actions') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($announcements as $announcement)
                      @php
                        $typeBadge = match ($announcement->type) {
                            'warning' => 'badge-warning',
                            'urgent' => 'badge-danger',
                            'event' => 'badge-primary',
                            default => 'badge-info',
                        };
                      @endphp
                      <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                          <div class="font-weight-600">
                            @if ($announcement->is_pinned)
                              <i class="fas fa-thumbtack text-warning mr-1" title="{{ __('Pinned') }}"></i>
                            @endif
                            {{ $announcement->title }}
                          </div>
                          <div class="text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 60) }}</div>
                        </td>
                        <td>
                          <span class="badge {{ $typeBadge }}">{{ ucfirst($announcement->type) }}</span>
                        </td>
                        <td class="small">
                          <div>{{ __('Start') }}: {{ $announcement->published_at?->format('d M Y H:i') ?? '-' }}</div>
                          <div>{{ __('End') }}: {{ $announcement->expires_at?->format('d M Y H:i') ?? '-' }}</div>
                        </td>
                        <td>
                          @if ($announcement->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                          @else
                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                          @endif
                        </td>
                        <td>{{ $announcement->author?->name ?? '-' }}</td>
                        <td>
                          <div class="d-flex flex-wrap" style="gap: 4px;">
                            @can('announcements.view')
                              <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-info btn-sm" title="{{ __('Detail') }}">
                                <i class="fas fa-eye"></i>
                              </a>
                            @endcan
                            @can('announcements.edit')
                              <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-warning btn-sm" title="{{ __('Edit') }}">
                                <i class="fas fa-edit"></i>
                              </a>
                              <form action="{{ route('announcements.toggleActive', $announcement) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $announcement->is_active ? 'btn-secondary' : 'btn-success' }}" title="{{ $announcement->is_active ? __('Deactivate') : __('Activate') }}">
                                  <i class="fas {{ $announcement->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                              </form>
                            @endcan
                            @can('announcements.create')
                              <form action="{{ route('announcements.duplicate', $announcement) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm" title="{{ __('Duplicate') }}">
                                  <i class="fas fa-copy"></i>
                                </button>
                              </form>
                            @endcan
                            @can('announcements.delete')
                              <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-inline">
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
