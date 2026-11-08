@extends('layouts.app')

@section('title', __('Shop › Vendors'))

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

    $("#table-vendors").dataTable({
      "pageLength": 10,
      "columnDefs": [
        { "sortable": false, "targets": [5] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This vendor will be deleted!')),
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
    <h1>{{ __('Product Vendors') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-vendors.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Vendors') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Vendor / Supplier Management') }}</h2>
    <p class="section-lead">{{ __('Manage suppliers, wholesalers, contact details, and their supplied products.') }}</p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-truck"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Vendors') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-box-open"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Supplied Products') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total_products'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Vendor List') }}</h4>
            <div class="card-header-action">
              @can('product-vendors.view')
                <a href="{{ route('product-vendors.export') }}" class="btn btn-outline-success btn-sm mr-1">
                  <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
                </a>
                <a href="{{ route('product-vendors.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
                  <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                </a>
              @endcan
              @can('product-vendors.create')
                <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modal-import-vendors">
                  <i class="fas fa-file-import mr-1"></i> {{ __('Import Excel') }}
                </button>
                <a href="{{ route('product-vendors.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-vendors">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>{{ __('Vendor Name') }}</th>
                    <th>{{ __('Contact') }}</th>
                    <th>{{ __('Address') }}</th>
                    <th class="text-center">{{ __('Products') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($vendors as $vendor)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        <a href="{{ route('product-vendors.show', $vendor) }}" class="font-weight-bold">
                          {{ $vendor->name }}
                        </a>
                      </td>
                      <td>
                        <div class="small"><i class="fas fa-phone mr-1 text-muted"></i> {{ $vendor->phone ?? '-' }}</div>
                        <div class="small"><i class="fas fa-envelope mr-1 text-muted"></i> {{ $vendor->email ?? '-' }}</div>
                      </td>
                      <td>
                        <span class="small text-muted text-truncate d-inline-block" style="max-width: 250px;">
                          {{ $vendor->address ?? '-' }}
                        </span>
                      </td>
                      <td class="text-center">
                        <span class="badge badge-light border">{{ $vendor->products_count }}</span>
                      </td>
                      <td class="text-center">
                        <div class="btn-group">
                          <a href="{{ route('product-vendors.show', $vendor) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('product-vendors.create')
                            <form action="{{ route('product-vendors.duplicate', $vendor) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Duplicate') }}">
                                <i class="fas fa-copy"></i>
                              </button>
                            </form>
                          @endcan
                          @can('product-vendors.edit')
                            <a href="{{ route('product-vendors.edit', $vendor) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                          @can('product-vendors.delete')
                            <form action="{{ route('product-vendors.destroy', $vendor) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-sm btn-danger btn-delete" title="{{ __('Delete') }}">
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
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@can('product-vendors.create')
  @push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-import-vendors">
      <div class="modal-dialog" role="document">
        <form action="{{ route('product-vendors.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Import Vendors from Excel') }}</h5>
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
                <a href="{{ route('product-vendors.template') }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Example Template (.xlsx)') }}
                </a>
                <a href="{{ asset('templates/product_vendors_import_example.xlsx') }}" class="btn btn-sm btn-outline-secondary ml-1" download>
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

