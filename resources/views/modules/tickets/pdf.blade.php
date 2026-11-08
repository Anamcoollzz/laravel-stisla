@extends('layouts.pdf')

@section('title', __('Ticket List'))
@section('total', $tickets->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th>{{ __('Title') }}</th>
        <th style="width: 140px;">{{ __('Submitted By') }}</th>
        <th class="text-center" style="width: 80px;">{{ __('Priority') }}</th>
        <th class="text-center" style="width: 90px;">{{ __('Status') }}</th>
        <th class="text-center" style="width: 60px;">{{ __('Replies') }}</th>
        <th style="width: 120px;">{{ __('Created At') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($tickets as $ticket)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><strong>{{ $ticket->title }}</strong></td>
          <td>{{ $ticket->user?->name ?? 'User' }}</td>
          <td class="text-center">
            @if ($ticket->priority === 'high')
              <span class="badge badge-danger">{{ __('High') }}</span>
            @elseif ($ticket->priority === 'medium')
              <span class="badge badge-warning">{{ __('Medium') }}</span>
            @else
              <span class="badge badge-info">{{ __('Low') }}</span>
            @endif
          </td>
          <td class="text-center">
            @if ($ticket->status === 'open')
              <span class="badge badge-primary">{{ __('Open') }}</span>
            @elseif ($ticket->status === 'in_progress')
              <span class="badge badge-warning">{{ __('In Progress') }}</span>
            @else
              <span class="badge badge-success">{{ __('Closed') }}</span>
            @endif
          </td>
          <td class="text-center">{{ $ticket->replies->count() }}</td>
          <td>{{ $ticket->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection

