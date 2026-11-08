@extends('layouts.pdf')

@section('title', __('Class List'))
@section('total', $classes->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th>{{ __('Class Name') }}</th>
        <th style="width: 140px;">{{ __('Created At') }}</th>
        <th style="width: 140px;">{{ __('Updated At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($classes as $class)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $class->class_name }}</strong></td>
          <td>{{ $class->created_at?->format('Y-m-d H:i:s') }}</td>
          <td>{{ $class->updated_at?->format('Y-m-d H:i:s') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

