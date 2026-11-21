@extends('layouts.app')

@section('title', __('Detail Berita: ') . $news->title)

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('admin.news.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Detail Berita') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">{{ __('News Articles') }}</a></div>
      <div class="breadcrumb-item">#{{ $news->id }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ $news->title }}</h4>
          </div>
          <div class="card-body">
            @if ($news->thumbnail_url)
              <div class="mb-4 text-center">
                <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" class="img-fluid rounded border" style="max-height: 360px; object-fit: cover;">
              </div>
            @endif

            <div class="text-dark" style="line-height: 1.7; white-space: pre-line;">{{ $news->content ?: '-' }}</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Metadata Berita') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Slug') }}</label>
              <code>{{ $news->slug }}</code>
            </div>
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Kategori') }}</label>
              @if ($news->category)
                <span class="badge badge-info">{{ $news->category->name }}</span>
              @else
                <span class="text-muted">-</span>
              @endif
            </div>
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('News Tags') }}</label>
              @forelse ($news->tags as $tag)
                <span class="badge badge-primary mr-1 mb-1">{{ $tag->name }}</span>
              @empty
                <span class="text-muted">-</span>
              @endforelse
            </div>
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Publisher') }}</label>
              <div>{{ $news->publisher?->name ?? '-' }}</div>
            </div>
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Status') }}</label>
              @if ($news->status === 'published')
                <span class="badge badge-success">Published</span>
              @elseif ($news->status === 'archived')
                <span class="badge badge-secondary">Archived</span>
              @else
                <span class="badge badge-warning text-dark">Draft</span>
              @endif
            </div>
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Published At') }}</label>
              <div>{{ $news->published_at ? $news->published_at->format('d F Y, H:i') : '-' }}</div>
            </div>
            @if ($news->thumbnail_mini_url)
              <div class="form-group mb-0">
                <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Thumbnail Mini') }}</label>
                <img src="{{ $news->thumbnail_mini_url }}" alt="{{ $news->title }}" class="rounded border" style="max-height: 90px;">
              </div>
            @endif
          </div>
          <div class="card-footer bg-whitesmoke text-right">
            @can('news.edit')
              <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt mr-1"></i> {{ __('Edit') }}
              </a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
