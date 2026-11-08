@extends('layouts.app')

@section('title', __('Shop › Banner Details'))

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
        text: @json(__('This banner will be deleted!')),
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
      <a href="{{ route('product-banners.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Banner Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-banners.index') }}">{{ __('Banners') }}</a></div>
      <div class="breadcrumb-item">{{ __('Details') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{!! strip_tags($banner->title, '<br>') !!}</h2>
    <p class="section-lead">{{ __('Banner information and live visual mockup.') }}</p>

    <!-- Visual Mockup Card -->
    <div class="row">
      <div class="col-12">
        <div class="card overflow-hidden">
          <div class="card-header bg-dark text-white">
            <h4><i class="fas fa-tv mr-2"></i> {{ __('Live Mockup Preview') }}</h4>
            <div class="card-header-action">
              <span class="badge badge-light">{{ ucfirst($banner->position) }} Slider</span>
            </div>
          </div>
          <div class="card-body p-0 position-relative text-white" style="background-image: url('{{ $banner->image_url }}'); background-size: cover; background-position: center; min-height: 380px; display: flex; align-items: center;">
            <div style="background: rgba(0, 0, 0, 0.45); position: absolute; inset: 0;"></div>
            <div class="p-5 position-relative" style="max-width: 650px;">
              @if ($banner->badge_label)
                <span class="badge badge-danger text-uppercase px-3 py-2 font-weight-bold mb-3 d-inline-block" style="font-size: 14px; letter-spacing: 1px;">
                  {!! strip_tags($banner->badge_label, '<br>') !!}
                </span>
              @endif
              <h1 class="font-weight-bold text-white mb-3" style="font-size: 2.5rem; line-height: 1.2;">
                {!! strip_tags($banner->title, '<br>') !!}
              </h1>
              @if ($banner->subtitle)
                <p class="text-white-50 mb-4" style="font-size: 1.15rem;">{{ $banner->subtitle }}</p>
              @endif
              @if ($banner->button_text)
                <a href="{{ $banner->button_url ?: '#' }}" target="_blank" class="btn btn-primary btn-lg px-4 font-weight-bold shadow">
                  {{ $banner->button_text }} <i class="fas fa-arrow-right ml-1"></i>
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Details Card -->
    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Banner Attributes') }}</h4>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <tr>
                  <th style="width: 200px;">{{ __('ID') }}</th>
                  <td><code>#{{ $banner->id }}</code></td>
                </tr>
                <tr>
                  <th>{{ __('Title') }}</th>
                  <td class="font-weight-bold">{!! strip_tags($banner->title, '<br>') !!}</td>
                </tr>
                <tr>
                  <th>{{ __('Subtitle') }}</th>
                  <td>{{ $banner->subtitle ?: '—' }}</td>
                </tr>
                <tr>
                  <th>{{ __('Badge Label') }}</th>
                  <td>
                    @if ($banner->badge_label)
                      <span class="badge badge-danger">{!! strip_tags($banner->badge_label, '<br>') !!}</span>
                    @else
                      <span class="text-muted">&mdash;</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Button') }}</th>
                  <td>
                    @if ($banner->button_text)
                      <span class="font-weight-bold">{{ $banner->button_text }}</span>
                      <small class="text-muted ml-2">({{ $banner->button_url ?: '#' }})</small>
                    @else
                      <span class="text-muted">&mdash;</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Position') }}</th>
                  <td><span class="badge badge-info">{{ ucfirst($banner->position) }}</span></td>
                </tr>
                <tr>
                  <th>{{ __('Display Order') }}</th>
                  <td><span class="badge badge-light border font-weight-bold">{{ $banner->order }}</span></td>
                </tr>
                <tr>
                  <th>{{ __('Status') }}</th>
                  <td>
                    @if ($banner->is_active)
                      <span class="badge badge-success">{{ __('Active') }}</span>
                    @else
                      <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Created At') }}</th>
                  <td>{{ $banner->created_at?->format('d M Y, H:i') ?: '—' }}</td>
                </tr>
                <tr>
                  <th>{{ __('Updated At') }}</th>
                  <td>{{ $banner->updated_at?->format('d M Y, H:i') ?: '—' }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="card-footer bg-whitesmoke text-right">
            <a href="{{ route('product-banners.index') }}" class="btn btn-secondary mr-2">{{ __('Back') }}</a>
            @can('product-banners.create')
              <form action="{{ route('product-banners.duplicate', $banner) }}" method="POST" class="d-inline mr-2">
                @csrf
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-copy mr-1"></i> {{ __('Duplicate') }}</button>
              </form>
            @endcan
            @can('product-banners.edit')
              <a href="{{ route('product-banners.edit', $banner) }}" class="btn btn-warning mr-2"><i class="fas fa-pencil-alt mr-1"></i> {{ __('Edit') }}</a>
            @endcan
            @can('product-banners.delete')
              <form action="{{ route('product-banners.destroy', $banner) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-delete"><i class="fas fa-trash mr-1"></i> {{ __('Delete') }}</button>
              </form>
            @endcan
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Image Asset') }}</h4>
          </div>
          <div class="card-body text-center">
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="img-fluid rounded border mb-3 shadow-sm">
            <p class="text-muted small">
              {{ $banner->image ? __('Custom uploaded storage image') : __('Default fallback template asset') }}
            </p>
            <a href="{{ $banner->image_url }}" target="_blank" class="btn btn-outline-info btn-sm">
              <i class="fas fa-external-link-alt mr-1"></i> {{ __('View Full Resolution') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

