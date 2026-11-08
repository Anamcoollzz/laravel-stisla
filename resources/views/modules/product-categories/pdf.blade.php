@extends('layouts.pdf')

@section('title', __('Product Categories Report'))
@section('total', $categories->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 30px;">#</th>
        <th class="text-center" style="width: 45px;">{{ __('Order') }}</th>
        <th class="text-center" style="width: 60px;">{{ __('Level') }}</th>
        <th>{{ __('Category Name') }}</th>
        <th>{{ __('Slug') }}</th>
        <th>{{ __('Parent Category') }}</th>
        <th>{{ __('Route / URL') }}</th>
        <th class="text-center" style="width: 60px;">{{ __('Status') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($categories as $cat)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td class="text-center">{{ $cat->order }}</td>
          <td class="text-center">
            @if ($cat->level === 1)
              <span class="badge badge-primary">L1</span>
            @elseif ($cat->level === 2)
              <span class="badge badge-info">L2</span>
            @else
              <span class="badge badge-warning">L3</span>
            @endif
          </td>
          <td>
            @if ($cat->level === 1)
              <strong>{{ $cat->name }}</strong>
            @elseif ($cat->level === 2)
              &nbsp;&nbsp;&nbsp;&nbsp;├── {{ $cat->name }}
            @else
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── {{ $cat->name }}
            @endif
          </td>
          <td><code>{{ $cat->slug }}</code></td>
          <td>{{ $cat->parent?->name ?? '-' }}</td>
          <td><code>{{ $cat->route ?: ($cat->url ?: '-') }}</code></td>
          <td class="text-center">
            @if ($cat->is_active)
              <span class="badge badge-success">{{ __('Active') }}</span>
            @else
              <span class="badge badge-secondary">{{ __('Inactive') }}</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">{{ __('No product categories found.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

