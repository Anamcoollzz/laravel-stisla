@extends('layouts.app')

@section('title', __('Modules › Create Product'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
  <style>
    .bootstrap-tagsinput {
      width: 100% !important;
      min-height: 42px;
      padding: 6px 12px;
    }
    .bootstrap-tagsinput .badge {
      margin: 2px;
      font-size: 12px;
      padding: 4px 8px;
    }
    .spec-row {
      transition: all 0.2s ease;
    }
    .color-dot {
      display: inline-block;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      margin-right: 6px;
      vertical-align: middle;
      border: 1px solid rgba(0,0,0,0.2);
    }
  </style>
@endpush

@push('js')
  <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
  <script src="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    // Initialize Summernote
    $('.summernote').summernote({
      height: 200,
      dialogsInBody: true,
      toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link', 'table']],
        ['view', ['codeview']]
      ]
    });

    $('.summernote-simple').summernote({
      height: 120,
      dialogsInBody: true,
      toolbar: [
        ['style', ['bold', 'italic', 'underline']],
        ['para', ['ul', 'ol']],
        ['insert', ['link']]
      ]
    });

    // Auto slug generator
    $('#title').on('input', function() {
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

    // Price and discount calculation preview
    function updateFinalPrice() {
      var price = parseFloat($('#price').val()) || 0;
      var discount = parseFloat($('#discount_percent').val()) || 0;
      if (discount > 100) discount = 100;
      if (discount < 0) discount = 0;

      var finalPrice = price - (price * (discount / 100));
      $('#final-price-preview').text('Rp ' + Math.round(finalPrice).toLocaleString('id-ID'));
    }

    $('#price, #discount_percent').on('input', updateFinalPrice);
    $(document).ready(updateFinalPrice);

    // Thumbnail Preview (file & text)
    $('#thumbnail_file').on('change', function(e) {
      var file = e.target.files[0];
      if (file) {
        var reader = new FileReader();
        reader.onload = function(evt) {
          $('#thumb-preview-img').attr('src', evt.target.result).show();
          $('#thumb-preview-placeholder').hide();
        };
        reader.readAsDataURL(file);
      }
    });

    $('#thumbnail').on('input', function() {
      var val = $(this).val().trim();
      if (val) {
        var src = val.startsWith('http') || val.startsWith('/') ? val : '{{ asset('') }}' + val;
        $('#thumb-preview-img').attr('src', src).show();
        $('#thumb-preview-placeholder').hide();
      } else if (!$('#thumbnail_file').val()) {
        $('#thumb-preview-img').hide();
        $('#thumb-preview-placeholder').show();
      }
    });

    // Multiple Gallery Images Preview
    $('#product_images').on('change', function(e) {
      var container = $('#gallery-preview-container');
      container.empty();
      var files = e.target.files;
      if (files && files.length > 0) {
        $(this).next('.custom-file-label').text(files.length + ' {{ __("files selected") }}');
        Array.from(files).forEach(function(file, index) {
          var reader = new FileReader();
          reader.onload = function(evt) {
            var item = $('<div class="position-relative border rounded p-1 bg-light" style="width: 75px; height: 75px;">' +
              '<img src="' + evt.target.result + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">' +
              '<span class="badge badge-dark position-absolute" style="top: 2px; right: 2px; font-size: 10px;">#' + (index + 1) + '</span>' +
              '</div>');
            container.append(item);
          };
          reader.readAsDataURL(file);
        });
      } else {
        $(this).next('.custom-file-label').text('{{ __("Choose multiple files") }}');
      }
    });

    // Specifications Repeater
    $('#btn-add-spec').on('click', function() {
      var row = `
        <div class="row mb-2 spec-row align-items-center">
          <div class="col-md-5 col-5">
            <input type="text" name="spec_keys[]" class="form-control form-control-sm" placeholder="{{ __('e.g. Material, Dimensions') }}">
          </div>
          <div class="col-md-6 col-5">
            <input type="text" name="spec_values[]" class="form-control form-control-sm" placeholder="{{ __('e.g. Cotton 100%, 25x15 cm') }}">
          </div>
          <div class="col-md-1 col-2 text-right">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-spec" title="{{ __('Remove') }}">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      `;
      $('#specs-container').append(row);
    });

    $(document).on('click', '.btn-remove-spec', function() {
      $(this).closest('.spec-row').remove();
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
      <a href="{{ route('products.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Product') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Products') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Product') }}</h2>
    <p class="section-lead">
      {{ __('Fill in all required fields to add a new product to the catalog.') }}
    </p>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate="">
      @csrf
      <div class="row">
        <!-- Main Product Information -->
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Basic Information') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="title">{{ __('Product Title') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="title"
                  name="title"
                  class="form-control @error('title') is-invalid @enderror"
                  placeholder="{{ __('e.g. Nike Air Zoom Pegasus 40') }}"
                  value="{{ old('title') }}"
                  required
                >
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="sku">{{ __('SKU (Stock Keeping Unit)') }}</label>
                  <input
                    type="text"
                    id="sku"
                    name="sku"
                    class="form-control @error('sku') is-invalid @enderror"
                    placeholder="{{ __('e.g. PRD-2026-0001') }}"
                    value="{{ old('sku') }}"
                  >
                  @error('sku')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-text text-muted">{{ __('Leave blank to generate automatically.') }}</small>
                </div>

                <div class="form-group col-md-6">
                  <label for="slug">{{ __('Slug') }}</label>
                  <input
                    type="text"
                    id="slug"
                    name="slug"
                    class="form-control @error('slug') is-invalid @enderror"
                    placeholder="{{ __('e.g. nike-air-zoom-pegasus-40') }}"
                    value="{{ old('slug') }}"
                  >
                  @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-text text-muted">{{ __('URL friendly identifier.') }}</small>
                </div>
              </div>

              <!-- Short Description HTML -->
              <div class="form-group">
                <label for="short_description">{{ __('Short Description') }} (HTML)</label>
                <textarea
                  id="short_description"
                  name="short_description"
                  class="summernote-simple form-control @error('short_description') is-invalid @enderror"
                  placeholder="{{ __('Brief summary of the product...') }}"
                >{{ old('short_description') }}</textarea>
                @error('short_description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('Brief highlight displayed in product summaries and listing cards.') }}</small>
              </div>

              <!-- Full Description Detail HTML -->
              <div class="form-group">
                <label for="description_detail">{{ __('Detailed Description') }} (HTML)</label>
                <textarea
                  id="description_detail"
                  name="description_detail"
                  class="summernote form-control @error('description_detail') is-invalid @enderror"
                  placeholder="{{ __('Complete product description, features, and details...') }}"
                >{{ old('description_detail') }}</textarea>
                @error('description_detail')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Specifications Repeater -->
              <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="mb-0 font-weight-bold">{{ __('Specifications') }} (Key - Value Pairs)</label>
                  <button type="button" id="btn-add-spec" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus mr-1"></i> {{ __('Add Specification') }}
                  </button>
                </div>
                <div id="specs-container">
                  <div class="row mb-2 spec-row align-items-center">
                    <div class="col-md-5 col-5">
                      <input type="text" name="spec_keys[]" class="form-control form-control-sm" placeholder="{{ __('e.g. Material') }}" value="Material">
                    </div>
                    <div class="col-md-6 col-5">
                      <input type="text" name="spec_values[]" class="form-control form-control-sm" placeholder="{{ __('e.g. 100% Cotton') }}">
                    </div>
                    <div class="col-md-1 col-2 text-right">
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-spec" title="{{ __('Remove') }}">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <small class="form-text text-muted">{{ __('Add technical specs like Material, Dimensions, Weight, Battery, etc.') }}</small>
              </div>

              <!-- Tags Input -->
              <div class="form-group">
                <label for="tags">{{ __('Tags') }}</label>
                <input
                  type="text"
                  id="tags"
                  name="tags"
                  data-role="tagsinput"
                  class="form-control @error('tags') is-invalid @enderror"
                  value="{{ old('tags', 'fashion, trend, new') }}"
                  placeholder="{{ __('Type and press enter...') }}"
                >
                @error('tags')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('Separate tags with commas or press enter.') }}</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar / Pricing / Relations -->
        <div class="col-12 col-lg-4">
          <!-- Pricing & Stock -->
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Pricing & Inventory') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="price">{{ __('Price (IDR)') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                  </div>
                  <input
                    type="number"
                    id="price"
                    name="price"
                    class="form-control @error('price') is-invalid @enderror"
                    placeholder="0"
                    value="{{ old('price', 0) }}"
                    min="0"
                    step="1000"
                    required
                  >
                  @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="discount_percent">{{ __('Discount (%)') }}</label>
                  <div class="input-group">
                    <input
                      type="number"
                      id="discount_percent"
                      name="discount_percent"
                      class="form-control @error('discount_percent') is-invalid @enderror"
                      placeholder="0"
                      value="{{ old('discount_percent', 0) }}"
                      min="0"
                      max="100"
                      step="1"
                    >
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                    @error('discount_percent')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="stock">{{ __('Stock Quantity') }} <span class="text-danger">*</span></label>
                  <input
                    type="number"
                    id="stock"
                    name="stock"
                    class="form-control @error('stock') is-invalid @enderror"
                    placeholder="0"
                    value="{{ old('stock', 10) }}"
                    min="0"
                    required
                  >
                  @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="p-2 mb-3 bg-light rounded text-center">
                <span class="text-muted small d-block">{{ __('Calculated Final Price:') }}</span>
                <h5 id="final-price-preview" class="text-primary mb-0 font-weight-bold">Rp 0</h5>
              </div>

              <div class="custom-control custom-switch mb-3">
                <input
                  type="checkbox"
                  class="custom-control-input"
                  id="is_active"
                  name="is_active"
                  value="1"
                  @checked(old('is_active', true))
                >
                <label class="custom-control-label font-weight-bold" for="is_active">{{ __('Active / Published') }}</label>
                <small class="form-text text-muted">{{ __('Inactive products will be hidden from the public store.') }}</small>
              </div>

              <div class="custom-control custom-switch mb-2">
                <input
                  type="checkbox"
                  class="custom-control-input"
                  id="is_trending"
                  name="is_trending"
                  value="1"
                  @checked(old('is_trending'))
                >
                <label class="custom-control-label" for="is_trending">
                  <span class="badge badge-warning text-dark font-weight-bold mr-1">{{ __('Trending') }}</span>
                  {{ __('Mark as Trending') }}
                </label>
              </div>

              <div class="custom-control custom-switch mb-2">
                <input
                  type="checkbox"
                  class="custom-control-input"
                  id="is_new"
                  name="is_new"
                  value="1"
                  @checked(old('is_new', true))
                >
                <label class="custom-control-label" for="is_new">
                  <span class="badge badge-info font-weight-bold mr-1">{{ __('New') }}</span>
                  {{ __('Mark as New Arrival') }}
                </label>
              </div>

              <div class="custom-control custom-switch">
                <input
                  type="checkbox"
                  class="custom-control-input"
                  id="is_sale"
                  name="is_sale"
                  value="1"
                  @checked(old('is_sale'))
                >
                <label class="custom-control-label" for="is_sale">
                  <span class="badge badge-danger font-weight-bold mr-1">{{ __('Sale') }}</span>
                  {{ __('Mark as On Sale') }}
                </label>
              </div>
            </div>
          </div>

          <!-- Relations & Classification -->
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Classification & Relations') }}</h4>
            </div>
            <div class="card-body">
              <!-- Category -->
              <div class="form-group">
                <label for="category_id">{{ __('Product Category') }}</label>
                <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                  <option value="">-- {{ __('Select Category') }} --</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                      @if ($cat->level === 1)
                        📁 [L1 Section] {{ $cat->name }}
                      @elseif ($cat->level === 2)
                        &nbsp;&nbsp;&nbsp;&nbsp;↳ 📂 [L2 Category] {{ $cat->name }}
                      @else
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;↳ 🏷️ [L3 Item] {{ $cat->name }}
                      @endif
                    </option>
                  @endforeach
                </select>
                @error('category_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Brand -->
              <div class="form-group">
                <label for="brand_id">{{ __('Brand') }}</label>
                <select id="brand_id" name="brand_id" class="form-control @error('brand_id') is-invalid @enderror">
                  <option value="">-- {{ __('Select Brand') }} --</option>
                  @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>
                      {{ $brand->name }}
                    </option>
                  @endforeach
                </select>
                @error('brand_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Vendor -->
              <div class="form-group">
                <label for="vendor_id">{{ __('Vendor / Supplier') }}</label>
                <select id="vendor_id" name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror">
                  <option value="">-- {{ __('Select Vendor') }} --</option>
                  @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @selected(old('vendor_id') == $vendor->id)>
                      {{ $vendor->name }}
                    </option>
                  @endforeach
                </select>
                @error('vendor_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Size & Color -->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="size_id">{{ __('Size') }}</label>
                  <select id="size_id" name="size_id" class="form-control @error('size_id') is-invalid @enderror">
                    <option value="">-- {{ __('Size') }} --</option>
                    @foreach ($sizes as $size)
                      <option value="{{ $size->id }}" @selected(old('size_id') == $size->id)>
                        {{ $size->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('size_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group col-md-6">
                  <label for="color_id">{{ __('Color') }}</label>
                  <select id="color_id" name="color_id" class="form-control @error('color_id') is-invalid @enderror">
                    <option value="">-- {{ __('Color') }} --</option>
                    @foreach ($colors as $color)
                      <option value="{{ $color->id }}" @selected(old('color_id') == $color->id)>
                        {{ $color->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('color_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <!-- Thumbnail Image -->
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Product Thumbnail') }}</h4>
            </div>
            <div class="card-body">
              <div class="text-center mb-3">
                <div class="p-2 border rounded d-inline-block bg-light" style="width: 140px; height: 140px; line-height: 120px;">
                  <img id="thumb-preview-img" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                  <span id="thumb-preview-placeholder" class="text-muted small"><i class="fas fa-image fa-2x d-block mb-1"></i> {{ __('No Image') }}</span>
                </div>
              </div>

              <div class="form-group">
                <label for="thumbnail_file">{{ __('Upload File') }}</label>
                <div class="custom-file">
                  <input
                    type="file"
                    class="custom-file-input @error('thumbnail_file') is-invalid @enderror"
                    id="thumbnail_file"
                    name="thumbnail_file"
                    accept="image/*"
                  >
                  <label class="custom-file-label" for="thumbnail_file">{{ __('Choose file') }}</label>
                  @error('thumbnail_file')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <label for="thumbnail">{{ __('Or Asset / Image Path') }}</label>
                <input
                  type="text"
                  id="thumbnail"
                  name="thumbnail"
                  class="form-control form-control-sm @error('thumbnail') is-invalid @enderror"
                  placeholder="e.g. mantu/assets/img/products/1.jpg"
                  value="{{ old('thumbnail') }}"
                >
                @error('thumbnail')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <!-- Multiple Gallery Images -->
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Product Gallery Images') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group mb-2">
                <label for="product_images">{{ __('Upload Additional Images') }}</label>
                <div class="custom-file">
                  <input
                    type="file"
                    class="custom-file-input @error('product_images.*') is-invalid @enderror"
                    id="product_images"
                    name="product_images[]"
                    accept="image/*"
                    multiple
                  >
                  <label class="custom-file-label" for="product_images">{{ __('Choose multiple files') }}</label>
                  @error('product_images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <small class="form-text text-muted">{{ __('Select one or multiple images for the product gallery slider.') }}</small>
              </div>

              <!-- Live Preview of Selected Gallery Images -->
              <div id="gallery-preview-container" class="d-flex flex-wrap mt-3" style="gap: 10px;"></div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('products.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Save Product') }}</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection

