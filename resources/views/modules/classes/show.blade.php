@extends('layouts.app')

@section('title', __('Modules › Class Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('classes.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Class Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('index.html') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('classes.index') }}">{{ __('Classes') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $schoolClass->class_name }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this class.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Class Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="d-block">{{ __('ID') }}</label>
              <div>{{ $schoolClass->id }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Class Name') }}</label>
              <div>{{ $schoolClass->class_name }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Created At') }}</label>
              <div>{{ $schoolClass->created_at?->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="form-group mb-0">
              <label class="d-block">{{ __('Updated At') }}</label>
              <div>{{ $schoolClass->updated_at?->format('Y-m-d H:i:s') }}</div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('classes.index') }}" class="btn btn-secondary btn-sm mr-1">{{ __('Back') }}</a>
            <a href="{{ route('classes.edit', $schoolClass) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
