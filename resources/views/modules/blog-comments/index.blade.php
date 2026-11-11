@extends('layouts.app')

@section('title', __('Shop › Blog Comments'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

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
            <div class="card-header-form">
              <form action="{{ route('blog-comments.index') }}" method="GET">
                <div class="input-group">
                  <select name="status" class="form-control mr-2" onchange="this.form.submit()" style="border-radius: 4px;">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                  </select>
                  <input type="text" name="search" class="form-control" placeholder="{{ __('Search comments...') }}" value="{{ request('search') }}">
                  <div class="input-group-btn">
                    <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                    @if (request('search') || request('status'))
                      <a href="{{ route('blog-comments.index') }}" class="btn btn-light"><i class="fas fa-times"></i></a>
                    @endif
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead>
                  <tr>
                    <th style="width: 60px;">#</th>
                    <th style="width: 200px;">{{ __('Commenter') }}</th>
                    <th>{{ __('Article') }}</th>
                    <th>{{ __('Comment') }}</th>
                    <th style="width: 120px;">{{ __('Status') }}</th>
                    <th style="width: 140px;">{{ __('Date') }}</th>
                    <th style="width: 130px;" class="text-center">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($comments as $comment)
                    <tr>
                      <td>{{ $comment->id }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="{{ $comment->avatar_url }}" alt="{{ $comment->name }}" class="rounded-circle mr-2" width="35" height="35" style="object-fit: cover;">
                          <div>
                            <div class="font-weight-bold text-dark">{{ $comment->name }}</div>
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
                  @empty
                    <tr>
                      <td colspan="7" class="text-center py-4 text-muted">
                        {{ __('No comments found.') }}
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          @if ($comments->hasPages())
            <div class="card-footer bg-whitesmoke text-right">
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                  {{ __('Showing') }} {{ $comments->firstItem() ?? 0 }} - {{ $comments->lastItem() ?? 0 }} {{ __('of') }} {{ $comments->total() }}
                </span>
                {{ $comments->links() }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

