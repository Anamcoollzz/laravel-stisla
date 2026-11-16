@extends('layouts.app')

@section('title', __('Shop › Footer Category Details'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('footer-product-categories.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Footer Category Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('footer-product-categories.index') }}">{{ __('Footer Categories') }}</a></div>
      <div class="breadcrumb-item">{{ $footerCategory->display_name }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-tags text-primary mr-2"></i> {{ $footerCategory->display_name }}</h4>
            <div class="card-header-action">
              @can('footer-product-categories.edit')
                <a href="{{ route('footer-product-categories.edit', $footerCategory) }}" class="btn btn-warning">
                  <i class="fas fa-pencil-alt"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <tr>
                <th style="width: 220px;">{{ __('Display Label') }}</th>
                <td class="font-weight-600">{{ $footerCategory->display_name }}</td>
              </tr>
              <tr>
                <th>{{ __('Linked Product Category') }}</th>
                <td>{{ $footerCategory->category?->name ?? '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('Category Slug') }}</th>
                <td><code>{{ $footerCategory->category?->slug ?? '-' }}</code></td>
              </tr>
              <tr>
                <th>{{ __('Display Order') }}</th>
                <td>{{ $footerCategory->order }}</td>
              </tr>
              <tr>
                <th>{{ __('Status') }}</th>
                <td>
                  @if ($footerCategory->is_active)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                  @else
                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('Created At') }}</th>
                <td>{{ $footerCategory->created_at?->format('d M Y H:i') }}</td>
              </tr>
              <tr>
                <th>{{ __('Last Updated') }}</th>
                <td>{{ $footerCategory->updated_at?->format('d M Y H:i') }}</td>
              </tr>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('footer-product-categories.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
            @can('footer-product-categories.delete')
              <form action="{{ route('footer-product-categories.destroy', $footerCategory) }}" method="POST" onsubmit="return confirm('{{ __('Delete this footer category?') }}');">
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
