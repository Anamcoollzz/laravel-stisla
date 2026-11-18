@extends('layouts.app')

@section('title', __('Edit Berita'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('admin.news.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Berita') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">{{ __('News Articles') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Konten Berita') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="title">{{ __('Judul Berita') }} <span class="text-danger">*</span></label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $news->title) }}" required autofocus>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="slug">{{ __('Slug') }}</label>
                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $news->slug) }}">
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group mb-0">
                <label for="content">{{ __('Konten') }}</label>
                <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" style="min-height: 280px;">{{ old('content', $news->content) }}</textarea>
                @error('content')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Pengaturan Publikasi') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="news_category_id">{{ __('Kategori') }}</label>
                <select id="news_category_id" name="news_category_id" class="form-control @error('news_category_id') is-invalid @enderror">
                  <option value="">-- {{ __('Pilih Kategori') }} --</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('news_category_id', $news->news_category_id) === (string) $category->id)>{{ $category->name }}</option>
                  @endforeach
                </select>
                @error('news_category_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="tag_ids">{{ __('News Tags') }}</label>
                @php
                  $selectedTagIds = old('tag_ids', $news->tags->pluck('id')->all());
                @endphp
                <select id="tag_ids" name="tag_ids[]" class="form-control @error('tag_ids') is-invalid @enderror" multiple style="min-height: 110px;">
                  @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(in_array($tag->id, $selectedTagIds))>{{ $tag->name }}</option>
                  @endforeach
                </select>
                @error('tag_ids')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('Tahan Ctrl / Cmd untuk memilih lebih dari satu tag.') }}</small>
              </div>

              <div class="form-group">
                <label for="publisher_user_id">{{ __('Publisher') }}</label>
                <select id="publisher_user_id" name="publisher_user_id" class="form-control @error('publisher_user_id') is-invalid @enderror">
                  @foreach ($publishers as $publisher)
                    <option value="{{ $publisher->id }}" @selected((string) old('publisher_user_id', $news->publisher_user_id) === (string) $publisher->id)>{{ $publisher->name }} ({{ $publisher->email }})</option>
                  @endforeach
                </select>
                @error('publisher_user_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                  <option value="draft" @selected(old('status', $news->status) === 'draft')>Draft</option>
                  <option value="published" @selected(old('status', $news->status) === 'published')>Published</option>
                  <option value="archived" @selected(old('status', $news->status) === 'archived')>Archived</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="published_at">{{ __('Tanggal Publikasi') }}</label>
                <input type="datetime-local" id="published_at" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', $news->published_at?->format('Y-m-d\TH:i')) }}">
                @error('published_at')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="thumbnail">{{ __('Thumbnail Utama') }}</label>
                @if ($news->thumbnail_url)
                  <div class="mb-2">
                    <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" class="img-fluid rounded border" style="max-height: 120px;">
                  </div>
                @endif
                <input type="file" id="thumbnail" name="thumbnail" class="form-control-file @error('thumbnail') is-invalid @enderror" accept="image/*">
                @error('thumbnail')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group mb-0">
                <label for="thumbnail_mini">{{ __('Thumbnail Mini') }}</label>
                @if ($news->thumbnail_mini_url)
                  <div class="mb-2">
                    <img src="{{ $news->thumbnail_mini_url }}" alt="{{ $news->title }}" class="img-fluid rounded border" style="max-height: 80px;">
                  </div>
                @endif
                <input type="file" id="thumbnail_mini" name="thumbnail_mini" class="form-control-file @error('thumbnail_mini') is-invalid @enderror" accept="image/*">
                @error('thumbnail_mini')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('admin.news.index') }}" class="btn btn-secondary mr-1">{{ __('Batal') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Perbarui') }}</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection
