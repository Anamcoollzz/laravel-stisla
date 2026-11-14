@extends('layouts.app')

@section('title', __('Shop › FAQs'))

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

    $("#table-faqs").dataTable({
      "pageLength": 10,
      "order": [[3, "asc"], [4, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [6] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This FAQ will be permanently deleted!')),
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
    <h1>{{ __('FAQs') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('ecommerce-faqs.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('FAQs') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Ecommerce FAQs Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage frequently asked questions displayed on the storefront FAQ page.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-question-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total FAQs') }}</h4>
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
              <h4>{{ __('Active') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['active'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-eye-slash"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Inactive') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['inactive'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="card">
      <div class="card-header">
        <h4>{{ __('All FAQs') }}</h4>
        <div class="card-header-action">
          <a href="{{ route('ecommerce.faq') }}" target="_blank" class="btn btn-outline-info btn-sm mr-1">
            <i class="fas fa-external-link-alt mr-1"></i> {{ __('View FAQ Page') }}
          </a>
          @can('ecommerce-faqs.view')
            <a href="{{ route('ecommerce-faqs.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
          @endcan
          @can('ecommerce-faqs.create')
            <button type="button" class="btn btn-outline-primary btn-sm mr-1" data-toggle="modal" data-target="#modal-import-faqs">
              <i class="fas fa-file-import mr-1"></i> {{ __('Import Excel') }}
            </button>
            <a href="{{ route('ecommerce-faqs.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> {{ __('Add FAQ') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-faqs">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th>{{ __('Question') }}</th>
                <th>{{ __('Answer') }}</th>
                <th style="width: 100px;">{{ __('Column') }}</th>
                <th style="width: 80px;">{{ __('Order') }}</th>
                <th style="width: 90px;">{{ __('Status') }}</th>
                <th style="width: 150px;" class="text-center">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($faqs as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <div class="font-weight-600">{{ $item->question }}</div>
                  </td>
                  <td>
                    <div class="text-muted" style="max-width: 360px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                      {{ $item->answer }}
                    </div>
                  </td>
                  <td>
                    @if ($item->column === 'left')
                      <span class="badge badge-info">{{ __('Left') }}</span>
                    @else
                      <span class="badge badge-primary">{{ __('Right') }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-light">{{ $item->order }}</span>
                  </td>
                  <td>
                    @if ($item->is_active)
                      <span class="badge badge-success">{{ __('Active') }}</span>
                    @else
                      <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      @can('ecommerce-faqs.view')
                        <a href="{{ route('ecommerce-faqs.show', $item) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      @endcan
                      @can('ecommerce-faqs.create')
                        <form action="{{ route('ecommerce-faqs.duplicate', $item) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Duplicate') }}">
                            <i class="fas fa-copy"></i>
                          </button>
                        </form>
                      @endcan
                      @can('ecommerce-faqs.edit')
                        <a href="{{ route('ecommerce-faqs.edit', $item) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                          <i class="fas fa-pencil-alt"></i>
                        </a>
                      @endcan
                      @can('ecommerce-faqs.delete')
                        <form action="{{ route('ecommerce-faqs.destroy', $item) }}" method="POST" class="d-inline">
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
@endsection

@can('ecommerce-faqs.create')
  @push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-import-faqs">
      <div class="modal-dialog" role="document">
        <form action="{{ route('ecommerce-faqs.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Import FAQs from Excel') }}</h5>
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
                <a href="{{ route('ecommerce-faqs.template') }}" class="btn btn-sm btn-outline-primary">
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
