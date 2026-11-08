@extends('layouts.app')

@section('title', __('Modules › Menu Management'))

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

    $("#table-menus").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [2, 8] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this menu!')),
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
    <h1>{{ __('Menu Management') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('menus.index') }}">{{ __('Access Control') }}</a></div>
      <div class="breadcrumb-item">{{ __('Menu Management') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Menu Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage sidebar navigation menus, headers, dropdowns, routes, icons, and permissions.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Menu List') }}</h4>
            @can('menus.create')
              <div class="card-header-action">
                <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              </div>
            @endcan
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-menus">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th style="width: 60px;">{{ __('Order') }}</th>
                    <th class="text-center" style="width: 50px;">{{ __('Icon') }}</th>
                    <th>{{ __('Menu Name') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Route / URL') }}</th>
                    <th>{{ __('Permission') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($menus as $menu)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        <span class="badge badge-light font-weight-bold">{{ $menu->order }}</span>
                      </td>
                      <td class="text-center">
                        @if ($menu->icon)
                          <i class="{{ $menu->icon }} text-primary" style="font-size: 16px;"></i>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        @if ($menu->parent_id)
                          <span class="text-muted mr-1">└─</span>
                          <strong>{{ __($menu->name) }}</strong>
                          <span class="badge badge-light text-muted small ml-1">({{ __($menu->parent?->name) }})</span>
                        @else
                          <strong>{{ __($menu->name) }}</strong>
                        @endif
                      </td>
                      <td>
                        <span class="badge {{ $menu->typeBadgeClass() }}">
                          {{ __(ucfirst($menu->type)) }}
                        </span>
                      </td>
                      <td>
                        @if ($menu->route)
                          <code>{{ $menu->route }}</code>
                        @elseif ($menu->url)
                          <span class="text-muted">{{ $menu->url }}</span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        @if ($menu->permission)
                          <span class="badge badge-warning text-dark">{{ $menu->permission }}</span>
                        @else
                          <span class="badge badge-light text-muted">{{ __('Public') }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($menu->is_active)
                          <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                          <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                        @endif
                      </td>
                      <td class="text-center text-nowrap">
                        <a href="{{ route('menus.show', $menu) }}" class="btn btn-sm btn-icon btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        @can('menus.edit')
                          <a href="{{ route('menus.edit', $menu) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        @endcan
                        @can('menus.delete')
                          <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline form-delete">
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

