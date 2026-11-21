@extends('layouts.app')

@section('title', __('Edit File').': '.$name)

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $parent_path]) }}" class="btn btn-icon">
        <i class="fas fa-arrow-left"></i>
      </a>
    </div>
    <h1>{{ __('Edit File') }}: {{ $name }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $parent_path]) }}">{{ __('File Manager') }}</a></div>
      <div class="breadcrumb-item">{{ $name }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4>
          <span class="badge {{ $visibility === 'public' ? 'badge-success' : 'badge-dark' }} mr-2">{{ ucfirst($visibility) }}</span>
          <code>{{ $path }}</code>
        </h4>
        <span class="text-muted small">{{ $size_formatted }}</span>
      </div>
      <form action="{{ route('file-manager.content.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="disk" value="{{ $visibility }}">
        <input type="hidden" name="target_path" value="{{ $path }}">

        <div class="card-body">
          <div class="form-group mb-0">
            <textarea name="content" rows="20" class="form-control" style="font-family: monospace; font-size: 13px; line-height: 1.5;">{{ old('content', $content) }}</textarea>
          </div>
        </div>

        <div class="card-footer text-right">
          <a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $parent_path]) }}" class="btn btn-secondary mr-1">
            {{ __('Back') }}
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
