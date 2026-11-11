@extends('layouts.app')

@section('title', __('Shop › Edit Blog Article'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).ready(function() {
      $('.summernote').summernote({
        dialogsInBody: true,
        minHeight: 280,
        toolbar: [
          ['style', ['bold', 'italic', 'underline', 'clear']],
          ['font', ['strikethrough', 'superscript', 'subscript']],
          ['fontsize', ['fontsize']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['codeview', 'help']]
        ]
      });
    });

    function previewImage(event) {
      const input = event.target;
      const preview = document.getElementById('imagePreview');
      const label = input.nextElementSibling;

      if (input.files && input.files[0]) {
        const reader = new FileReader();
        label.textContent = input.files[0].name;

        reader.onload = function(e) {
          preview.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
      } else {
        label.textContent = '{{ __('Choose file') }}';
      }
    }
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('blogs.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Blog Article') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blogs.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('blogs.index') }}">{{ __('Blogs') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Article') }}: {{ $blog->title }}</h2>
    <p class="section-lead">
      {{ __('Update article details, category, featured image or publishing status.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <form action="{{ route('blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')

            <div class="card-header">
              <h4>{{ __('Article Content') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label>{{ __('Title') }} <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $blog->title) }}" placeholder="e.g. Marketing Guide: 5 Steps to Success" required autofocus>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>{{ __('Slug (URL identifier)') }}</label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $blog->slug) }}" placeholder="e.g. marketing-guide-5-steps-to-success">
                <small class="form-text text-muted">{{ __('Unique URL slug. Leave blank to regenerate from title.') }}</small>
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>{{ __('Category') }}</label>
                    <input list="categories" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $blog->category) }}" placeholder="e.g. Fashion, Shoes, Bags, Grocery, Cosmetics">
                    <datalist id="categories">
                      <option value="Fashion">
                      <option value="Shoes">
                      <option value="Grocery">
                      <option value="Bags">
                      <option value="Cosmetics">
                      <option value="Lifestyle">
                      <option value="Electronics">
                    </datalist>
                    @error('category')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>{{ __('Author Name') }}</label>
                    <input type="text" name="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name', $blog->author_name) }}" placeholder="e.g. John Deo">
                    @error('author_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>{{ __('Featured Image') }}</label>
                <div class="mb-2">
                  <img id="imagePreview" src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="img-thumbnail rounded" style="max-height: 180px; width: auto; object-fit: cover;">
                </div>
                <div class="custom-file">
                  <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="customFile" accept="image/*" onchange="previewImage(event)">
                  <label class="custom-file-label" for="customFile">{{ __('Choose new file to replace') }}</label>
                </div>
                <small class="form-text text-muted">{{ __('Leave empty to keep existing image. Recommended size: 800x500 px. Max: 2MB.') }}</small>
                @error('image')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if ($blog->image)
                  <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" name="remove_image" value="1" class="custom-control-input" id="remove_image">
                    <label class="custom-control-label text-danger" for="remove_image">{{ __('Remove custom image (reset to default)') }}</label>
                  </div>
                @endif
              </div>

              <div class="form-group">
                <label>{{ __('Short Description / Excerpt') }}</label>
                <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="3" placeholder="Brief summary displayed on cards and list pages...">{{ old('short_description', $blog->short_description) }}</textarea>
                @error('short_description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>{{ __('Full Content') }}</label>
                <textarea name="content" class="summernote form-control @error('content') is-invalid @enderror">{{ old('content', $blog->content) }}</textarea>
                @error('content')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>{{ __('Published Date') }}</label>
                    <input type="date" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d') : '') }}">
                    @error('published_at')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>{{ __('Display Order') }}</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $blog->order) }}" min="0">
                    <small class="form-text text-muted">{{ __('Lower number displays first.') }}</small>
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="control-label">{{ __('Publishing Status') }}</div>
                <label class="custom-switch mt-2 pl-0">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ old('is_active', $blog->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Active / Published on Storefront') }}</span>
                </label>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('blogs.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ __('Update Article') }}</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Quick Info / Tips -->
      <div class="col-12 col-md-4 col-lg-4">
        <div class="card card-primary">
          <div class="card-header">
            <h4><i class="fas fa-info-circle text-primary"></i> {{ __('Article Meta') }}</h4>
          </div>
          <div class="card-body">
            <p><strong>{{ __('Created At') }}:</strong><br>{{ $blog->created_at->format('d M Y, H:i') }}</p>
            <p><strong>{{ __('Last Updated') }}:</strong><br>{{ $blog->updated_at->format('d M Y, H:i') }}</p>
            <p><strong>{{ __('Public URL') }}:</strong><br>
              <a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}" target="_blank" class="text-primary small">
                {{ url('/ecommerce/blog/' . $blog->slug) }} <i class="fas fa-external-link-alt"></i>
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

