@extends('layouts.app')

@section('title', __('Shop › Brand Details'))

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
        text: @json(__('This brand will be deleted!')),
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
      <a href="{{ route('product-brands.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Brand Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-brands.index') }}">{{ __('Brands') }}</a></div>
      <div class="breadcrumb-item">{{ $brand->name }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-5">
        <div class="card card-primary">
          <div class="card-header">
            <h4>{{ $brand->name }}</h4>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between px-0">
                <span class="text-muted">{{ __('Slug') }}</span>
                <code>{{ $brand->slug }}</code>
              </li>
              <li class="list-group-item d-flex justify-content-between px-0">
                <span class="text-muted">{{ __('Associated Products') }}</span>
                <span class="badge badge-primary">{{ $brand->products->count() }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between px-0">
                <span class="text-muted">{{ __('Created At') }}</span>
                <span>{{ $brand->created_at?->format('d M Y, H:i') }}</span>
              </li>
            </ul>
          </div>
          <div class="card-footer bg-whitesmoke d-flex justify-content-between">
            <form action="{{ route('product-brands.destroy', $brand) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              @can('product-brands.delete')
                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"><i class="fas fa-trash"></i> {{ __('Delete') }}</button>
              @endcan
            </form>
            @can('product-brands.edit')
              <a href="{{ route('product-brands.edit', $brand) }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i> {{ __('Edit') }}</a>
            @endcan
          </div>
        </div>
      </div>

      <div class="col-12 col-md-7">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Products by this Brand') }} ({{ $brand->products->count() }})</h4>
          </div>
          <div class="card-body p-0">
            @if ($brand->products->isEmpty())
              <p class="p-4 text-center text-muted mb-0">{{ __('No products linked to this brand.') }}</p>
            @else
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead>
                    <tr>
                      <th>{{ __('Product') }}</th>
                      <th>{{ __('SKU') }}</th>
                      <th>{{ __('Price') }}</th>
                      <th>{{ __('Stock') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($brand->products as $prd)
                      <tr>
                        <td>
                          <a href="{{ route('products.show', $prd) }}" class="font-weight-bold">{{ $prd->title }}</a>
                        </td>
                        <td><code>{{ $prd->sku }}</code></td>
                        <td>Rp {{ number_format($prd->price, 0, ',', '.') }}</td>
                        <td>{{ $prd->stock }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

