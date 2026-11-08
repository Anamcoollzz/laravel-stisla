@extends('layouts.pdf')

@section('title', __('Permission List'))
@section('total', $permissions->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th>{{ __('Permission Name') }}</th>
        <th style="width: 100px;">{{ __('Guard') }}</th>
        <th class="text-center" style="width: 130px;">{{ __('Assigned Roles') }}</th>
        <th style="width: 140px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($permissions as $permission)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><code>{{ $permission->name }}</code></td>
          <td>{{ $permission->guard_name }}</td>
          <td class="text-center"><span class="badge badge-primary">{{ $permission->roles_count }}</span></td>
          <td>{{ $permission->created_at?->format('Y-m-d H:i:s') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

