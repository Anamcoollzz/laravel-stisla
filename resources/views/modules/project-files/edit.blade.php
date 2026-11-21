@extends('layouts.app')

@section('title', __('Edit Project File').': '.$name)

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('project-files.index', ['path' => $parent_path]) }}" class="btn btn-icon">
        <i class="fas fa-arrow-left"></i>
      </a>
    </div>
    <h1>{{ $name }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('project-files.index', ['path' => $parent_path]) }}">{{ __('Project File Editor') }}</a></div>
      <div class="breadcrumb-item">{{ $name }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <div class="mb-2 mb-md-0">
          <code class="font-weight-bold">{{ $path }}</code>
          <div class="text-muted small mt-1">
            <span class="badge badge-light mr-1">{{ strtoupper($extension ?: 'TEXT') }}</span>
            <span class="mr-2">{{ $line_count }} {{ __('lines') }}</span>
            <span class="mr-2">&bull;</span>
            <span class="mr-2">{{ $size_formatted }}</span>
            <span class="mr-2">&bull;</span>
            <span>{{ $last_modified }}</span>
          </div>
        </div>

        <div class="d-flex flex-wrap align-items-center">
          <div class="input-group input-group-sm mr-2 mb-1 mb-md-0" style="width: 190px;">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-palette"></i></span>
            </div>
            <select id="ace-theme-select" class="form-control">
              <optgroup label="{{ __('Dark Themes') }}">
                <option value="ace/theme/one_dark">One Dark</option>
                <option value="ace/theme/dracula">Dracula</option>
                <option value="ace/theme/monokai">Monokai</option>
                <option value="ace/theme/tomorrow_night">Tomorrow Night</option>
                <option value="ace/theme/nord_dark">Nord Dark</option>
                <option value="ace/theme/solarized_dark">Solarized Dark</option>
              </optgroup>
              <optgroup label="{{ __('Light Themes') }}">
                <option value="ace/theme/github">GitHub Light</option>
                <option value="ace/theme/chrome">Chrome</option>
                <option value="ace/theme/eclipse">Eclipse</option>
                <option value="ace/theme/solarized_light">Solarized Light</option>
              </optgroup>
            </select>
          </div>

          <div class="input-group input-group-sm mb-1 mb-md-0" style="width: 115px;">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-text-height"></i></span>
            </div>
            <select id="ace-fontsize-select" class="form-control">
              <option value="12px">12px</option>
              <option value="13px">13px</option>
              <option value="14px" selected>14px</option>
              <option value="15px">15px</option>
              <option value="16px">16px</option>
            </select>
          </div>
        </div>
      </div>

      <form id="form-project-file-editor" action="{{ route('project-files.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="file" value="{{ $path }}">

        <div class="card-body p-0">
          <textarea id="project-code-textarea"
                    name="content"
                    rows="26"
                    spellcheck="false"
                    class="form-control border-0 rounded-0 p-3"
                    style="height: 70vh; min-height: 520px; resize: vertical; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 13px; line-height: 1.55; background: #1e293b; color: #f8fafc;">{{ old('content', $content) }}</textarea>
          <div id="project-ace-editor" style="display: none; width: 100%; height: 70vh; min-height: 520px;"></div>
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center">
          <span class="text-muted small mb-2 mb-md-0">
            <i class="fas fa-keyboard mr-1"></i> {{ __('Shortcuts: Ctrl+S / Cmd+S to save, Ctrl+F / Cmd+F to find, Ctrl+H to replace.') }}
          </span>
          <div>
            <a href="{{ route('project-files.index', ['path' => $parent_path]) }}" class="btn btn-secondary mr-1">
              {{ __('Back to Explorer') }}
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i> {{ __('Save File') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.2/ace.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.2/ext-language_tools.min.js" crossorigin="anonymous"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    (function () {
      var textarea = document.getElementById('project-code-textarea');
      var editorEl = document.getElementById('project-ace-editor');
      var form = document.getElementById('form-project-file-editor');
      var themeSelect = document.getElementById('ace-theme-select');
      var fontSizeSelect = document.getElementById('ace-fontsize-select');

      if (!textarea || !editorEl || !form) {
        return;
      }

      if (typeof window.ace === 'undefined') {
        textarea.addEventListener('keydown', function (e) {
          if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            form.submit();
          }
        });
        return;
      }

      var ext = @json(strtolower($extension ?? ''));
      var filename = @json(strtolower($name ?? ''));
      var modeMap = {
        php: 'ace/mode/php',
        js: 'ace/mode/javascript',
        ts: 'ace/mode/typescript',
        jsx: 'ace/mode/jsx',
        tsx: 'ace/mode/tsx',
        vue: 'ace/mode/html',
        json: 'ace/mode/json',
        css: 'ace/mode/css',
        scss: 'ace/mode/scss',
        sass: 'ace/mode/sass',
        less: 'ace/mode/less',
        html: 'ace/mode/html',
        htm: 'ace/mode/html',
        xml: 'ace/mode/xml',
        svg: 'ace/mode/svg',
        md: 'ace/mode/markdown',
        sql: 'ace/mode/sql',
        yml: 'ace/mode/yaml',
        yaml: 'ace/mode/yaml',
        sh: 'ace/mode/sh',
        bat: 'ace/mode/batchfile',
        ini: 'ace/mode/ini',
        conf: 'ace/mode/ini',
        env: 'ace/mode/ini',
        txt: 'ace/mode/text',
        log: 'ace/mode/text'
      };

      var mode = modeMap[ext] || 'ace/mode/text';
      if (filename.endsWith('.blade.php')) {
        mode = 'ace/mode/php';
      } else if (filename.startsWith('.env')) {
        mode = 'ace/mode/ini';
      }

      ace.config.set('basePath', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.2/');

      textarea.style.display = 'none';
      editorEl.style.display = 'block';

      var savedTheme = localStorage.getItem('stisla_editor_theme') || 'ace/theme/one_dark';
      var savedFontSize = localStorage.getItem('stisla_editor_fontsize') || '14px';

      var editor = ace.edit(editorEl);
      editor.setValue(textarea.value, -1);
      editor.session.setMode(mode);
      editor.setTheme(savedTheme);
      editor.setOptions({
        fontSize: savedFontSize,
        showPrintMargin: false,
        tabSize: 4,
        useSoftTabs: true,
        wrap: false,
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: false,
        enableSnippets: false
      });

      if (themeSelect) {
        themeSelect.value = savedTheme;
        themeSelect.addEventListener('change', function () {
          editor.setTheme(this.value);
          localStorage.setItem('stisla_editor_theme', this.value);
        });
      }

      if (fontSizeSelect) {
        fontSizeSelect.value = savedFontSize;
        fontSizeSelect.addEventListener('change', function () {
          editor.setFontSize(this.value);
          localStorage.setItem('stisla_editor_fontsize', this.value);
        });
      }

      editor.commands.addCommand({
        name: 'saveFile',
        bindKey: { win: 'Ctrl-S', mac: 'Command-S' },
        exec: function () {
          textarea.value = editor.getValue();
          form.submit();
        }
      });

      form.addEventListener('submit', function () {
        textarea.value = editor.getValue();
      });
    })();
  </script>
@endpush
