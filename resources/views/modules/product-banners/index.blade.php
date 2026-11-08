@extends('layouts.app')

@section('title', __('Shop › Banners'))

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

    $("#table-banners").dataTable({
      "pageLength": 10,
      "columnDefs": [
        { "sortable": false, "targets": [1, 9] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This banner will be deleted!')),
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
    <h1>{{ __('Product Banners') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-banners.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Banners') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Banner Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage hero sliders, promotional banners, and marketing highlights.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-images"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Banners') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Active Banners') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['active'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-sliders-h"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Hero Sliders') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['hero'] }}
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
            <h4>{{ __('Banner List') }}</h4>
            <div class="card-header-action">
              @can('product-banners.view')
                <a href="{{ route('product-banners.export') }}" class="btn btn-outline-success btn-sm mr-1">
                  <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
                </a>
                <a href="{{ route('product-banners.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
                  <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                </a>
              @endcan
              @can('product-banners.create')
                <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modal-import-banners">
                  <i class="fas fa-file-import mr-1"></i> {{ __('Import Excel') }}
                </button>
                <a href="{{ route('product-banners.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add New') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-banners">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th style="width: 100px;">{{ __('Preview') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Badge') }}</th>
                    <th>{{ __('Subtitle') }}</th>
                    <th>{{ __('Button') }}</th>
                    <th class="text-center">{{ __('Position') }}</th>
                    <th class="text-center" style="width: 60px;">{{ __('Order') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($banners as $banner)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="img-thumbnail rounded" style="width: 90px; height: 50px; object-fit: cover;">
                      </td>
                      <td>
                        <a href="{{ route('product-banners.show', $banner) }}" class="font-weight-bold">
                          {!! strip_tags($banner->title, '<br>') !!}
                        </a>
                      </td>
                      <td>
                        @if ($banner->badge_label)
                          <span class="badge badge-danger font-weight-bold">{!! strip_tags($banner->badge_label, '<br>') !!}</span>
                        @else
                          <span class="text-muted">&mdash;</span>
                        @endif
                      </td>
                      <td>
                        <small class="text-muted d-inline-block text-truncate" style="max-width: 180px;" title="{{ $banner->subtitle }}">
                          {{ $banner->subtitle ?: '—' }}
                        </small>
                      </td>
                      <td>
                        @if ($banner->button_text)
                          <a href="{{ $banner->button_url ?: '#' }}" target="_blank" class="badge badge-light border">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ $banner->button_text }}
                          </a>
                        @else
                          <span class="text-muted">&mdash;</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <span class="badge badge-info">{{ ucfirst($banner->position) }}</span>
                      </td>
                      <td class="text-center font-weight-bold">
                        {{ $banner->order }}
                      </td>
                      <td class="text-center">
                        @if ($banner->is_active)
                          <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                          <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <div class="btn-group">
                          <a href="{{ route('product-banners.show', $banner) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('product-banners.create')
                            <form action="{{ route('product-banners.duplicate', $banner) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Duplicate') }}">
                                <i class="fas fa-copy"></i>
                              </button>
                            </form>
                          @endcan
                          @can('product-banners.edit')
                            <a href="{{ route('product-banners.edit', $banner) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                          @can('product-banners.delete')
                            <form action="{{ route('product-banners.destroy', $banner) }}" method="POST" class="d-inline">
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

@can('product-banners.create')
  @push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-import-banners">
      <div class="modal-dialog" role="document">
        <form action="{{ route('product-banners.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Import Banners from Excel') }}</h5>
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
                <a href="{{ route('product-banners.template') }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Example Template (.xlsx)') }}
                </a>
              </div>
              <div class="form-group">
                <label>{{ __('Excel File (.xlsx, .xls, .csv)') }} <span class="text-danger">*</span></label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                <small class="form-text text-muted">{{ __('Max size: 5MB.') }}</small>
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

