@extends('layouts.app')

@section('title', __('Project File Editor'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Project File Editor') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Project File Editor') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Project Root Explorer') }}</h2>
    <p class="section-lead">
      {{ __('Browse and edit files from project root') }}: <code>{{ $root_path }}</code>
    </p>

    <div class="row">
      <div class="col-lg-4 col-md-4 col-12">
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
      <div class="col-lg-4 col-md-4 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-file-code"></i>
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
      <div class="col-lg-4 col-md-4 col-12">
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
        <h4>{{ __('Directory Contents') }}</h4>
        <div class="card-header-action">
          @can('project-files.create')
            <button type="button" class="btn btn-primary mr-1" data-toggle="modal" data-target="#modalCreateProjectFile">
              <i class="fas fa-file-medical mr-1"></i> {{ __('New File') }}
            </button>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalCreateProjectFolder">
              <i class="fas fa-folder-plus mr-1"></i> {{ __('New Folder') }}
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
                    <a href="{{ route('project-files.index', ['path' => $crumb['path']]) }}">
                      {{ $crumb['name'] }}
                    </a>
                  </li>
                @endif
              @endforeach
            </ol>
          </nav>

          <form method="GET" action="{{ route('project-files.index') }}" class="form-inline">
            <input type="hidden" name="path" value="{{ $path }}">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="{{ __('Filter in folder...') }}" value="{{ $search }}">
              <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                @if ($search !== '')
                  <a href="{{ route('project-files.index', ['path' => $path]) }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                @endif
              </div>
            </div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Size') }}</th>
                <th>{{ __('Last Modified') }}</th>
                <th class="text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @if ($parent_path !== null)
                <tr>
                  <td colspan="5">
                    <a href="{{ route('project-files.index', ['path' => $parent_path]) }}" class="font-weight-bold text-primary">
                      <i class="fas fa-level-up-alt mr-2"></i> .. ({{ __('Parent Directory') }})
                    </a>
                  </td>
                </tr>
              @endif

              @foreach ($directories as $dir)
                <tr>
                  <td>
                    <a href="{{ route('project-files.index', ['path' => $dir['path']]) }}" class="font-weight-600 text-dark">
                      <i class="fas fa-folder text-warning mr-2"></i> {{ $dir['name'] }}
                    </a>
                  </td>
                  <td><span class="badge badge-warning">{{ __('Folder') }}</span></td>
                  <td>-</td>
                  <td>{{ $dir['last_modified'] }}</td>
                  <td class="text-right">
                    <a href="{{ route('project-files.index', ['path' => $dir['path']]) }}" class="btn btn-sm btn-info" title="{{ __('Open') }}">
                      <i class="fas fa-folder-open"></i>
                    </a>
                    @can('project-files.edit')
                      <button type="button"
                              class="btn btn-sm btn-warning btn-rename-project-item"
                              data-target-path="{{ $dir['path'] }}"
                              data-current-name="{{ $dir['name'] }}"
                              title="{{ __('Rename') }}">
                        <i class="fas fa-i-cursor"></i>
                      </button>
                    @endcan
                    @can('project-files.delete')
                      <form action="{{ route('project-files.destroy') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
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
                    @if ($file['is_editable'])
                      <a href="{{ route('project-files.edit', ['file' => $file['path']]) }}" class="font-weight-600 text-dark">
                        <i class="fas fa-file-code text-primary mr-2"></i> {{ $file['name'] }}
                      </a>
                    @else
                      <i class="fas fa-file text-secondary mr-2"></i>
                      <span class="font-weight-600">{{ $file['name'] }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-light">{{ strtoupper($file['extension'] ?: 'FILE') }}</span>
                  </td>
                  <td>{{ $file['size_formatted'] }}</td>
                  <td>{{ $file['last_modified'] }}</td>
                  <td class="text-right">
                    @can('project-files.edit')
                      @if ($file['is_editable'])
                        <a href="{{ route('project-files.edit', ['file' => $file['path']]) }}" class="btn btn-sm btn-primary" title="{{ __('Edit File') }}">
                          <i class="fas fa-code mr-1"></i> {{ __('Edit') }}
                        </a>
                      @endif
                      <button type="button"
                              class="btn btn-sm btn-warning btn-rename-project-item"
                              data-target-path="{{ $file['path'] }}"
                              data-current-name="{{ $file['name'] }}"
                              title="{{ __('Rename') }}">
                        <i class="fas fa-i-cursor"></i>
                      </button>
                    @endcan
                    @can('project-files.delete')
                      <form action="{{ route('project-files.destroy') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="path" value="{{ $path }}">
                        <input type="hidden" name="target_path" value="{{ $file['path'] }}">
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-confirm-text="{{ __('Delete file :name permanently?', ['name' => $file['name']]) }}"
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
                    {{ __('This directory is empty.') }}
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
  @can('project-files.create')
    <div class="modal fade" id="modalCreateProjectFile" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('project-files.file.store') }}" method="POST" class="modal-content">
          @csrf
          <input type="hidden" name="path" value="{{ $path }}">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Create New Project File') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>{{ __('Filename') }}</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. CustomHelper.php, notes.md" required>
            </div>
            <div class="form-group mb-0">
              <label>{{ __('Initial Content (Optional)') }}</label>
              <textarea name="content" rows="8" class="form-control" style="font-family: monospace;"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Create & Edit') }}</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="modalCreateProjectFolder" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('project-files.folder.store') }}" method="POST" class="modal-content">
          @csrf
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
              <input type="text" name="name" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-info">{{ __('Create Folder') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endcan

  @can('project-files.edit')
    <div class="modal fade" id="modalRenameProjectItem" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('project-files.rename') }}" method="POST" class="modal-content">
          @csrf
          @method('PATCH')
          <input type="hidden" name="path" value="{{ $path }}">
          <input type="hidden" name="target_path" id="project-rename-target-path">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Rename Item') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group mb-0">
              <label>{{ __('New Name') }}</label>
              <input type="text" name="new_name" id="project-rename-new-name" class="form-control" required>
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
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-rename-project-item', function () {
      $('#project-rename-target-path').val($(this).data('target-path'));
      $('#project-rename-new-name').val($(this).data('current-name'));
      $('#modalRenameProjectItem').modal('show');
    });
  </script>
@endpush
