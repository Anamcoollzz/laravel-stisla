@extends('layouts.app')

@section('title', __('Modules › Product Category Details'))

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
        text: @json(__('Deleting this category will also delete all sub-categories under it!')),
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
      <a href="{{ route('product-categories.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Category Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-categories.index') }}">{{ __('Product Categories') }}</a></div>
      <div class="breadcrumb-item">{{ $category->name }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $category->name }}</h2>
    <p class="section-lead">
      {{ __('Full hierarchy, route mapping, and subcategories for this category.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-5">
        <div class="card card-primary">
          <div class="card-header">
            <h4>{{ __('Overview') }}</h4>
            <div class="card-header-action">
              @if ($category->level === 1)
                <span class="badge badge-primary">{{ __('Level 1 (Section)') }}</span>
              @elseif ($category->level === 2)
                <span class="badge badge-info">{{ __('Level 2 (Category)') }}</span>
              @else
                <span class="badge badge-warning">{{ __('Level 3 (Subcategory)') }}</span>
              @endif
            </div>
          </div>
          <div class="card-body">
            <div class="text-center mb-3">
              @if ($category->icon_url)
                <img src="{{ $category->icon_url }}" alt="{{ $category->name }}" style="width: 64px; height: 64px; object-fit: contain; padding: 8px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
              @else
                <div class="bg-light rounded p-3 d-inline-block text-muted">
                  <i class="fas fa-folder fa-3x"></i>
                </div>
              @endif
              <h5 class="mt-2 mb-0">{{ $category->name }}</h5>
              <code class="text-muted">{{ $category->slug }}</code>
            </div>

            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Hierarchy Path') }}</span>
                <span class="font-weight-bold text-right">
                  @if ($category->parent?->parent)
                    {{ $category->parent->parent->name }} › {{ $category->parent->name }} › {{ $category->name }}
                  @elseif ($category->parent)
                    {{ $category->parent->name }} › {{ $category->name }}
                  @else
                    {{ $category->name }}
                  @endif
                </span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Parent Category') }}</span>
                <span>
                  @if ($category->parent)
                    <a href="{{ route('product-categories.show', $category->parent) }}">
                      {{ $category->parent->name }}
                    </a>
                  @else
                    <span class="text-muted"><em>{{ __('None (Root Section)') }}</em></span>
                  @endif
                </span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Target Route') }}</span>
                <span>
                  @if ($category->route)
                    <code>{{ $category->route }}</code>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Custom URL') }}</span>
                <span>
                  @if ($category->url)
                    <code>{{ $category->url }}</code>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Resolved Link') }}</span>
                <span>
                  <a href="{{ $category->resolvedUrl() }}" target="_blank" class="badge badge-light border">
                    {{ $category->resolvedUrl() }} <i class="fas fa-external-link-alt ml-1"></i>
                  </a>
                </span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Sort Order') }}</span>
                <span class="badge badge-secondary">{{ $category->order }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Status') }}</span>
                @if ($category->is_active)
                  <span class="badge badge-success">{{ __('Active') }}</span>
                @else
                  <span class="badge badge-danger">{{ __('Inactive') }}</span>
                @endif
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted">{{ __('Subcategories') }}</span>
                <span class="badge badge-info">{{ $category->children->count() }}</span>
              </li>
            </ul>

            @if ($category->description)
              <div class="mt-3">
                <h6 class="text-muted font-weight-normal mb-1">{{ __('Description') }}</h6>
                <div class="p-2 bg-light rounded text-dark small">
                  {{ $category->description }}
                </div>
              </div>
            @endif
          </div>
          <div class="card-footer bg-whitesmoke d-flex justify-content-between align-items-center">
            <form action="{{ route('product-categories.destroy', $category) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              @can('product-categories.delete')
                <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                  <i class="fas fa-trash"></i> {{ __('Delete') }}
                </button>
              @endcan
            </form>

            <div class="btn-group">
              <form action="{{ route('product-categories.duplicate', $category) }}" method="POST" class="d-inline mr-1">
                @csrf
                @can('product-categories.create')
                  <button type="submit" class="btn btn-outline-secondary btn-sm" title="{{ __('Duplicate') }}">
                    <i class="fas fa-copy"></i> {{ __('Duplicate') }}
                  </button>
                @endcan
              </form>
              @can('product-categories.edit')
                <a href="{{ route('product-categories.edit', $category) }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-pencil-alt"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
        </div>
      </div>

      <!-- Children / Subcategories -->
      <div class="col-12 col-md-7">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Subcategories') }} ({{ $category->children->count() }})</h4>
            <div class="card-header-action">
              @if ($category->level < 3)
                @can('product-categories.create')
                  <a href="{{ route('product-categories.create', ['parent_id' => $category->id]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> {{ __('Add Subcategory') }}
                  </a>
                @endcan
              @endif
            </div>
          </div>
          <div class="card-body p-0">
            @if ($category->children->isEmpty())
              <div class="p-4 text-center text-muted">
                <i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i>
                <p class="mb-0">{{ __('No subcategories found under this category.') }}</p>
                @if ($category->level < 3)
                  @can('product-categories.create')
                    <a href="{{ route('product-categories.create', ['parent_id' => $category->id]) }}" class="btn btn-outline-primary btn-sm mt-2">
                      <i class="fas fa-plus mr-1"></i> {{ __('Add the first subcategory') }}
                    </a>
                  @endcan
                @endif
              </div>
            @else
              <div class="table-responsive">
                <table class="table table-striped table-md">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>{{ __('Name') }}</th>
                      <th>{{ __('Route / Link') }}</th>
                      <th>{{ __('Items') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th class="text-right">{{ __('Action') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($category->children->sortBy('order') as $child)
                      <tr>
                        <td>{{ $child->order }}</td>
                        <td>
                          <div class="d-flex align-items-center">
                            @if ($child->icon_url)
                              <img src="{{ $child->icon_url }}" alt="" class="mr-2" style="width: 24px; height: 24px; object-fit: contain;">
                            @endif
                            <div>
                              <a href="{{ route('product-categories.show', $child) }}" class="font-weight-bold">
                                {{ $child->name }}
                              </a>
                              <div class="small text-muted">{{ $child->slug }}</div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <code class="small">{{ $child->route ?? $child->url ?? '-' }}</code>
                        </td>
                        <td>
                          <span class="badge badge-light border">{{ $child->children->count() }}</span>
                        </td>
                        <td>
                          @if ($child->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                          @else
                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                          @endif
                        </td>
                        <td class="text-right">
                          <a href="{{ route('product-categories.show', $child) }}" class="btn btn-info btn-sm" title="{{ __('View') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('product-categories.edit')
                            <a href="{{ route('product-categories.edit', $child) }}" class="btn btn-warning btn-sm" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                        </td>
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

