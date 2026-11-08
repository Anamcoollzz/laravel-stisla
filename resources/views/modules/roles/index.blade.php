@extends('layouts.app')

@section('title', __('Role Management › Roles'))

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

    $("#table-roles").dataTable({
      "columnDefs": [{
        "sortable": false,
        "targets": [4]
      }]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this role!')),
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
    <h1>{{ __('Roles') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('Role Management') }}</a></div>
      <div class="breadcrumb-item">{{ __('Roles') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Roles') }}</h2>
    <p class="section-lead">
      {{ __('Manage user roles and configure granular permissions per module.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Role List') }}</h4>
            <div class="card-header-action">
              @can('roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                  {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-roles">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('Role Name') }}</th>
                    <th>{{ __('Users Count') }}</th>
                    <th>{{ __('Permissions Count') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $role)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        <span class="font-weight-bold">{{ $role->name }}</span>
                        @if ($role->name === 'admin')
                          <span class="badge badge-warning ml-1">{{ __('Default') }}</span>
                        @endif
                      </td>
                      <td>
                        <span class="badge badge-light">{{ $role->users_count }}</span>
                      </td>
                      <td>
                        <span class="badge badge-primary">{{ $role->permissions_count }}</span>
                      </td>
                      <td>
                        @can('roles.view')
                          <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-icon btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        @endcan
                        @can('roles.edit')
                          <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        @endcan
                        @can('roles.delete')
                          @if ($role->name !== 'admin')
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-icon btn-danger btn-delete" data-toggle="tooltip" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                            </form>
                          @endif
                        @endcan
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

