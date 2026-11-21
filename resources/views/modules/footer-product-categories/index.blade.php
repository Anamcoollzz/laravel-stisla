@extends('layouts.app')

@section('title', __('Shop › Footer Categories'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $("#table-footer-categories").dataTable({
      "pageLength": 10,
      "order": [[4, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [6] }
      ]
    });

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Footer Categories') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('footer-product-categories.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Footer Categories') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Footer Product Categories Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage product categories displayed in the storefront footer.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-tags"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Items') }}</h4>
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
        <h4>{{ __('All Footer Categories') }}</h4>
        <div class="card-header-action">
          @can('footer-product-categories.create')
            <a href="{{ route('footer-product-categories.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> {{ __('Add Footer Category') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-footer-categories">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th>{{ __('Display Label') }}</th>
                <th>{{ __('Product Category') }}</th>
                <th>{{ __('Slug') }}</th>
                <th style="width: 80px;">{{ __('Order') }}</th>
                <th style="width: 90px;">{{ __('Status') }}</th>
                <th style="width: 150px;" class="text-center">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($footerCategories as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td class="font-weight-600">{{ $item->display_name }}</td>
                  <td>
                    @if ($item->category)
                      <span>{{ $item->category->name }}</span>
                      @if ($item->category->parent)
                        <small class="text-muted">({{ $item->category->parent->name }})</small>
                      @endif
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td><code>{{ $item->category?->slug ?? '-' }}</code></td>
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
                      @can('footer-product-categories.view')
                        <a href="{{ route('footer-product-categories.show', $item) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      @endcan
                      @can('footer-product-categories.edit')
                        <a href="{{ route('footer-product-categories.edit', $item) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                          <i class="fas fa-pencil-alt"></i>
                        </a>
                      @endcan
                      @can('footer-product-categories.delete')
                        <form action="{{ route('footer-product-categories.destroy', $item) }}" method="POST" class="d-inline">
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
