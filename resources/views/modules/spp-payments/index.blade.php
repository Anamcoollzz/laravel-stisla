@extends('layouts.app')

@section('title', __('Modules › SPP Payments'))

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

    $("#table-spp-payments").dataTable({
      "columnDefs": [
        { "sortable": false, "targets": [8] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this SPP payment!')),
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
    <h1>{{ __('SPP Payments') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('spp-payments.index') }}">{{ __('Modules') }}</a></div>
      <div class="breadcrumb-item">{{ __('SPP Payments') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('SPP Payments') }}</h2>
    <p class="section-lead">
      {{ __('Manage all SPP payments data using DataTables.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('SPP Payment List') }}</h4>
            <div class="card-header-action">
              @can('spp-payments.view')
                <a href="{{ route('spp-payments.export') }}" class="btn btn-outline-success mr-1">
                  <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
                </a>
                <a href="{{ route('spp-payments.exportPdf') }}" class="btn btn-outline-danger mr-1">
                  <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                </a>
              @endcan
              @can('spp-payments.create')
                <button type="button" class="btn btn-outline-info mr-1" data-toggle="modal" data-target="#modal-import-spp-payments">
                  <i class="fas fa-file-import mr-1"></i> {{ __('Import Excel') }}
                </button>
                <a href="{{ route('spp-payments.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-spp-payments">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('Invoice Number') }}</th>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Period') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Payment Date') }}</th>
                    <th>{{ __('Payment Method') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sppPayments as $sppPayment)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>{{ $sppPayment->invoice_number }}</td>
                      <td>
                        {{ $sppPayment->student?->name ?? '-' }}
                        @if ($sppPayment->student?->schoolClass)
                          <div class="text-muted text-small">{{ $sppPayment->student->schoolClass->class_name }}</div>
                        @endif
                      </td>
                      <td>{{ $sppPayment->monthName() }} {{ $sppPayment->year }}</td>
                      <td>Rp {{ number_format($sppPayment->amount, 0, ',', '.') }}</td>
                      <td>{{ $sppPayment->payment_date?->format('Y-m-d') }}</td>
                      <td>{{ $sppPayment->payment_method === 'cash' ? __('Cash') : __('Bank Transfer') }}</td>
                      <td>
                        @if ($sppPayment->status === 'paid')
                          <span class="badge badge-success">{{ __('Paid') }}</span>
                        @else
                          <span class="badge badge-warning">{{ __('Unpaid') }}</span>
                        @endif
                      </td>
                      <td>
                        @can('spp-payments.view')
                          <a href="{{ route('spp-payments.show', $sppPayment) }}" class="btn btn-sm btn-icon btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        @endcan
                        @can('spp-payments.edit')
                          <a href="{{ route('spp-payments.edit', $sppPayment) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        @endcan
                        @can('spp-payments.create')
                          <form action="{{ route('spp-payments.duplicate', $sppPayment) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-icon btn-warning mr-1" data-toggle="tooltip" title="{{ __('Duplicate') }}"><i class="fas fa-copy"></i></button>
                          </form>
                        @endcan
                        @can('spp-payments.delete')
                          <form action="{{ route('spp-payments.destroy', $sppPayment) }}" method="POST" class="d-inline">
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

@can('spp-payments.create')
  @push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-import-spp-payments">
      <div class="modal-dialog" role="document">
        <form action="{{ route('spp-payments.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Import SPP Payments from Excel') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="alert alert-info alert-has-icon mb-3">
                <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                <div class="alert-body">
                  <div class="alert-title">{{ __('Format Template') }}</div>
                  {{ __('Please download the sample template to ensure your Excel headers and data format match.') }}
                </div>
              </div>
              <div class="mb-3">
                <a href="{{ route('spp-payments.template') }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Example Template (.xlsx)') }}
                </a>
                <a href="{{ asset('templates/spp_payments_import_example.xlsx') }}" class="btn btn-sm btn-outline-secondary ml-1" download>
                  <i class="fas fa-file-alt mr-1"></i> {{ __('Direct File') }}
                </a>
              </div>
              <div class="form-group">
                <label>{{ __('Excel File (.xlsx, .xls, .csv)') }} <span class="text-danger">*</span></label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                <small class="form-text text-muted">{{ __('Max size: 10MB.') }}</small>
              </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
              <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> {{ __('Upload & Import') }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  @endpush
@endcan

