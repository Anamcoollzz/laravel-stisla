@extends('layouts.app')

@section('title', __('Workers'))

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

    $("#table-lp-workers").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [1, 8] }
      ]
    });

    $('[data-toggle="tooltip"]').tooltip();

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Workers') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Workers') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-users mr-2 text-primary"></i> {{ __('Daftar Kandidat Pekerja') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          @can('lp-workers.view')
            <a href="{{ route('lp-workers.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('lp-workers.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('lp-workers.create')
            <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modalImportWorker">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
            <a href="{{ route('lp-workers.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> {{ __('Tambah Pekerja') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-lp-workers">
            <thead>
              <tr>
                <th width="45">#</th>
                <th width="60">{{ __('Foto') }}</th>
                <th>{{ __('Kode') }}</th>
                <th>{{ __('Nama & Asal') }}</th>
                <th>{{ __('Kategori') }}</th>
                <th>{{ __('Usia / Pengalaman') }}</th>
                <th>{{ __('Featured') }}</th>
                <th>{{ __('Status') }}</th>
                <th width="195">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($workers as $worker)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <img src="{{ $worker->photo_url }}" alt="{{ $worker->name }}" class="rounded" style="width: 48px; height: 56px; object-fit: cover;">
                  </td>
                  <td><code>{{ $worker->code }}</code></td>
                  <td>
                    <div class="font-weight-bold">{{ $worker->name }}</div>
                    @if ($worker->origin)
                      <small class="text-muted">{{ $worker->origin }}</small>
                    @endif
                  </td>
                  <td><span class="badge badge-info">{{ $worker->category }}</span></td>
                  <td>
                    <div>{{ $worker->age }} {{ __('thn') }}</div>
                    <small class="text-muted">{{ $worker->experience_label }}</small>
                  </td>
                  <td>
                    @if ($worker->is_featured)
                      <span class="badge badge-warning">{{ __('Featured') }}</span>
                    @else
                      <span class="badge badge-light">-</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $statusClass = match ($worker->status) {
                          'Available' => 'badge-success',
                          'In Training' => 'badge-warning',
                          default => 'badge-secondary',
                      };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $worker->status }}</span>
                  </td>
                  <td>
                    <a href="{{ route('lp-workers.show', $worker) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('lp-workers.create')
                      <form action="{{ route('lp-workers.duplicate', $worker) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="{{ __('Duplikat') }}">
                          <i class="fas fa-copy"></i>
                        </button>
                      </form>
                    @endcan
                    @can('lp-workers.edit')
                      <a href="{{ route('lp-workers.edit', $worker) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="{{ __('Edit') }}">
                        <i class="fas fa-edit"></i>
                      </a>
                    @endcan
                    @can('lp-workers.delete')
                      <form action="{{ route('lp-workers.destroy', $worker) }}" method="POST" class="d-inline">
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
  @can('lp-workers.create')
    <div class="modal fade" id="modalImportWorker" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('lp-workers.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-file-upload mr-2 text-info"></i> {{ __('Import Kandidat Pekerja') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Tutup') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-light border">
              {{ __('Gunakan template Excel yang disediakan agar format kolom sesuai.') }}
              <div class="mt-2">
                <a href="{{ route('lp-workers.template') }}" class="btn btn-sm btn-outline-primary">
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
