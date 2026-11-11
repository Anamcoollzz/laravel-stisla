@extends('layouts.app')

@section('title', __('Shop › Testimonials'))

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

    $("#table-testimonials").dataTable({
      "pageLength": 10,
      "order": [[4, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [1, 3, 6] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This testimonial will be permanently deleted!')),
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
    <h1>{{ __('Testimonials') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('testimonials.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Testimonials') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Testimonials Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage client reviews and testimonials displayed on the homepage and about page.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-quote-left"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Testimonials') }}</h4>
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
        <h4>{{ __('All Testimonials') }}</h4>
        <div class="card-header-action">
          @can('testimonials.create')
            <a href="{{ route('testimonials.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> {{ __('Add Testimonial') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-testimonials">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 70px;">{{ __('Photo') }}</th>
                <th>{{ __('Client Details') }}</th>
                <th>{{ __('Review') }}</th>
                <th style="width: 70px;">{{ __('Order') }}</th>
                <th style="width: 90px;">{{ __('Status') }}</th>
                <th style="width: 170px;" class="text-center">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($testimonials as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <img alt="{{ $item->name }}" src="{{ $item->image_url }}" class="rounded-circle mr-1" width="45" height="45" style="object-fit: cover;">
                  </td>
                  <td>
                    <div class="font-weight-600">{{ $item->name }}</div>
                    <div class="text-muted small">{{ $item->designation ?: __('Customer') }}</div>
                    <div class="text-warning small mt-1">
                      @for ($i = 1; $i <= 5; $i++)
                        <i class="{{ $i <= $item->rating ? 'fas' : 'far' }} fa-star"></i>
                      @endfor
                      <span class="text-muted ml-1">({{ $item->rating }}/5)</span>
                    </div>
                  </td>
                  <td>
                    <div class="text-muted" style="max-width: 320px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                      "{{ $item->content }}"
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
                      @can('testimonials.view')
                        <a href="{{ route('testimonials.show', $item) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      @endcan
                      @can('testimonials.create')
                        <form action="{{ route('testimonials.duplicate', $item) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-light" title="{{ __('Duplicate') }}">
                            <i class="fas fa-copy"></i>
                          </button>
                        </form>
                      @endcan
                      @can('testimonials.edit')
                        <a href="{{ route('testimonials.edit', $item) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                          <i class="fas fa-pencil-alt"></i>
                        </a>
                      @endcan
                      @can('testimonials.delete')
                        <form action="{{ route('testimonials.destroy', $item) }}" method="POST" class="d-inline">
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

