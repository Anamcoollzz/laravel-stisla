@extends('layouts.app')

@section('title', __('File Manager'))

@section('content')
  <div class="section-header">
    <h1>{{ __('File Manager') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('File Manager') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Storage File Manager') }}</h2>
    <p class="section-lead">
      {{ __('Manage files and folders across Public (storage/app/public) and Private (storage/app/private) storage.') }}
    </p>

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon {{ $visibility === 'public' ? 'bg-success' : 'bg-dark' }}">
            <i class="fas {{ $visibility === 'public' ? 'fa-globe' : 'fa-lock' }}"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Active Storage') }}</h4>
            </div>
            <div class="card-body">
              {{ ucfirst($visibility) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-folder"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Folders') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total_folders'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-file"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Files') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total_files'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-hdd"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Current Folder Size') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total_size_formatted'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a class="nav-link {{ $visibility === 'public' ? 'active' : '' }}" href="{{ route('file-manager.index', ['disk' => 'public']) }}">
              <i class="fas fa-globe mr-1"></i> {{ __('Public') }} <small class="ml-1 opacity-75">(storage/app/public)</small>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $visibility === 'private' ? 'active' : '' }}" href="{{ route('file-manager.index', ['disk' => 'private']) }}">
              <i class="fas fa-lock mr-1"></i> {{ __('Private') }} <small class="ml-1 opacity-75">(storage/app/private)</small>
            </a>
          </li>
        </ul>

        <div class="card-header-action mt-2 mt-md-0">
          @can('file-manager.create')
            <button type="button" class="btn btn-primary mr-1" data-toggle="modal" data-target="#modalUploadFile">
              <i class="fas fa-upload mr-1"></i> {{ __('Upload Files') }}
            </button>
            <button type="button" class="btn btn-info mr-1" data-toggle="modal" data-target="#modalCreateFolder">
              <i class="fas fa-folder-plus mr-1"></i> {{ __('New Folder') }}
            </button>
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalCreateFile">
              <i class="fas fa-file-medical mr-1"></i> {{ __('New Text File') }}
            </button>
          @endcan
        </div>
      </div>

      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
          <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
            <ol class="breadcrumb mb-0 bg-light py-2 px-3">
              @foreach ($breadcrumbs as $crumb)
                @if ($loop->last)
                  <li class="breadcrumb-item active" aria-current="page">{{ $crumb['name'] }}</li>
                @else
                  <li class="breadcrumb-item">
                    <a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $crumb['path']]) }}">
                      {{ $crumb['name'] }}
                    </a>
                  </li>
                @endif
              @endforeach
            </ol>
          </nav>

          <form method="GET" action="{{ route('file-manager.index') }}" class="form-inline">
            <input type="hidden" name="disk" value="{{ $visibility }}">
            <input type="hidden" name="path" value="{{ $path }}">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="{{ __('Search in folder...') }}" value="{{ $search }}">
              <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                @if ($search !== '')
                  <a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $path]) }}" class="btn btn-secondary" title="{{ __('Reset') }}">
                    <i class="fas fa-times"></i>
                  </a>
                @endif
              </div>
            </div>
          </form>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Size') }}</th>
                <th>{{ __('Last Modified') }}</th>
                <th class="text-right" style="min-width: 220px;">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @if ($parent_path !== null)
                <tr>
                  <td colspan="5">
                    <a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $parent_path]) }}" class="font-weight-bold text-primary">
                      <i class="fas fa-level-up-alt mr-2"></i> .. ({{ __('Parent Directory') }})
                    </a>
                  </td>
                </tr>
              @endif

              @foreach ($directories as $dir)
                <tr>
                  <td>
                    <a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $dir['path']]) }}" class="font-weight-600 text-dark">
                      <i class="fas fa-folder text-warning mr-2"></i> {{ $dir['name'] }}
                    </a>
                  </td>
                  <td><span class="badge badge-warning">{{ __('Folder') }}</span></td>
                  <td>-</td>
                  <td>{{ $dir['last_modified'] }}</td>
                  <td class="text-right">
                    <a href="{{ route('file-manager.index', ['disk' => $visibility, 'path' => $dir['path']]) }}" class="btn btn-sm btn-info" title="{{ __('Open') }}">
                      <i class="fas fa-folder-open"></i>
                    </a>
                    @can('file-manager.edit')
                      <button type="button"
                              class="btn btn-sm btn-warning btn-rename-item"
                              data-target-path="{{ $dir['path'] }}"
                              data-current-name="{{ $dir['name'] }}"
                              title="{{ __('Rename') }}">
                        <i class="fas fa-edit"></i>
                      </button>
                    @endcan
                    @can('file-manager.delete')
                      <form action="{{ route('file-manager.destroy') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="disk" value="{{ $visibility }}">
                        <input type="hidden" name="path" value="{{ $path }}">
                        <input type="hidden" name="target_path" value="{{ $dir['path'] }}">
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-confirm-text="{{ __('Delete folder :name and all of its contents?', ['name' => $dir['name']]) }}"
                                title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @endforeach

              @foreach ($files as $file)
                <tr>
                  <td>
                    <i class="fas {{ $file['is_image'] ? 'fa-file-image text-success' : ($file['is_editable'] ? 'fa-file-code text-primary' : 'fa-file text-secondary') }} mr-2"></i>
                    <span class="font-weight-600">{{ $file['name'] }}</span>
                  </td>
                  <td>
                    <span class="badge badge-light">{{ strtoupper($file['extension'] ?: 'FILE') }}</span>
                  </td>
                  <td>{{ $file['size_formatted'] }}</td>
                  <td>{{ $file['last_modified'] }}</td>
                  <td class="text-right">
                    @if ($file['is_image'])
                      <button type="button"
                              class="btn btn-sm btn-secondary btn-preview-image"
                              data-name="{{ $file['name'] }}"
                              data-url="{{ route('file-manager.preview', ['disk' => $visibility, 'target_path' => $file['path']]) }}"
                              title="{{ __('Preview') }}">
                        <i class="fas fa-eye"></i>
                      </button>
                    @elseif ($file['public_url'])
                      <a href="{{ $file['public_url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-secondary" title="{{ __('Open Public URL') }}">
                        <i class="fas fa-external-link-alt"></i>
                      </a>
                    @endif

                    <a href="{{ route('file-manager.download', ['disk' => $visibility, 'target_path' => $file['path']]) }}" class="btn btn-sm btn-primary" title="{{ __('Download') }}">
                      <i class="fas fa-download"></i>
                    </a>

                    @can('file-manager.edit')
                      @if ($file['is_editable'])
                        <a href="{{ route('file-manager.edit', ['disk' => $visibility, 'target_path' => $file['path']]) }}" class="btn btn-sm btn-info" title="{{ __('Edit Content') }}">
                          <i class="fas fa-code"></i>
                        </a>
                      @endif
                      <button type="button"
                              class="btn btn-sm btn-warning btn-rename-item"
                              data-target-path="{{ $file['path'] }}"
                              data-current-name="{{ $file['name'] }}"
                              title="{{ __('Rename') }}">
                        <i class="fas fa-edit"></i>
                      </button>
                    @endcan

                    @can('file-manager.delete')
                      <form action="{{ route('file-manager.destroy') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="disk" value="{{ $visibility }}">
                        <input type="hidden" name="path" value="{{ $path }}">
                        <input type="hidden" name="target_path" value="{{ $file['path'] }}">
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-confirm-text="{{ __('Once deleted, :name cannot be recovered!', ['name' => $file['name']]) }}"
                                title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @endforeach

              @if (empty($directories) && empty($files))
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-50"></i>
                    {{ __('This folder is empty.') }}
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('modals')
  @can('file-manager.create')
    <div class="modal fade" id="modalUploadFile" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('file-manager.upload') }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          <input type="hidden" name="disk" value="{{ $visibility }}">
          <input type="hidden" name="path" value="{{ $path }}">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Upload Files to') }} {{ ucfirst($visibility) }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>{{ __('Select Files (max 20 MB each)') }}</label>
              <input type="file" name="files[]" class="form-control" multiple required>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="overwriteCheck" name="overwrite" value="1">
              <label class="custom-control-label" for="overwriteCheck">{{ __('Overwrite existing files with the same name') }}</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="modalCreateFolder" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('file-manager.folder.store') }}" method="POST" class="modal-content">
          @csrf
          <input type="hidden" name="disk" value="{{ $visibility }}">
          <input type="hidden" name="path" value="{{ $path }}">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Create New Folder') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group mb-0">
              <label>{{ __('Folder Name') }}</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. invoices" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-info">{{ __('Create Folder') }}</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="modalCreateFile" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('file-manager.file.store') }}" method="POST" class="modal-content">
          @csrf
          <input type="hidden" name="disk" value="{{ $visibility }}">
          <input type="hidden" name="path" value="{{ $path }}">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Create New Text File') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>{{ __('Filename') }}</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. notes.txt, config.json" required>
              <small class="form-text text-muted">{{ __('Supported extensions: txt, md, json, csv, log, xml, html, css, js, yml, ini, sql, svg') }}</small>
            </div>
            <div class="form-group mb-0">
              <label>{{ __('Content') }}</label>
              <textarea name="content" rows="8" class="form-control" style="font-family: monospace;"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Create File') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endcan

  @can('file-manager.edit')
    <div class="modal fade" id="modalRenameItem" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('file-manager.rename') }}" method="POST" class="modal-content">
          @csrf
          @method('PATCH')
          <input type="hidden" name="disk" value="{{ $visibility }}">
          <input type="hidden" name="path" value="{{ $path }}">
          <input type="hidden" name="target_path" id="rename-target-path">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Rename Item') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group mb-0">
              <label>{{ __('New Name') }}</label>
              <input type="text" name="new_name" id="rename-new-name" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-warning">{{ __('Save Changes') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endcan

  <div class="modal fade" id="modalPreviewImage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="preview-image-title">{{ __('Preview') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <img id="preview-image-img" src="" alt="" class="img-fluid rounded" style="max-height: 70vh;">
        </div>
      </div>
    </div>
  </div>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-rename-item', function () {
      $('#rename-target-path').val($(this).data('target-path'));
      $('#rename-new-name').val($(this).data('current-name'));
      $('#modalRenameItem').modal('show');
    });

    $(document).on('click', '.btn-preview-image', function () {
      $('#preview-image-title').text($(this).data('name'));
      $('#preview-image-img').attr('src', $(this).data('url')).attr('alt', $(this).data('name'));
      $('#modalPreviewImage').modal('show');
    });
  </script>
@endpush
