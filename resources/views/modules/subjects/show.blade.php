@extends('layouts.app')

@section('title', __('Modules › Subject Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('subjects.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Subject Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('subjects.index') }}">{{ __('Subjects') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $subject->name }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this subject.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Subject Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="d-block">{{ __('ID') }}</label>
              <div>{{ $subject->id }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Subject Code') }}</label>
              <div>{{ $subject->code }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Subject Name') }}</label>
              <div>{{ $subject->name }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Description') }}</label>
              <div>{{ $subject->description ?? '-' }}</div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Created At') }}</label>
              <div>{{ $subject->created_at?->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="form-group mb-0">
              <label class="d-block">{{ __('Updated At') }}</label>
              <div>{{ $subject->updated_at?->format('Y-m-d H:i:s') }}</div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
            @can('subjects.edit')
              <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary btn-sm btn-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

