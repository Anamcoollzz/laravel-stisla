@extends('layouts.app')

@section('title', __('Shop › Service Details'))

@push('css')
  <style>
    .service-card-preview-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f4f6f9;
      border-radius: 12px;
    }
    .service-card-preview-icon svg {
      width: 50px;
      height: 50px;
    }
    .service-card-preview-icon svg path {
      fill: #3a4ee5;
    }
    .service-card-preview-icon img {
      width: 50px;
      height: 50px;
      object-fit: contain;
    }
  </style>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('store-services.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Service Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('store-services.index') }}">{{ __('Services') }}</a></div>
      <div class="breadcrumb-item">{{ $service->title }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-4">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Storefront Card Preview') }}</h4>
          </div>
          <div class="card-body text-center">
            <div class="service-card-preview-icon">
              @if ($service->icon_image_url)
                <img src="{{ $service->icon_image_url }}" alt="{{ $service->title }}">
              @elseif ($service->icon_svg)
                {!! $service->icon_svg !!}
              @else
                <i class="fas fa-concierge-bell fa-2x text-primary"></i>
              @endif
            </div>
            <h5 class="font-weight-bold text-dark">{{ $service->title }}</h5>
            <p class="text-muted mb-3">{{ $service->description }}</p>
            <div>
              @if ($service->is_active)
                <span class="badge badge-success">{{ __('Active') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
              @endif
              <span class="badge badge-light ml-1">{{ __('Order:') }} {{ $service->order }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-concierge-bell text-primary mr-2"></i> {{ __('Service Metadata') }}</h4>
            <div class="card-header-action">
              @can('store-services.edit')
                <a href="{{ route('store-services.edit', $service) }}" class="btn btn-warning">
                  <i class="fas fa-pencil-alt"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <tr>
                <th style="width: 200px;">{{ __('Title') }}</th>
                <td>{{ $service->title }}</td>
              </tr>
              <tr>
                <th>{{ __('Description') }}</th>
                <td>{{ $service->description }}</td>
              </tr>
              <tr>
                <th>{{ __('Icon Source') }}</th>
                <td>
                  @if ($service->icon_image_url)
                    <span class="badge badge-info">{{ __('Uploaded Image') }}</span>
                  @elseif ($service->icon_svg)
                    <span class="badge badge-primary">{{ __('Inline SVG') }}</span>
                  @else
                    <span class="badge badge-secondary">{{ __('None') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('Display Order') }}</th>
                <td>{{ $service->order }}</td>
              </tr>
              <tr>
                <th>{{ __('Status') }}</th>
                <td>
                  @if ($service->is_active)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                  @else
                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('Created At') }}</th>
                <td>{{ $service->created_at?->format('d M Y H:i') }}</td>
              </tr>
              <tr>
                <th>{{ __('Last Updated') }}</th>
                <td>{{ $service->updated_at?->format('d M Y H:i') }}</td>
              </tr>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('store-services.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
            @can('store-services.delete')
              <form action="{{ route('store-services.destroy', $service) }}" method="POST" onsubmit="return confirm('{{ __('Delete this service?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash"></i> {{ __('Delete') }}
                </button>
              </form>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
