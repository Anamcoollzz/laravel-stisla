@extends('layouts.app')

@section('title', __('Modules › Edit Ticket'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/summernote/dist/summernote-bs4.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/summernote/dist/summernote-bs4.js') }}"></script>
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('tickets.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Ticket') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('tickets.index') }}">{{ __('Tickets') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Ticket') }}</h2>
    <p class="section-lead">
      {{ __('Update the ticket information below.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="card">
          <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Ticket Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="title"
                  name="title"
                  class="form-control @error('title') is-invalid @enderror"
                  value="{{ old('title', $ticket->title) }}"
                  required
                >
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @else
                  <div class="invalid-feedback">{{ __('Please fill in the title.') }}</div>
                @enderror
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="priority">{{ __('Priority') }} <span class="text-danger">*</span></label>
                  <select
                    id="priority"
                    name="priority"
                    class="form-control @error('priority') is-invalid @enderror"
                    required
                  >
                    <option value="low" @selected(old('priority', $ticket->priority) === 'low')>{{ __('Low') }}</option>
                    <option value="medium" @selected(old('priority', $ticket->priority) === 'medium')>{{ __('Medium') }}</option>
                    <option value="high" @selected(old('priority', $ticket->priority) === 'high')>{{ __('High') }}</option>
                  </select>
                  @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group col-md-6">
                  <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                  <select
                    id="status"
                    name="status"
                    class="form-control @error('status') is-invalid @enderror"
                    required
                  >
                    <option value="open" @selected(old('status', $ticket->status) === 'open')>{{ __('Open') }}</option>
                    <option value="in_progress" @selected(old('status', $ticket->status) === 'in_progress')>{{ __('In Progress') }}</option>
                    <option value="closed" @selected(old('status', $ticket->status) === 'closed')>{{ __('Closed') }}</option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <label for="description">{{ __('Description') }} <span class="text-danger">*</span></label>
                <textarea
                  id="description"
                  name="description"
                  class="summernote form-control @error('description') is-invalid @enderror"
                  required
                >{{ old('description', $ticket->description) }}</textarea>
                @error('description')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @else
                  <div class="invalid-feedback">{{ __('Please fill in the description.') }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('tickets.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

