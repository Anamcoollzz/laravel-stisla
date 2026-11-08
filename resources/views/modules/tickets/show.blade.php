@extends('layouts.app')

@section('title', __('Modules › Ticket Detail'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/chocolat/dist/css/chocolat.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
  <script src="{{ asset('node_modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this ticket!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('tickets.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Ticket Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('tickets.index') }}">{{ __('Tickets') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $ticket->title }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this ticket.') }}
    </p>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Ticket Detail') }}</h4>
            <div class="card-header-action">
              <a href="{{ route('tickets.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
              @can('tickets.edit')
                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
              @endcan
              @can('tickets.create')
                <form action="{{ route('tickets.duplicate', $ticket) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-warning btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Duplicate') }}"><i class="fas fa-copy"></i></button>
                </form>
              @endcan
              @can('tickets.delete')
                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-inline form-delete">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm btn-icon btn-delete" data-toggle="tooltip" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                </form>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <a href="#" class="btn btn-primary btn-icon icon-left btn-lg btn-block mb-4 d-md-none" data-toggle-slide="#ticket-items">
              <i class="fas fa-list"></i> {{ __('All Tickets') }}
            </a>
            <div class="tickets">
              <div class="ticket-items" id="ticket-items">
                @foreach ($tickets as $item)
                  <a href="{{ route('tickets.show', $item) }}" class="ticket-item text-decoration-none {{ $ticket->id === $item->id ? 'active' : '' }}">
                    <div class="ticket-title">
                      <h4>{{ $item->title }}</h4>
                    </div>
                    <div class="ticket-desc">
                      <div>{{ $item->user?->name ?? 'User' }}</div>
                      <div class="bullet"></div>
                      <div>{{ $item->created_at?->diffForHumans() }}</div>
                    </div>
                  </a>
                @endforeach
              </div>
              <div class="ticket-content">
                <div class="ticket-header">
                  <div class="ticket-sender-picture img-shadow">
                    <img src="{{ $ticket->user?->avatar_url ?? asset('assets/img/avatar/avatar-5.png') }}" alt="{{ $ticket->user?->name ?? 'User' }}">
                  </div>
                  <div class="ticket-detail">
                    <div class="ticket-title d-flex justify-content-between align-items-start">
                      <h4>{{ $ticket->title }}</h4>
                      <div class="ticket-actions text-nowrap">
                        <span class="badge {{ $ticket->statusBadgeClass() }} mr-1">{{ __(ucfirst(str_replace('_', ' ', $ticket->status))) }}</span>
                        <span class="badge {{ $ticket->priorityBadgeClass() }} mr-2">{{ __(ucfirst($ticket->priority)) }}</span>
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
                        @can('tickets.edit')
                          <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        @endcan
                        @can('tickets.delete')
                          <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-inline form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon btn-delete" data-toggle="tooltip" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                          </form>
                        @endcan
                      </div>
                    </div>
                    <div class="ticket-info">
                      <div class="font-weight-600">{{ $ticket->user?->name ?? 'User' }}</div>
                      <div class="bullet"></div>
                      <div class="text-primary font-weight-600">{{ $ticket->created_at?->diffForHumans() }}</div>
                    </div>
                  </div>
                </div>
                <div class="ticket-description">
                  <div class="ticket-body-text mb-4">
                    {!! nl2br(e($ticket->description)) !!}
                  </div>

                  @if ($ticket->replies->isNotEmpty())
                    <div class="ticket-replies mt-4">
                      <h6 class="font-weight-bold mb-3 text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('Replies') }} ({{ $ticket->replies->count() }})</h6>
                      @foreach ($ticket->replies as $reply)
                        <div class="media mb-3 p-3 rounded" style="background-color: #f8f9fa;">
                          <img class="mr-3 rounded-circle" width="40" height="40" src="{{ $reply->user?->avatar_url ?? asset('assets/img/avatar/avatar-1.png') }}" alt="{{ $reply->user?->name ?? 'User' }}" style="object-fit: cover;">
                          <div class="media-body">
                            <div class="d-flex justify-content-between align-items-center">
                              <h6 class="mt-0 mb-1 font-weight-600">{{ $reply->user?->name ?? 'User' }}</h6>
                              <small class="text-muted">{{ $reply->created_at?->diffForHumans() }}</small>
                            </div>
                            <div class="text-muted" style="font-size: 13.5px;">
                              {!! nl2br(e($reply->message)) !!}
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif

                  <div class="ticket-divider"></div>

                  <div class="ticket-form">
                    <form action="{{ route('tickets.reply', $ticket) }}" method="POST" class="needs-validation" novalidate="">
                      @csrf
                      <div class="form-group">
                        <textarea name="message" class="summernote form-control @error('message') is-invalid @enderror" placeholder="{{ __('Type a reply ...') }}" required>{{ old('message') }}</textarea>
                        @error('message')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                          {{ __('Reply') }}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

