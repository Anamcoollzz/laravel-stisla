@extends('layouts.app')

@section('title', __('Database Backup'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $("#table-backups").dataTable({
      "columnDefs": [{
        "sortable": false,
        "targets": [4]
      }],
      "order": [[3, "desc"]]
    });

    $(document).on('click', '.btn-delete-backup', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this backup file!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    $(document).on('click', '#btn-create-backup', function(e) {
      e.preventDefault();
      var form = $('#form-create-backup');
      swal({
        title: @json(__('Create Database Backup?')),
        text: @json(__('A snapshot dump of your database will be generated and stored on the server.')),
        icon: 'info',
        buttons: [@json(__('Cancel')), @json(__('Yes, Create Backup!'))],
      }).then((willCreate) => {
        if (willCreate) {
          $('#btn-create-backup').attr('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> ' + @json(__('Creating Backup...')));
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
    <h1>{{ __('Database Backup') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('Access Control') }}</a></div>
      <div class="breadcrumb-item">{{ __('Database Backup') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Database Backup Manager') }}</h2>
    <p class="section-lead">
      {{ __('Create, download, and manage database snapshots securely.') }}
    </p>

    <!-- Stats Widgets -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-database"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Database Driver') }}</h4>
            </div>
            <div class="card-body">
              {{ $driver }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-archive"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Backups') }}</h4>
            </div>
            <div class="card-body">
              {{ $totalCount }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-hdd"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Size') }}</h4>
            </div>
            <div class="card-body">
              {{ $totalSize }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Latest Backup') }}</h4>
            </div>
            <div class="card-body" style="font-size: 15px; line-height: 24px;">
              {{ $latestBackup ? ($latestBackup['created_at'] ?? $latestBackup['modified_at'])->diffForHumans() : __('None') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Backup History') }}</h4>
            <div class="card-header-action">
              @can('backup.create')
                <form id="form-create-backup" action="{{ route('backups.create') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="button" id="btn-create-backup" class="btn btn-primary btn-icon icon-left">
                    <i class="fas fa-plus"></i> {{ __('Create Backup') }}
                  </button>
                </form>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="alert alert-light alert-has-icon mb-4">
              <div class="alert-icon"><i class="fas fa-info-circle text-primary"></i></div>
              <div class="alert-body">
                <div class="alert-title">{{ __('Automated & CLI Backups') }}</div>
                {{ __('You can also generate backups from the terminal or schedule them using:') }}
                <code class="ml-1 font-weight-bold">php artisan db:backup</code>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-striped" id="table-backups">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>{{ __('File Name') }}</th>
                    <th>{{ __('File Size') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($backups as $backup)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        <i class="fas fa-file-code text-primary mr-2"></i>
                        <span class="font-weight-600">{{ $backup['name'] }}</span>
                      </td>
                      <td>
                        <span class="badge badge-light border">{{ $backup['size_formatted'] ?? $backup['formatted_size'] }}</span>
                      </td>
                      <td>
                        @php
                          $createdAt = $backup['created_at'] ?? $backup['modified_at'];
                        @endphp
                        <div>{{ $createdAt->format('d M Y, H:i:s') }}</div>
                        <small class="text-muted">{{ $createdAt->diffForHumans() }}</small>
                      </td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center">
                          @can('backup.download')
                            <a href="{{ route('backups.download', $backup['name']) }}"
                               class="btn btn-sm btn-info mr-1"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="{{ __('Download') }}">
                              <i class="fas fa-download"></i>
                            </a>
                          @endcan
                          @can('backup.delete')
                            <form action="{{ route('backups.destroy', $backup['name']) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="button"
                                      class="btn btn-sm btn-danger btn-delete-backup"
                                      data-toggle="tooltip"
                                      data-placement="top"
                                      title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                              </button>
                            </form>
                          @endcan
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        {{ __('No backups found. Click "Create Backup" to generate your first backup.') }}
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

