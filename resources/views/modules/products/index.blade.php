@extends('layouts.app')

@section('title', __('Modules › Products'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
  <style>
    .product-thumb {
      width: 44px;
      height: 44px;
      object-fit: cover;
      border-radius: 6px;
      background: #f8f9fa;
      border: 1px solid #e9ecef;
    }
    .color-dot {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      margin-right: 4px;
      vertical-align: middle;
      border: 1px solid rgba(0,0,0,0.15);
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

    var table = $("#table-products").DataTable({
      "pageLength": 10,
      "columnDefs": [
        { "sortable": false, "targets": [1, 7] }
      ],
      "order": [[0, "desc"]]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This action will permanently delete this product!')),
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
    <h1>{{ __('Products') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Modules') }}</a></div>
      <div class="breadcrumb-item">{{ __('Products') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Product Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage the ecommerce product catalog, pricing, inventory, categories, specifications, and vendors.') }}
    </p>

    <!-- Stats Widgets -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-box-open"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Products') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('In Stock') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['in_stock'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Low / Out of Stock') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['low_stock'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-store"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Active Products') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['active'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
      <div class="card-header">
        <h4><i class="fas fa-filter mr-1"></i> {{ __('Filter Products') }}</h4>
        <div class="card-header-action">
          <a data-collapse="#filter-collapse" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
        </div>
      </div>
      <div class="collapse show" id="filter-collapse">
        <div class="card-body py-2">
          <form action="{{ route('products.index') }}" method="GET">
            <div class="row">
              <div class="col-md-3 col-sm-6 mb-2">
                <label class="small font-weight-bold">{{ __('Category') }}</label>
                <select name="category_id" class="form-control form-control-sm">
                  <option value="">-- {{ __('All Categories') }} --</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 col-sm-6 mb-2">
                <label class="small font-weight-bold">{{ __('Brand') }}</label>
                <select name="brand_id" class="form-control form-control-sm">
                  <option value="">-- {{ __('All Brands') }} --</option>
                  @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>
                      {{ $brand->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 col-sm-6 mb-2">
                <label class="small font-weight-bold">{{ __('Vendor') }}</label>
                <select name="vendor_id" class="form-control form-control-sm">
                  <option value="">-- {{ __('All Vendors') }} --</option>
                  @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @selected(request('vendor_id') == $vendor->id)>
                      {{ $vendor->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 col-sm-6 mb-2">
                <label class="small font-weight-bold">{{ __('Stock Status') }}</label>
                <select name="stock_status" class="form-control form-control-sm">
                  <option value="">-- {{ __('All Stock') }} --</option>
                  <option value="in_stock" @selected(request('stock_status') === 'in_stock')>{{ __('In Stock (>10)') }}</option>
                  <option value="low_stock" @selected(request('stock_status') === 'low_stock')>{{ __('Low Stock (<=10)') }}</option>
                  <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>{{ __('Out of Stock (0)') }}</option>
                </select>
              </div>
              <div class="col-md-2 col-sm-6 mb-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm btn-block mr-1">
                  <i class="fas fa-search"></i> {{ __('Filter') }}
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm" title="{{ __('Reset Filter') }}">
                  <i class="fas fa-undo"></i>
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Product List') }}</h4>
            <div class="card-header-action">
              @can('products.view')
                <a href="{{ route('products.export') }}" class="btn btn-outline-success btn-sm mr-1">
                  <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
                </a>
                <a href="{{ route('products.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
                  <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                </a>
              @endcan
              @can('products.create')
                <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modal-import-products">
                  <i class="fas fa-file-import mr-1"></i> {{ __('Import Excel') }}
                </button>
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New Product') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-products">
                <thead>
                  <tr>
                    <th style="width: 40px;">#</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Brand & Vendor') }}</th>
                    <th>{{ __('Size & Color') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th class="text-center">{{ __('Stock') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($products as $product)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <img
                            src="{{ $product->thumbnail_url }}"
                            alt="{{ $product->title }}"
                            class="product-thumb mr-2"
                            onerror="this.onerror=null; this.src='{{ asset('assets/img/products/product-1-50.png') }}';"
                          >
                          <div>
                            <a href="{{ route('products.show', $product) }}" class="font-weight-bold text-dark d-block">
                              {{ $product->title }}
                            </a>
                            <small class="text-muted mr-1">SKU: <code>{{ $product->sku }}</code></small>
                            @if ($product->category)
                              <span class="badge badge-light border small mr-1">{{ $product->category->name }}</span>
                            @endif
                            @if ($product->is_trending)
                              <span class="badge badge-warning text-dark font-weight-bold mr-1" style="font-size: 10px;">Trending</span>
                            @endif
                            @if ($product->is_new)
                              <span class="badge badge-info font-weight-bold mr-1" style="font-size: 10px;">New</span>
                            @endif
                            @if ($product->is_sale)
                              <span class="badge badge-danger font-weight-bold mr-1" style="font-size: 10px;">Sale</span>
                            @endif
                            @if ($product->is_deal)
                              <span class="badge badge-primary font-weight-bold mr-1" style="font-size: 10px;">Deal</span>
                            @endif
                          </div>
                        </div>
                      </td>
                      <td>
                        <div>
                          @if ($product->brand)
                            <span class="font-weight-600 text-primary">{{ $product->brand->name }}</span>
                          @else
                            <span class="text-muted">-</span>
                          @endif
                        </div>
                        <small class="text-muted d-block text-truncate" style="max-width: 140px;" title="{{ $product->vendor?->name ?? '-' }}">
                          <i class="fas fa-truck mr-1"></i> {{ $product->vendor?->name ?? '-' }}
                        </small>
                      </td>
                      <td>
                        <div>
                          @if ($product->size)
                            <span class="badge badge-light border">{{ $product->size->name }}</span>
                          @else
                            <span class="text-muted">-</span>
                          @endif
                        </div>
                        <div class="mt-1 small">
                          @if ($product->color)
                            @if ($product->color->color_code)
                              <span class="color-dot" style="background-color: {{ $product->color->color_code }};"></span>
                            @endif
                            <span>{{ $product->color->name }}</span>
                          @endif
                        </div>
                      </td>
                      <td>
                        @if ($product->discount_percent > 0)
                          <div>
                            <span class="font-weight-bold text-danger">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
                            <span class="badge badge-danger ml-1">-{{ (int) $product->discount_percent }}%</span>
                          </div>
                          <small class="text-muted text-decoration-line-through" style="text-decoration: line-through;">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                          </small>
                        @else
                          <span class="font-weight-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($product->stock <= 0)
                          <span class="badge badge-danger">{{ __('Out of Stock') }} (0)</span>
                        @elseif ($product->stock <= 10)
                          <span class="badge badge-warning">{{ $product->stock }} ({{ __('Low') }})</span>
                        @else
                          <span class="badge badge-success">{{ $product->stock }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($product->is_active)
                          <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                          <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <div class="btn-group" role="group">
                          <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info" data-toggle="tooltip" title="{{ __('View Details') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('products.create')
                            <form action="{{ route('products.duplicate', $product) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="{{ __('Duplicate') }}">
                                <i class="fas fa-copy"></i>
                              </button>
                            </form>
                          @endcan
                          @can('products.edit')
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning" data-toggle="tooltip" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                          @can('products.delete')
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-sm btn-danger btn-delete" data-toggle="tooltip" title="{{ __('Delete') }}">
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

@can('products.create')
  @push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-import-products">
      <div class="modal-dialog" role="document">
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Import Products from Excel') }}</h5>
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
                <a href="{{ route('products.template') }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Example Template (.xlsx)') }}
                </a>
                <a href="{{ asset('templates/products_import_example.xlsx') }}" class="btn btn-sm btn-outline-secondary ml-1" download>
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

