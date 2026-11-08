@extends('layouts.app')

@section('title', __('Modules › Field Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('fields.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Field Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('fields.index') }}">{{ __('Fields') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $field->name }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this field.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Field Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="d-block">{{ __('ID') }}</label>
              <div>{{ $field->id }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Field Code') }}</label>
              <div>{{ $field->code }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Field Name') }}</label>
              <div>{{ $field->name }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Description') }}</label>
              <div>{{ $field->description ?? '-' }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Created At') }}</label>
              <div>{{ $field->created_at?->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="form-group mb-0">
              <label class="d-block">{{ __('Updated At') }}</label>
              <div>{{ $field->updated_at?->format('Y-m-d H:i:s') }}</div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('fields.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
            @can('fields.edit')
              <a href="{{ route('fields.edit', $field) }}" class="btn btn-primary btn-sm btn-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
