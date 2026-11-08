@extends('layouts.app')

@section('title', __('Access Control › Permissions'))

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

    $("#table-permissions").dataTable({
      "columnDefs": [
        { "sortable": false, "targets": [4] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, this permission will be removed from all roles!')),
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
    <h1>{{ __('Permissions') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('Access Control') }}</a></div>
      <div class="breadcrumb-item">{{ __('Permissions') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Permissions') }}</h2>
    <p class="section-lead">
      {{ __('Manage the permissions that can be assigned to roles.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Permission List') }}</h4>
            <div class="card-header-action">
              @can('permissions.create')
                <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-permissions">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('Permission Name') }}</th>
                    <th>{{ __('Module') }}</th>
                    <th>{{ __('Roles Count') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($permissions as $permission)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td><code>{{ $permission->name }}</code></td>
                      <td>{{ explode('.', $permission->name)[0] }}</td>
                      <td>
                        <span class="badge badge-primary">{{ $permission->roles_count }}</span>
                      </td>
                      <td>
                        @can('permissions.edit')
                          <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        @endcan
                        @can('permissions.delete')
                          <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-danger btn-delete" data-toggle="tooltip" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                          </form>
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
