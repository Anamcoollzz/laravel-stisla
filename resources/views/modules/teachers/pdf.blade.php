@extends('layouts.pdf')

@section('title', __('Teacher List'))
@section('total', $teachers->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th style="width: 110px;">{{ __('NIP') }}</th>
        <th>{{ __('Teacher Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th style="width: 100px;">{{ __('Phone') }}</th>
        <th class="text-center" style="width: 70px;">{{ __('Gender') }}</th>
        <th>{{ __('Address') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($teachers as $teacher)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><code>{{ $teacher->nip }}</code></td>
          <td><strong>{{ $teacher->name }}</strong></td>
          <td>{{ $teacher->email }}</td>
          <td>{{ $teacher->phone ?? '-' }}</td>
          <td class="text-center">
            @if ($teacher->gender === 'L')
              <span class="badge badge-primary">{{ __('Male') }}</span>
            @else
              <span class="badge badge-info">{{ __('Female') }}</span>
            @endif
          </td>
          <td>{{ $teacher->address ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

