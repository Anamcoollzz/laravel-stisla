@extends('layouts.pdf')

@section('title', __('Field List'))
@section('total', $fields->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 40px;">#</th>
        <th style="width: 120px;">{{ __('Field Code') }}</th>
        <th>{{ __('Field Name') }}</th>
        <th>{{ __('Description') }}</th>
        <th style="width: 130px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($fields as $field)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><code>{{ $field->code }}</code></td>
          <td><strong>{{ $field->name }}</strong></td>
          <td>{{ $field->description ?? '-' }}</td>
          <td>{{ $field->created_at?->format('Y-m-d H:i:s') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

