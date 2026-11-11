@extends('layouts.app')

@section('title', __('Shop › Product Reviews'))

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

    $("#table-reviews").dataTable({
      "pageLength": 10,
      "order": [[0, "desc"]],
      "columnDefs": [
        { "sortable": false, "targets": [1, 2, 4, 6] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This review will be permanently deleted!')),
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
    <h1>{{ __('Product Reviews') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Product Reviews') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Review Moderation') }}</h2>
    <p class="section-lead">
      {{ __('Manage customer ratings and reviews across all store products.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-star-half-alt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Reviews') }}</h4>
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
              <h4>{{ __('Approved') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['approved'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Pending') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['pending'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-award"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Avg Rating') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['average_rating'] }} / 5.0
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Pills -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="card mb-0">
          <div class="card-body py-2">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('product-reviews.index') }}">
                  {{ __('All Reviews') }} <span class="badge {{ !request('status') ? 'badge-white' : 'badge-primary' }}">{{ $stats['total'] }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('product-reviews.index', ['status' => 'approved']) }}">
                  {{ __('Approved') }} <span class="badge {{ request('status') === 'approved' ? 'badge-white' : 'badge-success' }}">{{ $stats['approved'] }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('product-reviews.index', ['status' => 'pending']) }}">
                  {{ __('Pending') }} <span class="badge {{ request('status') === 'pending' ? 'badge-white' : 'badge-warning' }}">{{ $stats['pending'] }}</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Customer Reviews') }}</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-reviews">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 220px;">{{ __('Reviewer') }}</th>
                <th style="width: 200px;">{{ __('Product') }}</th>
                <th style="width: 130px;">{{ __('Rating') }}</th>
                <th>{{ __('Comment') }}</th>
                <th style="width: 100px;">{{ __('Status') }}</th>
                <th style="width: 130px;" class="text-center">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($reviews as $review)
                <tr>
                  <td>{{ $review->id }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="{{ $review->avatar_url }}" alt="{{ $review->name }}" class="rounded-circle mr-2" width="40" height="40" style="object-fit: cover;">
                      <div>
                        <div class="font-weight-600">{{ $review->name }}</div>
                        <div class="text-muted small">{{ $review->email }}</div>
                        @if ($review->user_id)
                          <span class="badge badge-light border small text-primary" style="font-size: 10px;">
                            <i class="fas fa-user-check"></i> {{ __('Member') }}
                          </span>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    @if ($review->product)
                      <div class="d-flex align-items-center">
                        <img src="{{ $review->product->thumbnail_url }}" alt="{{ $review->product->title }}" class="rounded mr-2" width="36" height="36" style="object-fit: cover;">
                        <div>
                          <a href="{{ route('ecommerce.products.detail', $review->product->slug) }}" target="_blank" class="font-weight-600 text-dark" title="{{ $review->product->title }}">
                            {{ Str::limit($review->product->title, 25) }}
                          </a>
                          <div class="text-muted small">Rp {{ number_format($review->product->final_price, 0, ',', '.') }}</div>
                        </div>
                      </div>
                    @else
                      <span class="text-muted font-italic">{{ __('Deleted Product') }}</span>
                    @endif
                  </td>
                  <td>
                    <div class="text-warning small">
                      @for ($i = 1; $i <= 5; $i++)
                        <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                      @endfor
                      <span class="font-weight-bold ml-1 text-dark">{{ $review->rating }}.0</span>
                    </div>
                    <div class="text-muted small">{{ $review->created_at?->diffForHumans() }}</div>
                  </td>
                  <td>
                    <div class="text-muted" style="max-width: 260px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                      {{ $review->comment }}
                    </div>
                  </td>
                  <td>
                    @if ($review->is_approved)
                      <span class="badge badge-success">{{ __('Approved') }}</span>
                    @else
                      <span class="badge badge-warning">{{ __('Pending') }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      @can('product-reviews.view')
                        <a href="{{ route('product-reviews.show', $review) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      @endcan

                      @can('product-reviews.edit')
                        <form action="{{ route('product-reviews.toggleApproval', $review) }}" method="POST" class="d-inline">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="btn btn-sm {{ $review->is_approved ? 'btn-warning' : 'btn-success' }}" title="{{ $review->is_approved ? __('Unapprove') : __('Approve') }}">
                            <i class="fas {{ $review->is_approved ? 'fa-times' : 'fa-check' }}"></i>
                          </button>
                        </form>
                      @endcan

                      @can('product-reviews.delete')
                        <form action="{{ route('product-reviews.destroy', $review) }}" method="POST" class="d-inline">
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

