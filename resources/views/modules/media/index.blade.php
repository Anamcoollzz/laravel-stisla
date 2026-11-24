@extends('layouts.app')

@section('title', __('Media Management'))

@push('css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
  <style>
    .media-dropzone {
      border: 2px dashed #6777ef;
      border-radius: 10px;
      background: #f8f9ff;
      padding: 28px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .media-dropzone.dragover {
      background: #e9edff;
      border-color: #3f51b5;
      transform: scale(1.01);
    }
    .media-card {
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .media-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08) !important;
    }
    .media-thumb {
      height: 150px;
      background: #f4f6f9;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border-bottom: 1px solid #eef0f4;
      cursor: pointer;
    }
    .media-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .cropper-container-box {
      max-height: 420px;
      overflow: hidden;
      background: #111827;
    }
    .cropper-container-box img {
      max-width: 100%;
      display: block;
    }
  </style>
@endpush

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var activeCropper = null;
    var activeCropUrl = '';

    function formatBytes(bytes) {
      if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
      if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
      if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
      return bytes + ' B';
    }

    function showMediaSuccess(message) {
      swal({
        title: @json(__('Success')),
        text: message,
        icon: 'success',
        timer: 1800,
        buttons: false
      }).then(function() {
        window.location.reload();
      });
    }

    function uploadMediaFiles(fileList) {
      if (!fileList || !fileList.length) {
        return;
      }

      var formData = new FormData();
      for (var i = 0; i < fileList.length; i++) {
        formData.append('files[]', fileList[i]);
      }

      var folderId = $('#upload-folder-id').val();
      if (folderId) {
        formData.append('folder_id', folderId);
      }
      formData.append('visibility', $('#upload-visibility').val() || 'public');

      $('#upload-progress-wrap').removeClass('d-none');
      $('#upload-progress-bar').css('width', '0%').text('0%');

      $.ajax({
        url: @json(route('media.upload')),
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
          var xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener('progress', function(evt) {
            if (evt.lengthComputable) {
              var pct = Math.round((evt.loaded / evt.total) * 100);
              $('#upload-progress-bar').css('width', pct + '%').text(pct + '%');
            }
          }, false);
          return xhr;
        },
        success: function(res) {
          showMediaSuccess(res.message || @json(__('Upload complete.')));
        },
        error: function(xhr) {
          $('#upload-progress-wrap').addClass('d-none');
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : @json(__('Upload failed.'));
          swal(@json(__('Error')), msg, 'error');
        }
      });
    }

    var $dropzone = $('#media-dropzone');
    $dropzone.on('click', function(e) {
      if (!$(e.target).is('input, select, option, label')) {
        $('#media-file-input').trigger('click');
      }
    });
    $dropzone.on('dragover dragenter', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).addClass('dragover');
    });
    $dropzone.on('dragleave dragend drop', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass('dragover');
    });
    $dropzone.on('drop', function(e) {
      var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
      uploadMediaFiles(files);
    });
    $('#media-file-input').on('change', function() {
      uploadMediaFiles(this.files);
    });

    // Image Preview
    $(document).on('click', '.js-preview-media', function() {
      var $btn = $(this);
      var isImage = $btn.data('is-image') === 1 || $btn.data('is-image') === '1';
      var url = $btn.data('url');
      var name = $btn.data('name');
      var meta = $btn.data('meta');

      $('#preview-modal-title').text(name);
      $('#preview-modal-meta').text(meta);
      $('#preview-download-btn').attr('href', $btn.data('download-url'));

      if (isImage) {
        $('#preview-image-el').attr('src', url).removeClass('d-none');
        $('#preview-non-image').addClass('d-none');
      } else {
        $('#preview-image-el').addClass('d-none');
        $('#preview-non-image').removeClass('d-none');
      }
      $('#modal-preview-media').modal('show');
    });

    // Image Cropper
    $(document).on('click', '.js-crop-media', function() {
      var url = $(this).data('url');
      activeCropUrl = $(this).data('crop-url');
      $('#crop-target-img').attr('src', url);
      $('#modal-crop-media').modal('show');
    });

    $('#modal-crop-media').on('shown.bs.modal', function() {
      if (activeCropper) {
        activeCropper.destroy();
      }
      var imgEl = document.getElementById('crop-target-img');
      activeCropper = new Cropper(imgEl, {
        viewMode: 1,
        autoCropArea: 0.85,
        responsive: true
      });
    }).on('hidden.bs.modal', function() {
      if (activeCropper) {
        activeCropper.destroy();
        activeCropper = null;
      }
    });

    $(document).on('click', '.js-crop-ratio', function() {
      if (!activeCropper) return;
      var ratio = $(this).data('ratio');
      activeCropper.setAspectRatio(ratio === 'free' ? NaN : eval(ratio));
    });

    $(document).on('click', '.js-crop-rotate', function() {
      if (activeCropper) activeCropper.rotate(Number($(this).data('deg')));
    });

    $('#btn-save-crop').on('click', function() {
      if (!activeCropper || !activeCropUrl) return;
      var canvas = activeCropper.getCroppedCanvas({ maxWidth: 2400, maxHeight: 2400 });
      if (!canvas) return;

      var dataUrl = canvas.toDataURL('image/png');
      var $btn = $(this).prop('disabled', true);

      $.ajax({
        url: activeCropUrl,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        data: { cropped_image: dataUrl },
        success: function(res) {
          $('#modal-crop-media').modal('hide');
          showMediaSuccess(res.message);
        },
        error: function(xhr) {
          $btn.prop('disabled', false);
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : @json(__('Crop failed.'));
          swal(@json(__('Error')), msg, 'error');
        }
      });
    });

    // Image Compression
    $(document).on('click', '.js-compress-media', function() {
      $('#compress-form').attr('action', $(this).data('compress-url'));
      $('#compress-file-name').text($(this).data('name') + ' (' + formatBytes(Number($(this).data('size'))) + ')');
      $('#compress-quality').val(75);
      $('#compress-quality-label').text('75%');
      $('#modal-compress-media').modal('show');
    });

    $('#compress-quality').on('input change', function() {
      $('#compress-quality-label').text($(this).val() + '%');
    });

    $('#compress-form').on('submit', function(e) {
      e.preventDefault();
      var $form = $(this);
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        data: $form.serialize(),
        success: function(res) {
          $('#modal-compress-media').modal('hide');
          showMediaSuccess(res.message);
        },
        error: function(xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : @json(__('Compression failed.'));
          swal(@json(__('Error')), msg, 'error');
        }
      });
    });

    // Metadata & Access Permissions
    function toggleAllowedRolesField() {
      var isRole = $('#meta-visibility').val() === 'role';
      $('#meta-roles-group').toggleClass('d-none', !isRole);
    }
    $('#meta-visibility').on('change', toggleAllowedRolesField);

    $(document).on('click', '.js-metadata-media', function() {
      var d = $(this).data('file');
      $('#metadata-form').attr('action', $(this).data('update-url'));
      $('#meta-title').val(d.title || '');
      $('#meta-alt').val(d.alt_text || '');
      $('#meta-description').val(d.description || '');
      $('#meta-tags').val(Array.isArray(d.tags) ? d.tags.join(', ') : '');
      $('#meta-folder-id').val(d.folder_id || '');
      $('#meta-visibility').val(d.visibility || 'public');

      var allowed = Array.isArray(d.allowed_roles) ? d.allowed_roles : [];
      $('.js-meta-role-checkbox').each(function() {
        $(this).prop('checked', allowed.indexOf($(this).val()) !== -1);
      });

      toggleAllowedRolesField();
      $('#modal-metadata-media').modal('show');
    });

    $('#metadata-form').on('submit', function(e) {
      e.preventDefault();
      var $form = $(this);
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        data: $form.serialize(),
        success: function(res) {
          $('#modal-metadata-media').modal('hide');
          showMediaSuccess(res.message);
        },
        error: function(xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : @json(__('Failed to update metadata.'));
          swal(@json(__('Error')), msg, 'error');
        }
      });
    });

    // Versioning
    $(document).on('click', '.js-versions-media', function() {
      var versions = $(this).data('versions') || [];
      var fileId = $(this).data('id');
      $('#version-upload-form').attr('action', $(this).data('version-url'));
      $('#versions-file-name').text($(this).data('name'));

      var $list = $('#versions-list').empty();
      versions.forEach(function(v) {
        var restoreBtn = '';
        @can('media.edit')
          restoreBtn = '<button type="button" class="btn btn-xs btn-outline-primary js-restore-version" data-url="/app/media/' + fileId + '/versions/' + v.id + '/restore"><i class="fas fa-history mr-1"></i>' + @json(__('Restore')) + '</button>';
        @endcan
        $list.append(
          '<div class="list-group-item d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<div class="font-weight-bold">v' + v.version_number + ' <span class="badge badge-light ml-1">' + formatBytes(Number(v.size)) + '</span></div>' +
              '<div class="small text-muted">' + (v.note || '-') + '</div>' +
            '</div>' +
            restoreBtn +
          '</div>'
        );
      });

      $('#modal-versions-media').modal('show');
    });

    $(document).on('click', '.js-restore-version', function() {
      $.ajax({
        url: $(this).data('url'),
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        success: function(res) {
          $('#modal-versions-media').modal('hide');
          showMediaSuccess(res.message);
        }
      });
    });

    // File Sharing
    $(document).on('click', '.js-share-media', function() {
      $('#share-form').attr('action', $(this).data('share-url'));
      $('#share-file-name').text($(this).data('name'));
      var isShared = $(this).data('is-shared') === 1 || $(this).data('is-shared') === '1';
      $('#share-is-shared').prop('checked', isShared);
      $('#share-expires-at').val($(this).data('expires-at') || '');
      var link = $(this).data('public-url') || '';
      $('#share-link-input').val(link);
      $('#share-link-box').toggleClass('d-none', !isShared || !link);
      $('#modal-share-media').modal('show');
    });

    $('#share-form').on('submit', function(e) {
      e.preventDefault();
      var $form = $(this);
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        data: {
          is_shared: $('#share-is-shared').is(':checked') ? 1 : 0,
          share_expires_at: $('#share-expires-at').val() || null
        },
        success: function(res) {
          if (res.is_shared && res.share_url) {
            $('#share-link-input').val(res.share_url);
            $('#share-link-box').removeClass('d-none');
          } else {
            $('#share-link-box').addClass('d-none');
          }
          swal(@json(__('Success')), res.message, 'success');
        }
      });
    });

    $('#btn-copy-share-link').on('click', function() {
      var val = $('#share-link-input').val();
      if (!val) return;
      navigator.clipboard.writeText(val).then(function() {
        swal(@json(__('Copied!')), @json(__('Share link copied to clipboard.')), 'success');
      });
    });

    // Rename Folder
    $(document).on('click', '.js-rename-folder', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $('#rename-folder-form').attr('action', $(this).data('url'));
      $('#rename-folder-name').val($(this).data('name'));
      $('#modal-rename-folder').modal('show');
    });

    // Delete File / Folder
    $(document).on('click', '.js-delete-media, .js-delete-folder', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var url = $(this).data('url');
      var name = $(this).data('name');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Delete :name?', ['name' => '__NAME__'])).replace('__NAME__', name),
        icon: 'warning',
        buttons: [@json(__('Cancel')), @json(__('Delete'))],
        dangerMode: true
      }).then(function(willDelete) {
        if (!willDelete) return;
        $.ajax({
          url: url,
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
          data: { _method: 'DELETE' },
          success: function(res) {
            showMediaSuccess(res.message);
          }
        });
      });
    });

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Media Management') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Media Management') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Media Library & File Manager') }}</h2>
    <p class="section-lead">
      {{ __('Upload, organize folders, crop, compress, version, share, and control file access permissions.') }}
    </p>

    {{-- Storage Usage Monitoring --}}
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i class="fas fa-file-alt"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Total Files') }}</h4></div>
            <div class="card-body">{{ $storageStats['total_files'] }}</div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info"><i class="fas fa-folder"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Total Folders') }}</h4></div>
            <div class="card-body">{{ $storageStats['total_folders'] }}</div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning"><i class="fas fa-hdd"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Active Storage') }}</h4></div>
            <div class="card-body">{{ number_format($storageStats['active_size'] / 1048576, 2) }} MB</div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i class="fas fa-layer-group"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('With Versions') }}</h4></div>
            <div class="card-body">{{ number_format($storageStats['versions_size'] / 1048576, 2) }} MB</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
          <div class="font-weight-bold">
            <i class="fas fa-chart-pie mr-1 text-primary"></i> {{ __('Storage Usage Monitoring') }}
            <span class="text-muted font-weight-normal small ml-2">
              ({{ number_format($storageStats['versions_size'] / 1048576, 2) }} MB / 1,024 MB &mdash; {{ $storageStats['usage_percent'] }}%)
            </span>
          </div>
          <div class="small">
            <span class="badge badge-info mr-1">{{ __('Images') }}: {{ number_format($storageStats['image_size'] / 1048576, 2) }} MB</span>
            <span class="badge badge-primary mr-1">{{ __('Documents') }}: {{ number_format($storageStats['document_size'] / 1048576, 2) }} MB</span>
            <span class="badge badge-secondary">{{ __('Others') }}: {{ number_format($storageStats['other_size'] / 1048576, 2) }} MB</span>
          </div>
        </div>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $storageStats['usage_percent'] }}%;"></div>
        </div>
      </div>
    </div>

    {{-- Drag-and-drop & Multiple Upload --}}
    @can('media.create')
      <div class="card mb-4">
        <div class="card-body">
          <div id="media-dropzone" class="media-dropzone">
            <input type="hidden" id="upload-folder-id" value="{{ $currentFolder?->id }}">
            <input type="file" id="media-file-input" class="d-none" multiple>
            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
            <h6 class="mb-1">{{ __('Drag & drop files here, or click to select multiple files') }}</h6>
            <p class="text-muted small mb-3">{{ __('Supports images, PDFs, documents, archives (max 20MB per file).') }}</p>
            <div class="d-inline-flex align-items-center" style="gap: 8px;">
              <label class="small font-weight-bold mb-0">{{ __('Default Access') }}:</label>
              <select id="upload-visibility" class="form-control form-control-sm" style="width: auto;">
                <option value="public">{{ __('Public') }}</option>
                <option value="private">{{ __('Private') }}</option>
                <option value="role">{{ __('Role Based') }}</option>
              </select>
            </div>
            <div id="upload-progress-wrap" class="progress mt-3 d-none" style="height: 16px;">
              <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%;">0%</div>
            </div>
          </div>
        </div>
      </div>
    @endcan

    {{-- File Manager Card --}}
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
          <a href="{{ route('media.index') }}" class="btn btn-sm {{ !$currentFolder ? 'btn-primary' : 'btn-light' }}">
            <i class="fas fa-home mr-1"></i>{{ __('Root') }}
          </a>
          @foreach ($breadcrumbs as $crumb)
            <span class="text-muted">/</span>
            <a href="{{ route('media.index', ['folder' => $crumb->id]) }}" class="btn btn-sm {{ $currentFolder?->id === $crumb->id ? 'btn-primary' : 'btn-light' }}">
              {{ $crumb->name }}
            </a>
          @endforeach
        </div>
        <div class="card-header-action d-flex align-items-center flex-wrap" style="gap: 6px;">
          <form method="GET" action="{{ route('media.index') }}" class="form-inline" style="gap: 6px;">
            @if ($currentFolder)
              <input type="hidden" name="folder" value="{{ $currentFolder->id }}">
            @endif
            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="{{ __('Search files...') }}">
            <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
              <option value="">{{ __('All Types') }}</option>
              <option value="image" @selected($type === 'image')>{{ __('Images') }}</option>
              <option value="document" @selected($type === 'document')>{{ __('Documents') }}</option>
              <option value="other" @selected($type === 'other')>{{ __('Others') }}</option>
            </select>
            <select name="visibility" class="form-control form-control-sm" onchange="this.form.submit()">
              <option value="">{{ __('All Access') }}</option>
              <option value="public" @selected($visibility === 'public')>{{ __('Public') }}</option>
              <option value="private" @selected($visibility === 'private')>{{ __('Private') }}</option>
              <option value="role" @selected($visibility === 'role')>{{ __('Role Based') }}</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-search"></i></button>
          </form>
          @can('media.create')
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-create-folder">
              <i class="fas fa-folder-plus mr-1"></i>{{ __('New Folder') }}
            </button>
          @endcan
        </div>
      </div>

      <div class="card-body">
        {{-- Folders --}}
        @if ($folders->isNotEmpty())
          <h6 class="text-muted text-uppercase small font-weight-bold mb-2">{{ __('Folders') }}</h6>
          <div class="row mb-4">
            @foreach ($folders as $folder)
              <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="card border mb-0 shadow-none media-card">
                  <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('media.index', ['folder' => $folder->id]) }}" class="d-flex align-items-center text-dark text-decoration-none" style="gap: 10px; min-width: 0;">
                      <i class="fas fa-folder fa-2x text-warning"></i>
                      <div style="min-width: 0;">
                        <div class="font-weight-bold text-truncate">{{ $folder->name }}</div>
                        <div class="small text-muted">{{ $folder->files_count }} {{ __('files') }}</div>
                      </div>
                    </a>
                    <div class="d-flex" style="gap: 4px;">
                      @can('media.edit')
                        <button type="button" class="btn btn-xs btn-light js-rename-folder" data-url="{{ route('media-folders.update', $folder) }}" data-name="{{ $folder->name }}" title="{{ __('Rename') }}">
                          <i class="fas fa-pencil-alt"></i>
                        </button>
                      @endcan
                      @can('media.delete')
                        <button type="button" class="btn btn-xs btn-light text-danger js-delete-folder" data-url="{{ route('media-folders.destroy', $folder) }}" data-name="{{ $folder->name }}" title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      @endcan
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Files --}}
        <h6 class="text-muted text-uppercase small font-weight-bold mb-2">{{ __('Files') }}</h6>
        @if ($files->isEmpty())
          <div class="text-center text-muted py-5">
            <i class="fas fa-photo-video fa-3x mb-2 text-light"></i>
            <div>{{ __('No files found in this location.') }}</div>
          </div>
        @else
          <div class="row">
            @foreach ($files as $file)
              @php
                $visBadge = match ($file->visibility) {
                    'private' => 'badge-danger',
                    'role' => 'badge-warning text-dark',
                    default => 'badge-success',
                };
                $metaSummary = $file->formattedSize()
                    .($file->width ? " • {$file->width}x{$file->height}px" : '')
                    ." • v{$file->current_version}";
              @endphp
              <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-4">
                <div class="card border h-100 mb-0 media-card">
                  <div class="media-thumb js-preview-media"
                       data-is-image="{{ $file->isImage() ? 1 : 0 }}"
                       data-url="{{ $file->url() }}"
                       data-download-url="{{ route('media.download', $file) }}"
                       data-name="{{ $file->title ?: $file->name }}"
                       data-meta="{{ $metaSummary }}">
                    @if ($file->isImage())
                      <img src="{{ $file->url() }}" alt="{{ $file->alt_text ?: $file->name }}">
                    @else
                      <div class="text-center text-muted">
                        <i class="fas fa-file fa-3x mb-1 text-primary"></i>
                        <div class="small font-weight-bold text-uppercase">{{ $file->extension ?: 'FILE' }}</div>
                      </div>
                    @endif
                  </div>

                  <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                      <div class="font-weight-bold text-dark text-truncate" title="{{ $file->title ?: $file->name }}">
                        {{ $file->title ?: $file->name }}
                      </div>
                      <div class="small text-muted mb-2">{{ $metaSummary }}</div>
                      <div class="d-flex flex-wrap mb-2" style="gap: 4px;">
                        <span class="badge {{ $visBadge }}">{{ __(ucfirst($file->visibility)) }}</span>
                        <span class="badge badge-info">v{{ $file->current_version }}</span>
                        @if ($file->is_shared)
                          <span class="badge badge-primary"><i class="fas fa-share-alt mr-1"></i>{{ __('Shared') }}</span>
                        @endif
                      </div>
                    </div>

                    <div class="d-flex flex-wrap pt-2 border-top" style="gap: 4px;">
                      <button type="button"
                              class="btn btn-xs btn-outline-info js-preview-media"
                              data-is-image="{{ $file->isImage() ? 1 : 0 }}"
                              data-url="{{ $file->url() }}"
                              data-download-url="{{ route('media.download', $file) }}"
                              data-name="{{ $file->title ?: $file->name }}"
                              data-meta="{{ $metaSummary }}"
                              title="{{ __('Preview') }}">
                        <i class="fas fa-eye"></i>
                      </button>

                      @if ($file->isImage())
                        @can('media.edit')
                          <button type="button"
                                  class="btn btn-xs btn-outline-primary js-crop-media"
                                  data-url="{{ $file->url() }}"
                                  data-crop-url="{{ route('media.crop', $file) }}"
                                  title="{{ __('Crop Image') }}">
                            <i class="fas fa-crop-alt"></i>
                          </button>
                          <button type="button"
                                  class="btn btn-xs btn-outline-success js-compress-media"
                                  data-compress-url="{{ route('media.compress', $file) }}"
                                  data-name="{{ $file->name }}"
                                  data-size="{{ $file->size }}"
                                  title="{{ __('Compress Image') }}">
                            <i class="fas fa-compress-arrows-alt"></i>
                          </button>
                        @endcan
                      @endif

                      @can('media.edit')
                        <button type="button"
                                class="btn btn-xs btn-outline-secondary js-metadata-media"
                                data-update-url="{{ route('media.metadata.update', $file) }}"
                                data-file='@json($file)'
                                title="{{ __('Metadata & Permissions') }}">
                          <i class="fas fa-sliders-h"></i>
                        </button>
                        <button type="button"
                                class="btn btn-xs btn-outline-warning js-versions-media"
                                data-id="{{ $file->id }}"
                                data-name="{{ $file->name }}"
                                data-version-url="{{ route('media.versions.store', $file) }}"
                                data-versions='@json($file->versions)'
                                title="{{ __('Versions') }}">
                          <i class="fas fa-code-branch"></i>
                        </button>
                        <button type="button"
                                class="btn btn-xs btn-outline-primary js-share-media"
                                data-name="{{ $file->name }}"
                                data-share-url="{{ route('media.share', $file) }}"
                                data-is-shared="{{ $file->is_shared ? 1 : 0 }}"
                                data-public-url="{{ $file->share_token ? route('media.shared', $file->share_token) : '' }}"
                                data-expires-at="{{ $file->share_expires_at?->format('Y-m-d\TH:i') }}"
                                title="{{ __('Share') }}">
                          <i class="fas fa-share-alt"></i>
                        </button>
                      @endcan

                      <a href="{{ route('media.download', $file) }}" class="btn btn-xs btn-outline-dark" title="{{ __('Download') }}">
                        <i class="fas fa-download"></i>
                      </a>

                      @can('media.delete')
                        <button type="button"
                                class="btn btn-xs btn-outline-danger js-delete-media"
                                data-url="{{ route('media.destroy', $file) }}"
                                data-name="{{ $file->name }}"
                                title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      @endcan
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('modals')
  {{-- Create Folder Modal --}}
  <div class="modal fade" id="modal-create-folder" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form action="{{ route('media-folders.store') }}" method="POST" class="modal-content">
        @csrf
        <input type="hidden" name="parent_id" value="{{ $currentFolder?->id }}">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('New Folder') }}</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-0">
            <label>{{ __('Folder Name') }} *</label>
            <input type="text" name="name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Rename Folder Modal --}}
  <div class="modal fade" id="modal-rename-folder" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form id="rename-folder-form" method="POST" class="modal-content">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Rename Folder') }}</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-0">
            <label>{{ __('Folder Name') }} *</label>
            <input type="text" name="name" id="rename-folder-name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Image Preview Modal --}}
  <div class="modal fade" id="modal-preview-media" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="preview-modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body text-center">
          <img id="preview-image-el" src="" alt="" class="img-fluid rounded d-none" style="max-height: 70vh;">
          <div id="preview-non-image" class="py-5 text-muted d-none">
            <i class="fas fa-file fa-4x mb-2"></i>
            <div>{{ __('Preview is not available for this file type.') }}</div>
          </div>
          <div id="preview-modal-meta" class="small text-muted mt-3"></div>
        </div>
        <div class="modal-footer">
          <a id="preview-download-btn" href="#" class="btn btn-primary"><i class="fas fa-download mr-1"></i>{{ __('Download') }}</a>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Image Cropper Modal --}}
  <div class="modal fade" id="modal-crop-media" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-crop-alt mr-1"></i> {{ __('Image Cropper') }}</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="mb-2 d-flex flex-wrap" style="gap: 6px;">
            <button type="button" class="btn btn-xs btn-outline-primary js-crop-ratio" data-ratio="free">{{ __('Free') }}</button>
            <button type="button" class="btn btn-xs btn-outline-primary js-crop-ratio" data-ratio="1">1:1</button>
            <button type="button" class="btn btn-xs btn-outline-primary js-crop-ratio" data-ratio="16/9">16:9</button>
            <button type="button" class="btn btn-xs btn-outline-primary js-crop-ratio" data-ratio="4/3">4:3</button>
            <button type="button" class="btn btn-xs btn-outline-secondary js-crop-rotate" data-deg="-90"><i class="fas fa-undo"></i></button>
            <button type="button" class="btn btn-xs btn-outline-secondary js-crop-rotate" data-deg="90"><i class="fas fa-redo"></i></button>
          </div>
          <div class="cropper-container-box rounded">
            <img id="crop-target-img" src="" alt="Crop">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="button" class="btn btn-primary" id="btn-save-crop"><i class="fas fa-save mr-1"></i>{{ __('Crop & Save New Version') }}</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Image Compression Modal --}}
  <div class="modal fade" id="modal-compress-media" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form id="compress-form" method="POST" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-compress-arrows-alt mr-1"></i> {{ __('Image Compression') }}</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div id="compress-file-name" class="small text-muted mb-3"></div>
          <div class="form-group">
            <label>{{ __('Quality') }}: <strong id="compress-quality-label">75%</strong></label>
            <input type="range" name="quality" id="compress-quality" min="10" max="100" value="75" class="custom-range">
          </div>
          <div class="form-group">
            <label>{{ __('Max Width (px, optional)') }}</label>
            <input type="number" name="max_width" class="form-control" min="100" max="8000" placeholder="1920">
          </div>
          <div class="form-group mb-0">
            <label>{{ __('Output Format') }}</label>
            <select name="format" class="form-control">
              <option value="original">{{ __('Keep Original') }}</option>
              <option value="webp">WebP</option>
              <option value="jpg">JPG</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-compress mr-1"></i>{{ __('Compress & Save Version') }}</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Metadata & Access Permissions Modal --}}
  <div class="modal fade" id="modal-metadata-media" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form id="metadata-form" method="POST" class="modal-content">
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-sliders-h mr-1"></i> {{ __('Metadata & Access Permission') }}</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>{{ __('Title') }}</label>
            <input type="text" name="title" id="meta-title" class="form-control">
          </div>
          <div class="form-group">
            <label>{{ __('Alt Text') }}</label>
            <input type="text" name="alt_text" id="meta-alt" class="form-control">
          </div>
          <div class="form-group">
            <label>{{ __('Description') }}</label>
            <textarea name="description" id="meta-description" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>{{ __('Tags (comma separated)') }}</label>
            <input type="text" name="tags" id="meta-tags" class="form-control" placeholder="banner, promo, product">
          </div>
          <div class="form-group">
            <label>{{ __('Folder') }}</label>
            <select name="folder_id" id="meta-folder-id" class="form-control">
              <option value="">{{ __('Root') }}</option>
              @foreach ($allFolders as $f)
                <option value="{{ $f->id }}">{{ $f->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>{{ __('File Access Permission') }}</label>
            <select name="visibility" id="meta-visibility" class="form-control">
              <option value="public">{{ __('Public (All Logged-in Users)') }}</option>
              <option value="private">{{ __('Private (Uploader & Admin Only)') }}</option>
              <option value="role">{{ __('Specific Roles') }}</option>
            </select>
          </div>
          <div class="form-group mb-0 d-none" id="meta-roles-group">
            <label>{{ __('Allowed Roles') }}</label>
            <div class="d-flex flex-wrap" style="gap: 10px;">
              @foreach ($roles as $roleName)
                <label class="mb-0 small d-flex align-items-center" style="gap: 4px;">
                  <input type="checkbox" name="allowed_roles[]" value="{{ $roleName }}" class="js-meta-role-checkbox">
                  <span>{{ $roleName }}</span>
                </label>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
      </form>
    </div>
  </div>

  {{-- File Versioning Modal --}}
  <div class="modal fade" id="modal-versions-media" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-code-branch mr-1"></i> {{ __('File Versioning') }}</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div id="versions-file-name" class="font-weight-bold mb-2"></div>
          <div id="versions-list" class="list-group mb-3"></div>
          @can('media.edit')
            <form id="version-upload-form" method="POST" enctype="multipart/form-data" class="border-top pt-3">
              @csrf
              <h6 class="small font-weight-bold">{{ __('Upload New Version') }}</h6>
              <div class="form-group mb-2">
                <input type="file" name="file" class="form-control-file" required>
              </div>
              <div class="form-group mb-2">
                <input type="text" name="note" class="form-control form-control-sm" placeholder="{{ __('Version note (optional)') }}">
              </div>
              <button type="submit" class="btn btn-sm btn-primary">{{ __('Upload Version') }}</button>
            </form>
          @endcan
        </div>
      </div>
    </div>
  </div>

  {{-- File Sharing Modal --}}
  <div class="modal fade" id="modal-share-media" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form id="share-form" method="POST" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-share-alt mr-1"></i> {{ __('File Sharing') }}</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div id="share-file-name" class="font-weight-bold mb-3"></div>
          <div class="form-group">
            <label class="custom-switch pl-0">
              <input type="checkbox" id="share-is-shared" class="custom-switch-input">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">{{ __('Enable Public Share Link') }}</span>
            </label>
          </div>
          <div class="form-group">
            <label>{{ __('Expiration (Optional)') }}</label>
            <input type="datetime-local" id="share-expires-at" class="form-control">
          </div>
          <div id="share-link-box" class="d-none">
            <label>{{ __('Share URL') }}</label>
            <div class="input-group">
              <input type="text" id="share-link-input" class="form-control" readonly>
              <div class="input-group-append">
                <button type="button" class="btn btn-outline-primary" id="btn-copy-share-link"><i class="fas fa-copy"></i></button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save Sharing Settings') }}</button>
        </div>
      </form>
    </div>
  </div>
@endpush
