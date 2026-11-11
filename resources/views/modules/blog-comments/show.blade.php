@extends('layouts.app')

@section('title', __('Shop › Blog Comment Details'))

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
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('blog-comments.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Comment Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blogs.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blog-comments.index') }}">{{ __('Blog Comments') }}</a></div>
      <div class="breadcrumb-item">#{{ $blogComment->id }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Comment from') }}: {{ $blogComment->name }}</h2>
    <p class="section-lead">
      {{ __('Inspect message content and manage public approval status.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-comment-dots text-primary mr-2"></i> {{ __('Comment Content') }}</h4>
            <div class="card-header-action">
              @if ($blogComment->is_approved)
                <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Approved') }}</span>
              @else
                <span class="badge badge-warning"><i class="fas fa-clock"></i> {{ __('Pending') }}</span>
              @endif
            </div>
          </div>
          <div class="card-body">
            @if ($blogComment->parent)
              <div class="alert alert-light border mb-4">
                <div class="small font-weight-bold text-info mb-1">
                  <i class="fas fa-reply fa-rotate-180"></i> {{ __('In reply to') }} {{ $blogComment->parent->name }}:
                </div>
                <p class="small text-muted mb-0 font-italic">
                  "{{ Str::limit($blogComment->parent->comment, 150) }}"
                </p>
              </div>
            @endif

            <div class="p-3 bg-light rounded mb-4" style="font-size: 1.05rem; line-height: 1.6;">
              {{ $blogComment->comment }}
            </div>

            @if ($blogComment->replies->isNotEmpty())
              <h5 class="mt-4 mb-3">{{ __('Replies to this comment') }} ({{ $blogComment->replies->count() }})</h5>
              <div class="list-group">
                @foreach ($blogComment->replies as $reply)
                  <div class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                      <h6 class="mb-0 text-dark">{{ $reply->name }} <small class="text-muted">({{ $reply->email }})</small></h6>
                      <small class="text-muted">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                    </div>
                    <p class="mb-1 small text-dark">{{ $reply->comment }}</p>
                    <div class="mt-2">
                      <form action="{{ route('blog-comments.toggleApproval', $reply) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-xs {{ $reply->is_approved ? 'btn-success' : 'btn-warning' }} py-0 px-2" style="font-size: 10px;">
                          {{ $reply->is_approved ? __('Approved') : __('Pending') }}
                        </button>
                      </form>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
          <div class="card-footer bg-whitesmoke d-flex justify-content-between align-items-center">
            <a href="{{ route('blog-comments.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
            <div>
              @can('blog-comments.edit')
                <form action="{{ route('blog-comments.toggleApproval', $blogComment) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn {{ $blogComment->is_approved ? 'btn-warning' : 'btn-success' }} mr-1">
                    <i class="fas {{ $blogComment->is_approved ? 'fa-times' : 'fa-check' }}"></i>
                    {{ $blogComment->is_approved ? __('Unapprove (Hide)') : __('Approve (Publish)') }}
                  </button>
                </form>
              @endcan
              @can('blog-comments.delete')
                <form action="{{ route('blog-comments.destroy', $blogComment) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="btn btn-danger btn-delete">
                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                  </button>
                </form>
              @endcan
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <!-- Commenter Info -->
        <div class="card card-primary">
          <div class="card-header">
            <h4>{{ __('Commenter Profile') }}</h4>
          </div>
          <div class="card-body text-center">
            <img src="{{ $blogComment->avatar_url }}" alt="{{ $blogComment->name }}" class="rounded-circle shadow-sm mb-3" width="70" height="70" style="object-fit: cover;">
            <h5 class="mb-1 text-dark">{{ $blogComment->name }}</h5>
            <p class="text-muted small mb-3">{{ $blogComment->email }}</p>
            <p class="small text-muted mb-0">
              <i class="far fa-calendar-alt"></i> {{ $blogComment->created_at->format('d M Y, H:i') }}
            </p>
          </div>
        </div>

        <!-- Article Info -->
        @if ($blogComment->blog)
          <div class="card card-info">
            <div class="card-header">
              <h4>{{ __('Article Info') }}</h4>
            </div>
            <div class="card-body">
              <img src="{{ $blogComment->blog->image_url }}" alt="{{ $blogComment->blog->title }}" class="img-fluid rounded mb-3" style="max-height: 140px; width: 100%; object-fit: cover;">
              <h6 class="text-dark mb-2">{{ $blogComment->blog->title }}</h6>
              <p class="text-muted small mb-3">
                <span class="badge badge-primary">{{ $blogComment->blog->category ?? 'General' }}</span>
              </p>
              <a href="{{ route('blogs.show', $blogComment->blog) }}" class="btn btn-outline-info btn-sm btn-block mb-2">
                <i class="fas fa-edit"></i> {{ __('Manage Article in Admin') }}
              </a>
              <a href="{{ route('ecommerce.blogs.detail', $blogComment->blog->slug) }}#comment-{{ $blogComment->id }}" target="_blank" class="btn btn-outline-secondary btn-sm btn-block">
                <i class="fas fa-external-link-alt"></i> {{ __('View on Storefront') }}
              </a>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

