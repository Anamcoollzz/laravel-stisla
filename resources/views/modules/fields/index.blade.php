@extends('layouts.app')

@section('title', __('Modules › Fields'))

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

    $("#table-fields").dataTable({
      "columnDefs": [
        { "sortable": false, "targets": [5] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this field!')),
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
    <h1>{{ __('Fields') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('fields.index') }}">{{ __('Modules') }}</a></div>
      <div class="breadcrumb-item">{{ __('Fields') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Fields') }}</h2>
    <p class="section-lead">
      {{ __('Manage all fields data using DataTables.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Field List') }}</h4>
            <div class="card-header-action">
              @can('fields.create')
                <a href="{{ route('fields.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-fields">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('Field Code') }}</th>
                    <th>{{ __('Field Name') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($fields as $field)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>{{ $field->code }}</td>
                      <td>{{ $field->name }}</td>
                      <td>{{ $field->description ?? '-' }}</td>
                      <td>{{ $field->created_at?->format('Y-m-d H:i:s') }}</td>
                      <td>
                        @can('fields.view')
                          <a href="{{ route('fields.show', $field) }}" class="btn btn-sm btn-icon btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        @endcan
                        @can('fields.edit')
                          <a href="{{ route('fields.edit', $field) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        @endcan
                        @can('fields.create')
                          <form action="{{ route('fields.duplicate', $field) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-icon btn-warning mr-1" data-toggle="tooltip" title="{{ __('Duplicate') }}"><i class="fas fa-copy"></i></button>
                          </form>
                        @endcan
                        @can('fields.delete')
                          <form action="{{ route('fields.destroy', $field) }}" method="POST" class="d-inline">
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
