@extends('layouts.pdf')

@section('title', __('Student List'))
@section('total', $students->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th style="width: 100px;">{{ __('NIS') }}</th>
        <th>{{ __('Student Name') }}</th>
        <th style="width: 110px;">{{ __('Class') }}</th>
        <th>{{ __('Email') }}</th>
        <th style="width: 95px;">{{ __('Phone') }}</th>
        <th class="text-center" style="width: 70px;">{{ __('Gender') }}</th>
        <th style="width: 85px;">{{ __('Birth Date') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($students as $student)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><code>{{ $student->nis }}</code></td>
          <td><strong>{{ $student->name }}</strong></td>
          <td><span class="badge badge-light">{{ $student->schoolClass?->class_name ?? '-' }}</span></td>
          <td>{{ $student->email ?? '-' }}</td>
          <td>{{ $student->phone ?? '-' }}</td>
          <td class="text-center">
            @if ($student->gender === 'L')
              <span class="badge badge-primary">{{ __('Male') }}</span>
            @else
              <span class="badge badge-info">{{ __('Female') }}</span>
            @endif
          </td>
          <td>{{ $student->birth_date?->format('Y-m-d') ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

