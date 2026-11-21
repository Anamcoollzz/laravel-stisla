@extends('layouts.app')

@section('title', __('Services'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $("#table-lp-services").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [2, 7] }
      ]
    });

    $('[data-toggle="tooltip"]').tooltip();

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Services') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Services') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-briefcase mr-2 text-primary"></i> {{ __('Daftar Layanan Rekrutmen') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          @can('lp-services.view')
            <a href="{{ route('lp-services.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('lp-services.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('lp-services.create')
            <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modalImportService">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
            <a href="{{ route('lp-services.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> {{ __('Tambah Layanan') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-lp-services">
            <thead>
              <tr>
                <th width="50">#</th>
                <th>{{ __('Urutan') }}</th>
                <th width="70">{{ __('Gambar') }}</th>
                <th>{{ __('Judul & Subjudul') }}</th>
                <th>{{ __('Category Key') }}</th>
                <th>{{ __('Ikon') }}</th>
                <th>{{ __('Status') }}</th>
                <th width="195">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($services as $service)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><span class="badge badge-light">{{ $service->order }}</span></td>
                  <td>
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="rounded" style="width: 56px; height: 42px; object-fit: cover;">
                  </td>
                  <td>
                    <div class="font-weight-bold">{{ $service->title }}</div>
                    @if ($service->subtitle)
                      <small class="text-muted">{{ $service->subtitle }}</small>
                    @endif
                  </td>
                  <td><span class="badge badge-info">{{ $service->category_key }}</span></td>
                  <td><code>{{ $service->icon }}</code></td>
                  <td>
                    @if ($service->is_active)
                      <span class="badge badge-success">{{ __('Aktif') }}</span>
                    @else
                      <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('lp-services.show', $service) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('lp-services.create')
                      <form action="{{ route('lp-services.duplicate', $service) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="{{ __('Duplikat') }}">
                          <i class="fas fa-copy"></i>
                        </button>
                      </form>
                    @endcan
                    @can('lp-services.edit')
                      <a href="{{ route('lp-services.edit', $service) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="{{ __('Edit') }}">
                        <i class="fas fa-edit"></i>
                      </a>
                    @endcan
                    @can('lp-services.delete')
                      <form action="{{ route('lp-services.destroy', $service) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete" data-toggle="tooltip" title="{{ __('Hapus') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('modals')
  @can('lp-services.create')
    <div class="modal fade" id="modalImportService" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('lp-services.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-file-upload mr-2 text-info"></i> {{ __('Import Layanan Rekrutmen') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Tutup') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-light border">
              {{ __('Gunakan template Excel yang disediakan agar format kolom sesuai. Kolom responsibilities, skills, dan benefits dipisah dengan tanda |.') }}
              <div class="mt-2">
                <a href="{{ route('lp-services.template') }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Template Excel') }}
                </a>
              </div>
            </div>
            <div class="form-group mb-0">
              <label for="importFile">{{ __('File Excel (.xlsx, .xls, .csv)') }} <span class="text-danger">*</span></label>
              <input type="file" name="file" id="importFile" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
          </div>
          <div class="modal-footer bg-whitesmoke">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Batal') }}</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> {{ __('Import') }}</button>
          </div>
        </form>
      </div>
    </div>
  @endcan
@endpush
