@extends('layouts.app')

@section('title', __('Modules › Product Categories'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
  <style>
    .cat-icon-thumb {
      width: 28px;
      height: 28px;
      object-fit: contain;
      background: #f8f9fa;
      border-radius: 4px;
      padding: 2px;
      border: 1px solid #e9ecef;
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

    $("#table-categories").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [2, 8] }
      ],
      "ordering": false // Keep hierarchical pre-sorted order
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Deleting this category will also delete all sub-categories under it!')),
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
    <h1>{{ __('Product Categories') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-categories.index') }}">{{ __('Modules') }}</a></div>
      <div class="breadcrumb-item">{{ __('Product Categories') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Product Categories (3 Levels)') }}</h2>
    <p class="section-lead">
      {{ __('Manage the 3-level ecommerce navigation: Level 1 (Section Header), Level 2 (Main Category / Dropdown), and Level 3 (Subcategory Item).') }}
    </p>

    <!-- Stats Widgets -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-sitemap"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Categories') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-layer-group"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Level 1 (Sections)') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['level1'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-folder"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Level 2 (Categories)') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['level2'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-tag"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Level 3 (Items)') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['level3'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Category Hierarchy List') }}</h4>
            <div class="card-header-action">
              @can('product-categories.view')
                <a href="{{ route('product-categories.export') }}" class="btn btn-outline-success btn-sm mr-1">
                  <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
                </a>
                <a href="{{ route('product-categories.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
                  <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                </a>
              @endcan
              @can('product-categories.create')
                <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modal-import-categories">
                  <i class="fas fa-file-import mr-1"></i> {{ __('Import Excel') }}
                </button>
                <a href="{{ route('product-categories.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-categories">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th style="width: 50px;">{{ __('Order') }}</th>
                    <th class="text-center" style="width: 50px;">{{ __('Icon') }}</th>
                    <th>{{ __('Category Name') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Level') }}</th>
                    <th>{{ __('Parent Category') }}</th>
                    <th>{{ __('Target Route / URL') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($categories as $cat)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        <span class="badge badge-light border">{{ $cat->order }}</span>
                      </td>
                      <td class="text-center">
                        @if ($cat->icon_url)
                          <img src="{{ $cat->icon_url }}" alt="{{ $cat->name }}" class="cat-icon-thumb">
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        @if ($cat->level === 1)
                          <span class="font-weight-bold text-dark text-uppercase">
                            <i class="fas fa-heading mr-1 text-primary"></i> {{ $cat->name }}
                          </span>
                        @elseif ($cat->level === 2)
                          <span style="padding-left: 18px;">
                            <span class="text-muted mr-1">├──</span>
                            <span class="font-weight-600 text-primary">{{ $cat->name }}</span>
                            @if ($cat->isDropdown())
                              <i class="fas fa-chevron-down small text-muted ml-1" title="{{ __('Has subcategories') }}"></i>
                            @endif
                          </span>
                        @else
                          <span style="padding-left: 38px;">
                            <span class="text-muted mr-1">└──</span>
                            <span>{{ $cat->name }}</span>
                          </span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($cat->level === 1)
                          <span class="badge badge-primary">{{ __('Level 1 (Section)') }}</span>
                        @elseif ($cat->level === 2)
                          <span class="badge badge-info">{{ __('Level 2 (Category)') }}</span>
                        @else
                          <span class="badge badge-light border text-muted">{{ __('Level 3 (Item)') }}</span>
                        @endif
                      </td>
                      <td>
                        @if ($cat->parent)
                          <span class="small font-weight-600">{{ $cat->parent->name }}</span>
                          @if ($cat->parent->parent)
                            <small class="text-muted d-block">&laquo; {{ $cat->parent->parent->name }}</small>
                          @endif
                        @else
                          <span class="text-muted small">&mdash; {{ __('Root') }} &mdash;</span>
                        @endif
                      </td>
                      <td>
                        @if ($cat->route)
                          <code class="small text-primary">{{ $cat->route }}</code>
                        @elseif ($cat->url)
                          <a href="{{ $cat->url }}" target="_blank" class="small text-truncate d-inline-block" style="max-width: 150px;">{{ $cat->url }}</a>
                        @else
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($cat->is_active)
                          <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                          <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center">
                          @can('product-categories.create')
                            <form action="{{ route('product-categories.duplicate', $cat) }}" method="POST" class="d-inline mr-1">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" title="{{ __('Duplicate') }}">
                                <i class="fas fa-copy"></i>
                              </button>
                            </form>
                          @endcan
                          @can('product-categories.view')
                            <a href="{{ route('product-categories.show', $cat) }}" class="btn btn-sm btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}">
                              <i class="fas fa-eye"></i>
                            </a>
                          @endcan
                          @can('product-categories.edit')
                            <a href="{{ route('product-categories.edit', $cat) }}" class="btn btn-sm btn-warning mr-1" data-toggle="tooltip" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                          @can('product-categories.delete')
                            <form action="{{ route('product-categories.destroy', $cat) }}" method="POST" class="d-inline">
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

@can('product-categories.create')
  @push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-import-categories">
      <div class="modal-dialog" role="document">
        <form action="{{ route('product-categories.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Import Product Categories from Excel') }}</h5>
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
                <a href="{{ route('product-categories.template') }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Example Template (.xlsx)') }}
                </a>
                <a href="{{ asset('templates/product_categories_import_example.xlsx') }}" class="btn btn-sm btn-outline-secondary ml-1" download>
                  <i class="fas fa-file-alt mr-1"></i> {{ __('Direct File') }}
                </a>
              </div>
              <div class="form-group">
                <label>{{ __('Excel File (.xlsx, .xls, .csv)') }} <span class="text-danger">*</span></label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                <small class="form-text text-muted">{{ __('Max size: 10MB. Rows can reference parent categories by name or ID.') }}</small>
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

