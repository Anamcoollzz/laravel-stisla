@extends('layouts.app')

@section('title', __('Shop › Blogs'))

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

    $("#table-blogs").dataTable({
      "pageLength": 10,
      "order": [[0, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [1, 7] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This blog post will be permanently deleted!')),
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
    <h1>{{ __('Blogs') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blogs.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Blogs') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Blog Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage articles and news displayed on the ecommerce storefront and blog sections.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-newspaper"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Articles') }}</h4>
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
              <h4>{{ __('Active / Published') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['active'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-secondary">
            <i class="fas fa-eye-slash"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Draft / Inactive') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['inactive'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('All Blog Articles') }}</h4>
            <div class="card-header-action">
              @can('blogs.create')
                <a href="{{ route('blogs.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus"></i> {{ __('Add Article') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover" id="table-blogs">
                <thead>
                  <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 80px;">{{ __('Image') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Author') }}</th>
                    <th>{{ __('Published Date') }}</th>
                    <th style="width: 90px;">{{ __('Status') }}</th>
                    <th style="width: 160px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($blogs as $blog)
                    <tr>
                      <td>
                        <span class="badge badge-light">#{{ $blog->order }}</span>
                      </td>
                      <td>
                        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="rounded" width="55" height="40" style="object-fit: cover;">
                      </td>
                      <td>
                        <a href="{{ route('blogs.show', $blog) }}" class="font-weight-bold text-dark">
                          {{ $blog->title }}
                        </a>
                        <div class="small text-muted font-italic">
                          /{{ $blog->slug }}
                        </div>
                      </td>
                      <td>
                        @if ($blog->category)
                          <span class="badge badge-primary">{{ $blog->category }}</span>
                        @else
                          <span class="badge badge-light">-</span>
                        @endif
                      </td>
                      <td>
                        {{ $blog->author_name ?: '-' }}
                      </td>
                      <td>
                        <span class="text-muted small">
                          <i class="far fa-calendar-alt"></i> {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                        </span>
                      </td>
                      <td>
                        @if ($blog->is_active)
                          <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Active') }}</span>
                        @else
                          <span class="badge badge-danger"><i class="fas fa-times"></i> {{ __('Inactive') }}</span>
                        @endif
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="{{ route('blogs.show', $blog) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('blogs.create')
                            <form action="{{ route('blogs.duplicate', $blog) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Duplicate') }}">
                                <i class="fas fa-copy"></i>
                              </button>
                            </form>
                          @endcan
                          @can('blogs.edit')
                            <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                          @can('blogs.delete')
                            <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="d-inline">
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

