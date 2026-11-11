@extends('layouts.app')

@section('title', __('Modules › Product Details'))

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
        text: @json(__('This action will permanently delete this product!')),
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
      <a href="{{ route('products.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Product Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Products') }}</a></div>
      <div class="breadcrumb-item">{{ $product->title }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <!-- Left Column: Image, Pricing, Specifications -->
      <div class="col-12 col-md-5">
        <div class="card card-primary">
          <div class="card-body">
            <div class="text-center mb-3">
              <img
                src="{{ $product->thumbnail_url }}"
                alt="{{ $product->title }}"
                class="img-fluid rounded shadow-sm border"
                style="max-height: 280px; object-fit: contain; width: 100%; background: #fdfdfd;"
                onerror="this.onerror=null; this.src='{{ asset('assets/img/products/product-1.jpg') }}';"
              >
            </div>

            @if ($product->images->isNotEmpty())
              <div class="mb-4">
                <label class="small text-muted font-weight-bold mb-2 d-block">{{ __('Gallery Images') }} ({{ $product->images->count() }})</label>
                <div class="d-flex flex-wrap" style="gap: 8px;">
                  @foreach ($product->images as $img)
                    <a href="{{ $img->image_url }}" target="_blank" class="border rounded p-1 bg-light d-inline-block" style="width: 60px; height: 60px;">
                      <img src="{{ $img->image_url }}" alt="Gallery image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                    </a>
                  @endforeach
                </div>
              </div>
            @endif

            <h4>{{ $product->title }}</h4>
            <div class="mb-3">
              <span class="badge badge-light border mr-1">SKU: <code>{{ $product->sku }}</code></span>
              @if ($product->is_active)
                <span class="badge badge-success mr-1">{{ __('Active') }}</span>
              @else
                <span class="badge badge-secondary mr-1">{{ __('Inactive') }}</span>
              @endif
              @if ($product->is_trending)
                <span class="badge badge-warning text-dark font-weight-bold mr-1">{{ __('Trending') }}</span>
              @endif
              @if ($product->is_new)
                <span class="badge badge-info font-weight-bold mr-1">{{ __('New Arrival') }}</span>
              @endif
              @if ($product->is_sale)
                <span class="badge badge-danger font-weight-bold mr-1">{{ __('On Sale') }}</span>
              @endif
              @if ($product->is_deal)
                <span class="badge badge-primary font-weight-bold mr-1">{{ __('Day of the Deals') }}</span>
              @endif
            </div>

            <!-- Price & Stock Summary -->
            <div class="p-3 bg-light rounded mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted">{{ __('Price') }}</span>
                @if ($product->discount_percent > 0)
                  <span class="text-muted" style="text-decoration: line-through;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @else
                  <span class="font-weight-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @endif
              </div>

              @if ($product->discount_percent > 0)
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="text-danger font-weight-bold">{{ __('Discount') }}</span>
                  <span class="badge badge-danger">-{{ (int) $product->discount_percent }}%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                  <span class="font-weight-bold">{{ __('Final Price') }}</span>
                  <h4 class="text-danger mb-0 font-weight-bold">Rp {{ number_format($product->final_price, 0, ',', '.') }}</h4>
                </div>
              @endif

              <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <span class="text-muted">{{ __('Stock Available') }}</span>
                @if ($product->stock <= 0)
                  <span class="badge badge-danger">{{ __('Out of Stock') }} (0)</span>
                @elseif ($product->stock <= 10)
                  <span class="badge badge-warning">{{ $product->stock }} ({{ __('Low Stock') }})</span>
                @else
                  <span class="badge badge-success">{{ $product->stock }} {{ __('Units') }}</span>
                @endif
              </div>
            </div>

            <!-- Specifications Table -->
            <h6 class="font-weight-bold mt-4 mb-2"><i class="fas fa-list-ul mr-1"></i> {{ __('Technical Specifications') }}</h6>
            @if (is_array($product->spesifications) && count($product->spesifications) > 0)
              <table class="table table-sm table-bordered">
                <tbody>
                  @foreach ($product->spesifications as $spec)
                    <tr>
                      <th class="bg-light" style="width: 40%;">{{ $spec['key'] ?? '' }}</th>
                      <td>{{ $spec['value'] ?? '' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p class="text-muted small"><em>{{ __('No specifications added.') }}</em></p>
            @endif

            <!-- Tags -->
            <h6 class="font-weight-bold mt-4 mb-2"><i class="fas fa-tags mr-1"></i> {{ __('Tags') }}</h6>
            <div>
              @if (is_array($product->tags) && count($product->tags) > 0)
                @foreach ($product->tags as $tag)
                  <span class="badge badge-info mr-1 mb-1">{{ $tag }}</span>
                @endforeach
              @else
                <span class="text-muted small"><em>{{ __('No tags.') }}</em></span>
              @endif
            </div>
          </div>
          <div class="card-footer bg-whitesmoke d-flex justify-content-between">
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              @can('products.delete')
                <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                  <i class="fas fa-trash"></i> {{ __('Delete') }}
                </button>
              @endcan
            </form>

            <div>
              @can('products.create')
                <form action="{{ route('products.duplicate', $product) }}" method="POST" class="d-inline mr-1">
                  @csrf
                  <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-copy"></i> {{ __('Duplicate') }}
                  </button>
                </form>
              @endcan
              @can('products.edit')
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-pencil-alt"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Classification, Vendor, Descriptions -->
      <div class="col-12 col-md-7">
        <!-- Classification -->
        <div class="card mb-4">
          <div class="card-header">
            <h4><i class="fas fa-sitemap mr-1"></i> {{ __('Classification & Attributes') }}</h4>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped mb-0">
              <tbody>
                <tr>
                  <th style="width: 30%;">{{ __('Category') }}</th>
                  <td>
                    @if ($product->category)
                      <span class="font-weight-bold">{{ $product->category->name }}</span>
                      @if ($product->category->parent)
                        <span class="text-muted small">({{ $product->category->parent->name }})</span>
                      @endif
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Brand') }}</th>
                  <td>
                    @if ($product->brand)
                      <span class="badge badge-primary">{{ $product->brand->name }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Size') }}</th>
                  <td>
                    @if ($product->size)
                      <span class="badge badge-light border">{{ $product->size->name }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Color') }}</th>
                  <td>
                    @if ($product->color)
                      @if ($product->color->color_code)
                        <span class="color-dot" style="background-color: {{ $product->color->color_code }}; display: inline-block; width: 14px; height: 14px; border-radius: 50%; border: 1px solid #ccc; vertical-align: middle;"></span>
                      @endif
                      <span>{{ $product->color->name }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Slug') }}</th>
                  <td><code>{{ $product->slug }}</code></td>
                </tr>
                <tr>
                  <th>{{ __('Created At') }}</th>
                  <td>{{ $product->created_at?->format('d M Y, H:i') }}</td>
                </tr>
                <tr>
                  <th>{{ __('Updated At') }}</th>
                  <td>{{ $product->updated_at?->format('d M Y, H:i') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Vendor / Supplier Card -->
        @if ($product->vendor)
          <div class="card mb-4 border-left border-primary" style="border-left-width: 4px !important;">
            <div class="card-header">
              <h4><i class="fas fa-truck mr-1"></i> {{ __('Vendor / Supplier Information') }}</h4>
            </div>
            <div class="card-body py-2">
              <h6 class="font-weight-bold text-dark">{{ $product->vendor->name }}</h6>
              <div class="row small text-muted">
                <div class="col-md-6 mb-1">
                  <i class="fas fa-phone mr-1"></i> {{ $product->vendor->phone ?? '-' }}
                </div>
                <div class="col-md-6 mb-1">
                  <i class="fas fa-envelope mr-1"></i> {{ $product->vendor->email ?? '-' }}
                </div>
                <div class="col-12 mb-1">
                  <i class="fas fa-map-marker-alt mr-1"></i> {{ $product->vendor->address ?? '-' }}
                </div>
              </div>
              @if ($product->vendor->description)
                <p class="small text-muted mt-2 mb-0 border-top pt-2">
                  {{ $product->vendor->description }}
                </p>
              @endif
            </div>
          </div>
        @endif

        <!-- Short Description HTML -->
        <div class="card mb-4">
          <div class="card-header">
            <h4><i class="fas fa-align-left mr-1"></i> {{ __('Short Description') }}</h4>
          </div>
          <div class="card-body">
            @if ($product->short_description)
              <div class="content-preview">
                {!! $product->short_description !!}
              </div>
            @else
              <p class="text-muted mb-0"><em>{{ __('No short description.') }}</em></p>
            @endif
          </div>
        </div>

        <!-- Detailed Description HTML -->
        <div class="card mb-4">
          <div class="card-header">
            <h4><i class="fas fa-file-alt mr-1"></i> {{ __('Detailed Description') }}</h4>
          </div>
          <div class="card-body">
            @if ($product->description_detail)
              <div class="content-preview">
                {!! $product->description_detail !!}
              </div>
            @else
              <p class="text-muted mb-0"><em>{{ __('No detailed description.') }}</em></p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

