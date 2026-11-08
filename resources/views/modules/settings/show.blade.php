@extends('layouts.app')

@section('title', __($definition['label']).' '.__('Settings'))

@php
  $hasCode = collect($definition['fields'])->contains(fn ($field) => $field['type'] === 'code');
  $canEdit = auth()->user()->can('settings.edit');
@endphp

@push('css')
  @if ($hasCode)
    <link rel="stylesheet" href="{{ asset('node_modules/codemirror/lib/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/codemirror/theme/duotone-dark.css') }}">
  @endif
@endpush

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
  @if ($hasCode)
    <script src="{{ asset('node_modules/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('node_modules/codemirror/mode/javascript/javascript.js') }}"></script>
  @endif
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

    @if ($hasCode)
      $(".codeeditor").each(function() {
        let editor = CodeMirror.fromTextArea(this, {
          lineNumbers: true,
          theme: "duotone-dark",
          mode: 'javascript',
          readOnly: {{ $canEdit ? 'false' : 'true' }},
          height: 200
        });
        editor.setSize("100%", 200);
      });
    @endif

    $(document).on('change', '.custom-file-input', function() {
      $(this).next('.custom-file-label').text(this.files.length ? this.files[0].name : @json(__('Choose File')));
    });

    $('#btn-reset').on('click', function(e) {
      e.preventDefault();
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('All settings in this section will be reset to default.')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willReset) => {
        if (willReset) {
          $('#reset-form').trigger('submit');
        }
      });
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('settings.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __($definition['label']) }} {{ __('Settings') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item active"><a href="{{ route('settings.index') }}">{{ __('Settings') }}</a></div>
      <div class="breadcrumb-item">{{ __($definition['label']) }} {{ __('Settings') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('All About :name Settings', ['name' => __($definition['label'])]) }}</h2>
    <p class="section-lead">
      {{ __('You can adjust all :name settings here', ['name' => strtolower(__($definition['label']))]) }}
    </p>

    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Jump To') }}</h4>
          </div>
          <div class="card-body">
            <ul class="nav nav-pills flex-column">
              @foreach ($groups as $key => $group)
                <li class="nav-item"><a href="{{ route('settings.show', $key) }}" @class(['nav-link', 'active' => $key === $currentGroup])>{{ __($group['label']) }}</a></li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <form action="{{ route('settings.update', $currentGroup) }}" method="POST" enctype="multipart/form-data" id="setting-form">
          @csrf
          @method('PUT')
          <div class="card" id="settings-card">
            <div class="card-header">
              <h4>{{ __($definition['label']) }} {{ __('Settings') }}</h4>
            </div>
            <div class="card-body">
              <p class="text-muted">{{ __($definition['description']) }}</p>

              @foreach ($definition['fields'] as $key => $field)
                @php
                  $value = old($key, $values[$key] ?? ($field['default'] ?? null));
                @endphp
                <div class="form-group row {{ in_array($field['type'], ['code', 'textarea']) ? '' : 'align-items-center' }}">
                  <label for="{{ $key }}" class="form-control-label col-sm-3 text-md-right {{ $field['type'] === 'code' ? 'mt-3' : '' }}">{{ __($field['label']) }}</label>
                  <div class="col-sm-6 col-md-9">
                    @switch($field['type'])
                      @case('textarea')
                        <textarea id="{{ $key }}" name="{{ $key }}" class="form-control @error($key) is-invalid @enderror" style="height: {{ $field['height'] ?? '100px' }}" @disabled(! $canEdit)>{{ $value }}</textarea>
                        @break

                      @case('code')
                        <textarea id="{{ $key }}" name="{{ $key }}" class="form-control codeeditor @error($key) is-invalid @enderror">{{ $value }}</textarea>
                        @break

                      @case('select')
                        <select id="{{ $key }}" name="{{ $key }}" class="form-control @error($key) is-invalid @enderror" @disabled(! $canEdit)>
                          @foreach ($field['options'] as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ __($optionLabel) }}</option>
                          @endforeach
                        </select>
                        @break

                      @case('switch')
                        <label class="custom-switch mt-2 pl-0">
                          <input type="checkbox" id="{{ $key }}" name="{{ $key }}" value="1" class="custom-switch-input" @checked((string) $value === '1') @disabled(! $canEdit)>
                          <span class="custom-switch-indicator"></span>
                        </label>
                        @break

                      @case('image')
                        @if (! empty($values[$key]))
                          <div class="mb-2">
                            <img src="{{ Storage::disk('public')->url($values[$key]) }}" alt="{{ __($field['label']) }}" class="img-thumbnail" style="max-height: 80px">
                            @if ($canEdit)
                              <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" name="remove_{{ $key }}" value="1" class="custom-control-input" id="remove_{{ $key }}">
                                <label class="custom-control-label" for="remove_{{ $key }}">{{ __('Remove image') }}</label>
                              </div>
                            @endif
                          </div>
                        @endif
                        @if ($canEdit)
                          <div class="custom-file">
                            <input type="file" id="{{ $key }}" name="{{ $key }}" accept="image/*" class="custom-file-input @error($key) is-invalid @enderror">
                            <label class="custom-file-label" for="{{ $key }}">{{ __('Choose File') }}</label>
                          </div>
                        @endif
                        @break

                      @default
                        <input type="{{ $field['type'] }}" id="{{ $key }}" name="{{ $key }}" class="form-control @error($key) is-invalid @enderror" value="{{ $field['type'] === 'password' ? '' : $value }}" @if ($field['type'] === 'password') autocomplete="new-password" placeholder="{{ ! empty($values[$key]) ? '••••••••' : '' }}" @endif @disabled(! $canEdit)>
                    @endswitch

                    @error($key)
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @if (! empty($field['help']))
                      <div class="form-text text-muted">{{ __($field['help']) }}</div>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
            @can('settings.edit')
              <div class="card-footer bg-whitesmoke text-md-right">
                <button type="submit" class="btn btn-primary" id="save-btn">{{ __('Save Changes') }}</button>
                <button type="button" class="btn btn-secondary" id="btn-reset">{{ __('Reset') }}</button>
              </div>
            @endcan
          </div>
        </form>
        @can('settings.edit')
          <form action="{{ route('settings.reset', $currentGroup) }}" method="POST" id="reset-form" class="d-none">
            @csrf
            @method('DELETE')
          </form>
        @endcan
      </div>
    </div>
  </div>
@endsection
