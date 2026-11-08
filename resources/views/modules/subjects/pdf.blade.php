@extends('layouts.pdf')

@section('title', __('Subject List'))
@section('total', $subjects->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th style="width: 120px;">{{ __('Subject Code') }}</th>
        <th>{{ __('Subject Name') }}</th>
        <th>{{ __('Description') }}</th>
        <th style="width: 130px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($subjects as $subject)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><code>{{ $subject->code }}</code></td>
          <td><strong>{{ $subject->name }}</strong></td>
          <td>{{ $subject->description ?? '-' }}</td>
          <td>{{ $subject->created_at?->format('Y-m-d H:i:s') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

