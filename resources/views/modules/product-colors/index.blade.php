@extends('layouts.app')

@section('title', __('Shop › Colors'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
  <style>
    .color-swatch {
      display: inline-block;
      width: 24px;
      height: 24px;
      border-radius: 4px;
      vertical-align: middle;
      border: 1px solid rgba(0,0,0,0.15);
      margin-right: 8px;
    }
  </style>
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

    $("#table-colors").dataTable({
      "pageLength": 10,
      "columnDefs": [
        { "sortable": false, "targets": [4] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This color will be deleted!')),
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
    <h1>{{ __('Product Colors') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-colors.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Colors') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Color Palette Management') }}</h2>
    <p class="section-lead">{{ __('Manage product color variants and hex codes.') }}</p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-palette"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Colors') }}</h4>
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
            <i class="fas fa-tshirt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Associated Products') }}</h4>
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
            <h4>{{ __('Color List') }}</h4>
            <div class="card-header-action">
              @can('product-colors.view')
                <a href="{{ route('product-colors.export') }}" class="btn btn-outline-success btn-sm mr-1">
                  <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
                </a>
                <a href="{{ route('product-colors.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
                  <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                </a>
              @endcan
              @can('product-colors.create')
                <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modal-import-colors">
                  <i class="fas fa-file-import mr-1"></i> {{ __('Import Excel') }}
                </button>
                <a href="{{ route('product-colors.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-colors">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>{{ __('Color') }}</th>
                    <th>{{ __('Hex Code') }}</th>
                    <th class="text-center">{{ __('Products') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($colors as $color)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          @if ($color->color_code)
                            <span class="color-swatch" style="background-color: {{ $color->color_code }};"></span>
                          @else
                            <span class="color-swatch bg-light"></span>
                          @endif
                          <a href="{{ route('product-colors.show', $color) }}" class="font-weight-bold">
                            {{ $color->name }}
                          </a>
                        </div>
                      </td>
                      <td><code>{{ $color->color_code ?? '-' }}</code></td>
                      <td class="text-center">
                        <span class="badge badge-light border">{{ $color->products_count }}</span>
                      </td>
                      <td class="text-center">
                        <div class="btn-group">
                          <a href="{{ route('product-colors.show', $color) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('product-colors.create')
                            <form action="{{ route('product-colors.duplicate', $color) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Duplicate') }}">
                                <i class="fas fa-copy"></i>
                              </button>
                            </form>
                          @endcan
                          @can('product-colors.edit')
                            <a href="{{ route('product-colors.edit', $color) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                          @can('product-colors.delete')
                            <form action="{{ route('product-colors.destroy', $color) }}" method="POST" class="d-inline">
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

@can('product-colors.create')
  @push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-import-colors">
      <div class="modal-dialog" role="document">
        <form action="{{ route('product-colors.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Import Colors from Excel') }}</h5>
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
                <a href="{{ route('product-colors.template') }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Example Template (.xlsx)') }}
                </a>
                <a href="{{ asset('templates/product_colors_import_example.xlsx') }}" class="btn btn-sm btn-outline-secondary ml-1" download>
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

