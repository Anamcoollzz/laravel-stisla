@extends('layouts.app')

@section('title', __('Modules › Teachers'))

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

    $("#table-teachers").dataTable({
      "columnDefs": [
        { "sortable": false, "targets": [6] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this teacher!')),
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
    <h1>{{ __('Teachers') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('index.html') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('teachers.index') }}">{{ __('Modules') }}</a></div>
      <div class="breadcrumb-item">{{ __('Teachers') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Teachers') }}</h2>
    <p class="section-lead">
      {{ __('Manage all teachers data using DataTables.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Teacher List') }}</h4>
            <div class="card-header-action">
              <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                {{ __('Add New') }}
              </a>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-teachers">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('NIP') }}</th>
                    <th>{{ __('Teacher Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Gender') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($teachers as $teacher)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>{{ $teacher->nip }}</td>
                      <td>{{ $teacher->name }}</td>
                      <td>{{ $teacher->email }}</td>
                      <td>{{ $teacher->phone ?? '-' }}</td>
                      <td>
                        @if ($teacher->gender === 'L')
                          <span class="badge badge-primary">{{ __('Male') }}</span>
                        @else
                          <span class="badge badge-info">{{ __('Female') }}</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-icon btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-icon btn-danger btn-delete" data-toggle="tooltip" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                        </form>
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

