@extends('layouts.pdf')

@section('title', __('User List'))
@section('total', $users->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th>{{ __('Full Name') }}</th>
        <th>{{ __('Email Address') }}</th>
        <th style="width: 140px;">{{ __('Role') }}</th>
        <th style="width: 130px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($users as $user)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $user->name }}</strong></td>
          <td>{{ $user->email }}</td>
          <td>
            @forelse ($user->roles as $role)
              <span class="badge badge-primary">{{ $role->name }}</span>
            @empty
              <span class="badge badge-secondary">{{ $user->role ?? 'user' }}</span>
            @endforelse
          </td>
          <td>{{ $user->created_at?->format('Y-m-d H:i:s') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

