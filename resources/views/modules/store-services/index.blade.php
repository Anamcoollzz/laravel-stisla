@extends('layouts.app')

@section('title', __('Shop › Services'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
  <style>
    .service-icon-preview {
      width: 48px;
      height: 48px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #f4f6f9;
      border-radius: 8px;
      padding: 6px;
    }
    .service-icon-preview svg {
      width: 34px;
      height: 34px;
    }
    .service-icon-preview svg path {
      fill: #3a4ee5;
    }
    .service-icon-preview img {
      width: 36px;
      height: 36px;
      object-fit: contain;
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

    $("#table-services").dataTable({
      "pageLength": 10,
      "order": [[4, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [1, 6] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This service will be permanently deleted!')),
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
    <h1>{{ __('Services') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('store-services.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Services') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Store Services Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage service feature cards displayed on the storefront homepage.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-concierge-bell"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Services') }}</h4>
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
        <h4>{{ __('All Services') }}</h4>
        <div class="card-header-action">
          @can('store-services.create')
            <a href="{{ route('store-services.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> {{ __('Add Service') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-services">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 80px;">{{ __('Icon') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th style="width: 80px;">{{ __('Order') }}</th>
                <th style="width: 90px;">{{ __('Status') }}</th>
                <th style="width: 150px;" class="text-center">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($services as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <div class="service-icon-preview">
                      @if ($item->icon_image_url)
                        <img src="{{ $item->icon_image_url }}" alt="{{ $item->title }}">
                      @elseif ($item->icon_svg)
                        {!! $item->icon_svg !!}
                      @else
                        <i class="fas fa-concierge-bell text-primary"></i>
                      @endif
                    </div>
                  </td>
                  <td>
                    <div class="font-weight-600">{{ $item->title }}</div>
                  </td>
                  <td>
                    <div class="text-muted" style="max-width: 360px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                      {{ $item->description }}
                    </div>
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
                      @can('store-services.view')
                        <a href="{{ route('store-services.show', $item) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      @endcan
                      @can('store-services.edit')
                        <a href="{{ route('store-services.edit', $item) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                          <i class="fas fa-pencil-alt"></i>
                        </a>
                      @endcan
                      @can('store-services.delete')
                        <form action="{{ route('store-services.destroy', $item) }}" method="POST" class="d-inline">
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

