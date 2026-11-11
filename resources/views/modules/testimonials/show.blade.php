@extends('layouts.app')

@section('title', __('Shop › Testimonial Details'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('testimonials.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Testimonial Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('testimonials.index') }}">{{ __('Testimonials') }}</a></div>
      <div class="breadcrumb-item">{{ $testimonial->name }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-4">
        <div class="card profile-widget">
          <div class="profile-widget-header text-center pt-4">
            <img alt="{{ $testimonial->name }}" src="{{ $testimonial->image_url }}" class="rounded-circle profile-widget-picture" style="width: 100px; height: 100px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,.1);">
          </div>
          <div class="profile-widget-description text-center">
            <div class="profile-widget-name font-weight-bold">{{ $testimonial->name }}</div>
            <div class="text-muted">{{ $testimonial->designation ?: __('Customer') }}</div>
            <div class="text-warning mt-2">
              @for ($i = 1; $i <= 5; $i++)
                <i class="{{ $i <= $testimonial->rating ? 'fas' : 'far' }} fa-star"></i>
              @endfor
            </div>
            <div class="mt-3">
              @if ($testimonial->is_active)
                <span class="badge badge-success">{{ __('Active') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
              @endif
              <span class="badge badge-light ml-1">{{ __('Order:') }} {{ $testimonial->order }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-quote-left text-primary mr-2"></i> {{ __('Customer Review') }}</h4>
            <div class="card-header-action">
              @can('testimonials.edit')
                <a href="{{ route('testimonials.edit', $testimonial) }}" class="btn btn-warning">
                  <i class="fas fa-pencil-alt"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <blockquote class="blockquote mb-4 p-3 bg-light rounded" style="border-left: 4px solid #6777ef;">
              <p class="mb-0 font-italic text-dark">"{{ $testimonial->content }}"</p>
              <footer class="blockquote-footer mt-2">{{ $testimonial->name }} <cite title="{{ $testimonial->designation }}">({{ $testimonial->designation ?: __('Customer') }})</cite></footer>
            </blockquote>

            <table class="table table-bordered">
              <tr>
                <th style="width: 200px;">{{ __('Created At') }}</th>
                <td>{{ $testimonial->created_at?->format('d M Y H:i') }}</td>
              </tr>
              <tr>
                <th>{{ __('Last Updated') }}</th>
                <td>{{ $testimonial->updated_at?->format('d M Y H:i') }}</td>
              </tr>
            </table>
          </div>
          <div class="card-footer">
            <a href="{{ route('testimonials.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

