@extends('layouts.app')

@section('title', __('Shop › Blog Details'))

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
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('blogs.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Blog Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blogs.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blogs.index') }}">{{ __('Blogs') }}</a></div>
      <div class="breadcrumb-item">{{ __('Details') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $blog->title }}</h2>
    <p class="section-lead">
      {{ __('Published under') }} <span class="badge badge-primary">{{ $blog->category ?? 'General' }}</span> • {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-body">
            @if ($blog->image_url)
              <div class="mb-4 text-center">
                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="img-fluid rounded shadow-sm" style="max-height: 380px; width: 100%; object-fit: cover;">
              </div>
            @endif

            @if ($blog->short_description)
              <div class="alert alert-light border font-italic mb-4">
                {{ $blog->short_description }}
              </div>
            @endif

            <div class="article-content" style="line-height: 1.8; font-size: 1.05rem;">
              {!! $blog->content !!}
            </div>
          </div>
          <div class="card-footer bg-whitesmoke d-flex justify-content-between align-items-center">
            <a href="{{ route('blogs.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
            <div>
              @can('blogs.create')
                <form action="{{ route('blogs.duplicate', $blog) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-secondary mr-1">
                    <i class="fas fa-copy"></i> {{ __('Duplicate') }}
                  </button>
                </form>
              @endcan
              @can('blogs.edit')
                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-warning mr-1">
                  <i class="fas fa-pencil-alt"></i> {{ __('Edit') }}
                </a>
              @endcan
              @can('blogs.delete')
                <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="d-inline">
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
        <div class="card card-primary">
          <div class="card-header">
            <h4>{{ __('Post Metadata') }}</h4>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Status') }}</span>
                @if ($blog->is_active)
                  <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Active') }}</span>
                @else
                  <span class="badge badge-danger"><i class="fas fa-times"></i> {{ __('Inactive') }}</span>
                @endif
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Category') }}</span>
                <span class="badge badge-primary">{{ $blog->category ?? '-' }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Author') }}</span>
                <span class="font-weight-bold">{{ $blog->author_name ?? '-' }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Order') }}</span>
                <span class="badge badge-light">#{{ $blog->order }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Published Date') }}</span>
                <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : '-' }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Created At') }}</span>
                <span>{{ $blog->created_at->format('M d, Y H:i') }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Slug') }}</span>
                <code class="text-dark">{{ $blog->slug }}</code>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

