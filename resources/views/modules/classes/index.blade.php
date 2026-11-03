@extends('layouts.app')

@section('title', __('Modules › Classes'))

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

    $("#table-classes").dataTable({
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
        text: @json(__('Once deleted, you will not be able to recover this class!')),
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
    <h1>{{ __('Classes') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('classes.index') }}">{{ __('Modules') }}</a></div>
      <div class="breadcrumb-item">{{ __('Classes') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Classes') }}</h2>
    <p class="section-lead">
      {{ __('Manage all school classes data using DataTables.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Class List') }}</h4>
            <div class="card-header-action">
              @can('classes.create')
                <a href="{{ route('classes.create') }}" class="btn btn-primary">
                  {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-classes">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('Class Name') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Updated At') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($classes as $schoolClass)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>{{ $schoolClass->class_name }}</td>
                      <td>{{ $schoolClass->created_at?->format('Y-m-d H:i:s') }}</td>
                      <td>{{ $schoolClass->updated_at?->format('Y-m-d H:i:s') }}</td>
                      <td>
                        @can('classes.view')
                          <a href="{{ route('classes.show', $schoolClass) }}" class="btn btn-sm btn-icon btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        @endcan
                        @can('classes.edit')
                          <a href="{{ route('classes.edit', $schoolClass) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        @endcan
                        @can('classes.delete')
                          <form action="{{ route('classes.destroy', $schoolClass) }}" method="POST" class="d-inline">
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
