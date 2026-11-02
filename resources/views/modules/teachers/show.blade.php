@extends('layouts.app')

@section('title', __('Modules › Teacher Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('teachers.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Teacher Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('teachers.index') }}">{{ __('Teachers') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $teacher->name }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this teacher.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Teacher Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('ID') }}</label>
                <div>{{ $teacher->id }}</div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('NIP') }}</label>
                <div>{{ $teacher->nip }}</div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Teacher Name') }}</label>
                <div>{{ $teacher->name }}</div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Gender') }}</label>
                <div>{{ $teacher->gender === 'L' ? __('Male') : __('Female') }}</div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Email') }}</label>
                <div>{{ $teacher->email }}</div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Phone') }}</label>
                <div>{{ $teacher->phone ?? '-' }}</div>
              </div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Address') }}</label>
              <div>{{ $teacher->address ?? '-' }}</div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-0">
                <label class="d-block">{{ __('Created At') }}</label>
                <div>{{ $teacher->created_at?->format('Y-m-d H:i:s') }}</div>
              </div>
              <div class="form-group col-md-6 mb-0">
                <label class="d-block">{{ __('Updated At') }}</label>
                <div>{{ $teacher->updated_at?->format('Y-m-d H:i:s') }}</div>
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('teachers.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary btn-sm btn-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

