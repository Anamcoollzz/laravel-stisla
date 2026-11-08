@extends('layouts.pdf')

@section('title', __('Role List'))
@section('total', $roles->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th>{{ __('Role Name') }}</th>
        <th style="width: 100px;">{{ __('Guard') }}</th>
        <th class="text-center" style="width: 130px;">{{ __('Permissions Count') }}</th>
        <th class="text-center" style="width: 110px;">{{ __('Users Count') }}</th>
        <th style="width: 130px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($roles as $role)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $role->name }}</strong></td>
          <td><code>{{ $role->guard_name }}</code></td>
          <td class="text-center"><span class="badge badge-info">{{ $role->permissions_count }}</span></td>
          <td class="text-center"><span class="badge badge-primary">{{ $role->users_count }}</span></td>
          <td>{{ $role->created_at?->format('Y-m-d H:i:s') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

