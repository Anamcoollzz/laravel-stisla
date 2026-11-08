@extends('layouts.app')

@section('title', __('Modules › Create Product Category'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    function updateLevelIndicator() {
      var selected = $('#parent_id option:selected');
      var parentLevel = selected.data('level');
      var badge = $('#level-badge');
      var note = $('#level-note');

      if (!parentLevel || parentLevel === 0) {
        badge.attr('class', 'badge badge-primary').text(@json(__('Level 1 (Section Header)')));
        note.text(@json(__('Appears as a section title / group header in the sidebar menu (e.g. Fashion, Bakery).')));
      } else if (parentLevel === 1) {
        badge.attr('class', 'badge badge-info').text(@json(__('Level 2 (Main Category)')));
        note.text(@json(__('Appears as a clickable category or dropdown item with an icon (e.g. Clothes, Shoes).')));
      } else if (parentLevel === 2) {
        badge.attr('class', 'badge badge-warning').text(@json(__('Level 3 (Subcategory Item)')));
        note.text(@json(__('Appears inside a Level 2 dropdown menu (e.g. T-shirts, Shirts, Boots).')));
      }
    }

    $('#parent_id').on('change', updateLevelIndicator);
    $(document).ready(updateLevelIndicator);

    // Auto slug from name if slug is empty
    $('#name').on('input', function() {
      if (!$('#slug').data('manually-edited')) {
        var slug = $(this).val()
          .toLowerCase()
          .replace(/[^\w\s-]/g, '')
          .trim()
          .replace(/[\s_-]+/g, '-')
          .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
      }
    });

    $('#slug').on('input', function() {
      $(this).data('manually-edited', $(this).val().length > 0);
    });

    // Icon file preview
    $('#icon_file').on('change', function(e) {
      var file = e.target.files[0];
      if (file) {
        var reader = new FileReader();
        reader.onload = function(evt) {
          $('#icon-preview-img').attr('src', evt.target.result).show();
          $('#icon-preview-placeholder').hide();
        };
        reader.readAsDataURL(file);
      }
    });

    // Icon text path preview
    $('#icon').on('input', function() {
      var val = $(this).val().trim();
      if (val) {
        var src = val.startsWith('http') || val.startsWith('/') ? val : '{{ asset('') }}' + val;
        $('#icon-preview-img').attr('src', src).show();
        $('#icon-preview-placeholder').hide();
      } else if (!$('#icon_file').val()) {
        $('#icon-preview-img').hide();
        $('#icon-preview-placeholder').show();
      }
    });

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('product-categories.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Product Category') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-categories.index') }}">{{ __('Product Categories') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Product Category') }}</h2>
    <p class="section-lead">
      {{ __('Add a new 3-level product category for the ecommerce catalog and sidebar navigation.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="card">
          <form action="{{ route('product-categories.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('Category Form') }}</h4>
            </div>
            <div class="card-body">
              <!-- Parent Category & Target Hierarchy Level -->
              <div class="form-group">
                <label for="parent_id">{{ __('Parent Category') }}</label>
                <select
                  id="parent_id"
                  name="parent_id"
                  class="form-control @error('parent_id') is-invalid @enderror"
                >
                  <option value="" data-level="0" @selected(old('parent_id', request('parent_id')) == '')>
                    -- {{ __('None (Level 1: Section Header)') }} --
                  </option>
                  @foreach ($potentialParents as $parent)
                    <option
                      value="{{ $parent->id }}"
                      data-level="{{ $parent->level }}"
                      @selected(old('parent_id', request('parent_id')) == $parent->id)
                    >
                      @if ($parent->level === 1)
                        📁 [L1 Section] {{ $parent->name }}
                      @elseif ($parent->level === 2)
                        &nbsp;&nbsp;&nbsp;&nbsp;↳ 📂 [L2 Category] {{ $parent->parent?->name }} › {{ $parent->name }}
                      @endif
                    </option>
                  @endforeach
                </select>
                @error('parent_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="mt-2">
                  <span class="mr-1">{{ __('Resulting Level:') }}</span>
                  <span id="level-badge" class="badge badge-primary">{{ __('Level 1 (Section Header)') }}</span>
                  <small id="level-note" class="d-block text-muted mt-1"></small>
                </div>
              </div>

              <!-- Category Name & Slug -->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="name">{{ __('Category Name') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="{{ __('e.g. Clothes, Shoes, Bakery') }}"
                    value="{{ old('name') }}"
                    required
                  >
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @else
                    <div class="invalid-feedback">{{ __('Please fill in the category name.') }}</div>
                  @enderror
                </div>

                <div class="form-group col-md-6">
                  <label for="slug">{{ __('Slug') }}</label>
                  <input
                    type="text"
                    id="slug"
                    name="slug"
                    class="form-control @error('slug') is-invalid @enderror"
                    placeholder="{{ __('auto-generated-from-name') }}"
                    value="{{ old('slug') }}"
                  >
                  @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-text text-muted">{{ __('Leave blank to generate automatically.') }}</small>
                </div>
              </div>

              <!-- Icon: Path & File Upload -->
              <div class="form-row">
                <div class="form-group col-md-7">
                  <label for="icon">{{ __('Icon Path / Asset') }}</label>
                  <input
                    type="text"
                    id="icon"
                    name="icon"
                    class="form-control @error('icon') is-invalid @enderror"
                    placeholder="e.g. mantu/assets/img/icons/clothes-2.svg"
                    value="{{ old('icon') }}"
                  >
                  @error('icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-text text-muted">{{ __('Path relative to public directory (e.g. mantu/assets/img/icons/clothes-2.svg).') }}</small>
                </div>

                <div class="form-group col-md-5">
                  <label for="icon_file">{{ __('Or Upload Icon') }}</label>
                  <div class="custom-file">
                    <input
                      type="file"
                      class="custom-file-input @error('icon_file') is-invalid @enderror"
                      id="icon_file"
                      name="icon_file"
                      accept="image/*,.svg"
                    >
                    <label class="custom-file-label" for="icon_file">{{ __('Choose file') }}</label>
                    @error('icon_file')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <small class="form-text text-muted">{{ __('PNG, JPG, SVG, WebP up to 2MB.') }}</small>
                </div>
              </div>

              <!-- Icon Preview Box -->
              <div class="form-group">
                <label>{{ __('Icon Preview') }}</label>
                <div class="d-flex align-items-center p-2 border rounded" style="background: #fdfdff; width: fit-content; min-width: 140px;">
                  <img id="icon-preview-img" src="" alt="Icon Preview" style="display: none; width: 36px; height: 36px; object-fit: contain; margin-right: 10px;">
                  <span id="icon-preview-placeholder" class="text-muted small"><i class="fas fa-image mr-1"></i> {{ __('No icon selected') }}</span>
                </div>
              </div>

              <!-- Route & URL -->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="route">{{ __('Target Route') }}</label>
                  <input
                    type="text"
                    id="route"
                    name="route"
                    list="routes-list"
                    class="form-control @error('route') is-invalid @enderror"
                    placeholder="e.g. ecommerce.shop.html"
                    value="{{ old('route') }}"
                  >
                  <datalist id="routes-list">
                    @foreach ($availableRoutes as $routeName)
                      <option value="{{ $routeName }}"></option>
                    @endforeach
                  </datalist>
                  @error('route')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-text text-muted">{{ __('Named route to navigate to (e.g. ecommerce.shop.html).') }}</small>
                </div>

                <div class="form-group col-md-6">
                  <label for="url">{{ __('Custom URL (Fallback)') }}</label>
                  <input
                    type="text"
                    id="url"
                    name="url"
                    class="form-control @error('url') is-invalid @enderror"
                    placeholder="e.g. /shop?category=clothes or #"
                    value="{{ old('url') }}"
                  >
                  @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-text text-muted">{{ __('Used if route is empty. Use "#" for dropdown toggles.') }}</small>
                </div>
              </div>

              <!-- Order & Is Active -->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="order">{{ __('Sort Order') }}</label>
                  <input
                    type="number"
                    id="order"
                    name="order"
                    class="form-control @error('order') is-invalid @enderror"
                    value="{{ old('order', 0) }}"
                    min="0"
                  >
                  @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-text text-muted">{{ __('Display order (lower numbers appear first).') }}</small>
                </div>

                <div class="form-group col-md-6 d-flex align-items-center pt-3">
                  <div class="custom-control custom-switch">
                    <input
                      type="checkbox"
                      class="custom-control-input"
                      id="is_active"
                      name="is_active"
                      value="1"
                      @checked(old('is_active', true))
                    >
                    <label class="custom-control-label font-weight-bold" for="is_active">{{ __('Active / Visible') }}</label>
                    <small class="form-text text-muted">{{ __('Inactive categories will be hidden in the sidebar navigation.') }}</small>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div class="form-group">
                <label for="description">{{ __('Description') }}</label>
                <textarea
                  id="description"
                  name="description"
                  class="form-control @error('description') is-invalid @enderror"
                  rows="3"
                  placeholder="{{ __('Optional description or note for this category...') }}"
                >{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('product-categories.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Save Category') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

