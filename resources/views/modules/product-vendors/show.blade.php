@extends('layouts.app')

@section('title', __('Shop › Vendor Details'))

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
        text: @json(__('This vendor will be deleted!')),
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
      <a href="{{ route('product-vendors.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Vendor Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-vendors.index') }}">{{ __('Vendors') }}</a></div>
      <div class="breadcrumb-item">{{ $vendor->name }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-5">
        <div class="card card-primary">
          <div class="card-header">
            <h4>{{ $vendor->name }}</h4>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between px-0">
                <span class="text-muted">{{ __('Phone') }}</span>
                <span>{{ $vendor->phone ?? '-' }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between px-0">
                <span class="text-muted">{{ __('Email') }}</span>
                <span>{{ $vendor->email ?? '-' }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between px-0">
                <span class="text-muted">{{ __('Supplied Products') }}</span>
                <span class="badge badge-primary">{{ $vendor->products->count() }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between px-0">
                <span class="text-muted">{{ __('Created At') }}</span>
                <span>{{ $vendor->created_at?->format('d M Y, H:i') }}</span>
              </li>
            </ul>

            @if ($vendor->address)
              <div class="mt-3">
                <h6 class="small font-weight-bold text-muted mb-1">{{ __('Address') }}</h6>
                <p class="small text-muted">{{ $vendor->address }}</p>
              </div>
            @endif

            @if ($vendor->description)
              <div class="mt-3">
                <h6 class="small font-weight-bold text-muted mb-1">{{ __('Description') }}</h6>
                <p class="small text-muted mb-0">{{ $vendor->description }}</p>
              </div>
            @endif
          </div>
          <div class="card-footer bg-whitesmoke d-flex justify-content-between">
            <form action="{{ route('product-vendors.destroy', $vendor) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              @can('product-vendors.delete')
                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"><i class="fas fa-trash"></i> {{ __('Delete') }}</button>
              @endcan
            </form>
            @can('product-vendors.edit')
              <a href="{{ route('product-vendors.edit', $vendor) }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i> {{ __('Edit') }}</a>
            @endcan
          </div>
        </div>
      </div>

      <div class="col-12 col-md-7">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Products from this Vendor') }} ({{ $vendor->products->count() }})</h4>
          </div>
          <div class="card-body p-0">
            @if ($vendor->products->isEmpty())
              <p class="p-4 text-center text-muted mb-0">{{ __('No products supplied by this vendor yet.') }}</p>
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
                    @foreach ($vendor->products as $prd)
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

