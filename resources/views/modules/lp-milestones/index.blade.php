@extends('layouts.app')

@section('title', __('Milestones'))

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

    $("#table-lp-milestones").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [6] }
      ]
    });

    $('[data-toggle="tooltip"]').tooltip();

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Milestones') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Milestones') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-flag-checkered mr-2 text-primary"></i> {{ __('Daftar Milestone Perusahaan') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          @can('lp-milestones.view')
            <a href="{{ route('lp-milestones.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('lp-milestones.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('lp-milestones.create')
            <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modalImportLpMilestone">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
            <a href="{{ route('lp-milestones.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> {{ __('Tambah Milestone') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-lp-milestones">
            <thead>
              <tr>
                <th width="50">#</th>
                <th>{{ __('Urutan') }}</th>
                <th>{{ __('Tahun') }}</th>
                <th>{{ __('Judul') }}</th>
                <th>{{ __('Deskripsi') }}</th>
                <th>{{ __('Status') }}</th>
                <th width="195">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($milestones as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><span class="badge badge-light">{{ $item->order }}</span></td>
                  <td><span class="badge badge-primary">{{ $item->year }}</span></td>
                  <td class="font-weight-bold">{{ $item->title }}</td>
                  <td>{{ \Illuminate\Support\Str::limit($item->description ?? '-', 80) }}</td>
                  <td>
                    @if ($item->is_active)
                      <span class="badge badge-success">{{ __('Aktif') }}</span>
                    @else
                      <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('lp-milestones.show', $item) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('lp-milestones.create')
                      <form action="{{ route('lp-milestones.duplicate', $item) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="{{ __('Duplikat') }}">
                          <i class="fas fa-copy"></i>
                        </button>
                      </form>
                    @endcan
                    @can('lp-milestones.edit')
                      <a href="{{ route('lp-milestones.edit', $item) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="{{ __('Edit') }}">
                        <i class="fas fa-edit"></i>
                      </a>
                    @endcan
                    @can('lp-milestones.delete')
                      <form action="{{ route('lp-milestones.destroy', $item) }}" method="POST" class="d-inline">
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
  @can('lp-milestones.create')
    <div class="modal fade" id="modalImportLpMilestone" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('lp-milestones.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-file-upload mr-2 text-info"></i> {{ __('Import Milestone Perusahaan') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Tutup') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-light border">
              {{ __('Gunakan template Excel yang disediakan agar format kolom sesuai.') }}
              <div class="mt-2">
                <a href="{{ route('lp-milestones.template') }}" class="btn btn-sm btn-outline-primary">
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
