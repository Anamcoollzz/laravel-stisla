@extends('layouts.app')

@section('title', __('Shop › Review Details'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('product-reviews.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Review Details') }} #{{ $productReview->id }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-reviews.index') }}">{{ __('Product Reviews') }}</a></div>
      <div class="breadcrumb-item">#{{ $productReview->id }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-4">
        <div class="card profile-widget">
          <div class="profile-widget-header text-center pt-4">
            <img alt="{{ $productReview->name }}" src="{{ $productReview->avatar_url }}" class="rounded-circle profile-widget-picture" style="width: 90px; height: 90px; object-fit: cover;">
          </div>
          <div class="profile-widget-description text-center">
            <div class="profile-widget-name font-weight-bold">{{ $productReview->name }}</div>
            <div class="text-muted small">{{ $productReview->email }}</div>
            @if ($productReview->user_id)
              <div class="mt-2">
                <span class="badge badge-primary"><i class="fas fa-user-check"></i> {{ __('Registered User') }}</span>
              </div>
            @else
              <div class="mt-2">
                <span class="badge badge-light">{{ __('Guest Reviewer') }}</span>
              </div>
            @endif
            <div class="mt-3 text-warning">
              @for ($i = 1; $i <= 5; $i++)
                <i class="{{ $i <= $productReview->rating ? 'fas' : 'far' }} fa-star fa-lg"></i>
              @endfor
            </div>
            <div class="mt-2 font-weight-bold">{{ $productReview->rating }}.0 / 5.0</div>
            <div class="mt-3">
              @if ($productReview->is_approved)
                <span class="badge badge-success">{{ __('Approved') }}</span>
              @else
                <span class="badge badge-warning">{{ __('Pending') }}</span>
              @endif
            </div>
          </div>
        </div>

        @if ($productReview->product)
          <div class="card">
            <div class="card-header">
              <h4>{{ __('Reviewed Product') }}</h4>
            </div>
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <img src="{{ $productReview->product->thumbnail_url }}" alt="{{ $productReview->product->title }}" class="rounded mr-3" width="60" height="60" style="object-fit: cover;">
                <div>
                  <h6 class="mb-1">{{ $productReview->product->title }}</h6>
                  <span class="text-muted small">{{ $productReview->product->category?->name ?? '-' }}</span>
                  <div class="font-weight-bold text-primary mt-1">Rp {{ number_format($productReview->product->final_price, 0, ',', '.') }}</div>
                </div>
              </div>
              <a href="{{ route('ecommerce.products.detail', $productReview->product->slug) }}" target="_blank" class="btn btn-outline-primary btn-block btn-sm">
                <i class="fas fa-external-link-alt"></i> {{ __('View Product on Store') }}
              </a>
            </div>
          </div>
        @endif
      </div>

      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Review Comment') }}</h4>
            <div class="card-header-action">
              @can('product-reviews.edit')
                <form action="{{ route('product-reviews.toggleApproval', $productReview) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn {{ $productReview->is_approved ? 'btn-warning' : 'btn-success' }}">
                    <i class="fas {{ $productReview->is_approved ? 'fa-times' : 'fa-check' }}"></i>
                    {{ $productReview->is_approved ? __('Unapprove Review') : __('Approve Review') }}
                  </button>
                </form>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <blockquote class="blockquote bg-light p-4 rounded mb-4" style="border-left: 4px solid #6777ef;">
              <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $productReview->comment }}</p>
            </blockquote>

            <table class="table table-bordered">
              <tr>
                <th style="width: 200px;">{{ __('Submitted At') }}</th>
                <td>{{ $productReview->created_at?->format('d M Y H:i:s') }} ({{ $productReview->created_at?->diffForHumans() }})</td>
              </tr>
              <tr>
                <th>{{ __('Last Updated') }}</th>
                <td>{{ $productReview->updated_at?->format('d M Y H:i:s') }}</td>
              </tr>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('product-reviews.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
            @can('product-reviews.delete')
              <form action="{{ route('product-reviews.destroy', $productReview) }}" method="POST" onsubmit="return confirm(@json(__('Are you sure you want to delete this review?')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash"></i> {{ __('Delete Review') }}
                </button>
              </form>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

