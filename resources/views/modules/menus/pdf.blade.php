@extends('layouts.pdf')

@section('title', __('Menu List'))
@section('total', $menus->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th style="width: 50px;">{{ __('Order') }}</th>
        <th>{{ __('Menu Name') }}</th>
        <th style="width: 80px;">{{ __('Type') }}</th>
        <th>{{ __('Parent Menu') }}</th>
        <th>{{ __('Route / URL') }}</th>
        <th>{{ __('Permission') }}</th>
        <th class="text-center" style="width: 70px;">{{ __('Status') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($menus as $menu)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td class="text-center">{{ $menu->order }}</td>
          <td><strong>{{ $menu->name }}</strong></td>
          <td><span class="badge badge-light">{{ ucfirst($menu->type) }}</span></td>
          <td>{{ $menu->parent?->name ?? '-' }}</td>
          <td><code>{{ $menu->route ?: ($menu->url ?: '-') }}</code></td>
          <td>{{ $menu->permission ?? '-' }}</td>
          <td class="text-center">
            @if ($menu->is_active)
              <span class="badge badge-success">{{ __('Active') }}</span>
            @else
              <span class="badge badge-secondary">{{ __('Inactive') }}</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

