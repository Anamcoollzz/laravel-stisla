@extends('layouts.app')

@section('title', __('Shop › Blog Comments'))

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

    $("#table-comments").dataTable({
      "pageLength": 10,
      "order": [[0, "desc"]],
      "columnDefs": [
        { "sortable": false, "targets": [1, 3, 6] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This comment will be permanently deleted!')),
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
    <h1>{{ __('Blog Comments') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blogs.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Blog Comments') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Comment Moderation') }}</h2>
    <p class="section-lead">
      {{ __('Manage reader comments and replies across all store articles.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-comments"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Comments') }}</h4>
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
              <h4>{{ __('Approved') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['approved'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Pending / Hidden') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['pending'] }}
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
            <h4>{{ __('All Comments') }}</h4>
            <div class="card-header-action">
              <div class="btn-group" role="group">
                <a href="{{ route('blog-comments.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                  {{ __('All') }}
                </a>
                <a href="{{ route('blog-comments.index', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                  {{ __('Approved') }}
                </a>
                <a href="{{ route('blog-comments.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                  {{ __('Pending') }}
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover" id="table-comments">
                <thead>
                  <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 200px;">{{ __('Commenter') }}</th>
                    <th>{{ __('Article') }}</th>
                    <th>{{ __('Comment') }}</th>
                    <th style="width: 110px;">{{ __('Status') }}</th>
                    <th style="width: 140px;">{{ __('Date') }}</th>
                    <th style="width: 120px;" class="text-center">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($comments as $comment)
                    <tr>
                      <td>{{ $comment->id }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="{{ $comment->avatar_url }}" alt="{{ $comment->name }}" class="rounded-circle mr-2" width="35" height="35" style="object-fit: cover;">
                          <div>
                            <div class="font-weight-bold text-dark">
                              {{ $comment->name }}
                              @if ($comment->user_id)
                                <span class="badge badge-primary py-0 px-1 ml-1" style="font-size: 10px;" title="{{ __('Registered Member') }}"><i class="fas fa-user-check"></i> Member</span>
                              @endif
                            </div>
                            <div class="small text-muted">{{ $comment->email }}</div>
                          </div>
                        </div>
                      </td>
                      <td>
                        @if ($comment->blog)
                          <a href="{{ route('blogs.show', $comment->blog) }}" class="font-weight-bold">
                            {{ Str::limit($comment->blog->title, 35) }}
                          </a>
                          <div>
                            <a href="{{ route('ecommerce.blogs.detail', $comment->blog->slug) }}#comment-{{ $comment->id }}" target="_blank" class="small text-muted">
                              <i class="fas fa-external-link-alt"></i> {{ __('View on store') }}
                            </a>
                          </div>
                        @else
                          <span class="text-muted font-italic">{{ __('Article deleted') }}</span>
                        @endif
                      </td>
                      <td>
                        @if ($comment->parent_id && $comment->parent)
                          <div class="small text-info mb-1">
                            <i class="fas fa-reply fa-rotate-180"></i> {{ __('Reply to') }} <strong>{{ $comment->parent->name }}</strong>
                          </div>
                        @endif
                        <p class="mb-0 text-dark small" style="line-height: 1.4;">
                          {{ Str::limit($comment->comment, 80) }}
                        </p>
                      </td>
                      <td>
                        @can('blog-comments.edit')
                          <form action="{{ route('blog-comments.toggleApproval', $comment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $comment->is_approved ? 'btn-success' : 'btn-warning' }}" title="{{ $comment->is_approved ? __('Click to Unapprove') : __('Click to Approve') }}" style="border-radius: 20px; font-size: 11px; padding: 2px 10px;">
                              <i class="fas {{ $comment->is_approved ? 'fa-check' : 'fa-clock' }}"></i>
                              {{ $comment->is_approved ? __('Approved') : __('Pending') }}
                            </button>
                          </form>
                        @else
                          <span class="badge {{ $comment->is_approved ? 'badge-success' : 'badge-warning' }}">
                            {{ $comment->is_approved ? __('Approved') : __('Pending') }}
                          </span>
                        @endcan
                      </td>
                      <td>
                        <span class="text-muted small">
                          <i class="far fa-calendar-alt"></i> {{ $comment->created_at->format('M d, Y H:i') }}
                        </span>
                      </td>
                      <td class="text-center">
                        <div class="btn-group" role="group">
                          <a href="{{ route('blog-comments.show', $comment) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('blog-comments.delete')
                            <form action="{{ route('blog-comments.destroy', $comment) }}" method="POST" class="d-inline">
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
