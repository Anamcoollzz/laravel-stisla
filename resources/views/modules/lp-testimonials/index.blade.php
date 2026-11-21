@extends('layouts.app')

@section('title', __('Testimonials'))

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

    $("#table-lp-testimonials").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [2, 8] }
      ]
    });

    $('[data-toggle="tooltip"]').tooltip();

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Testimonials') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Testimonials') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-quote-left mr-2 text-primary"></i> {{ __('Daftar Testimoni Klien') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          @can('lp-testimonials.view')
            <a href="{{ route('lp-testimonials.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('lp-testimonials.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('lp-testimonials.create')
            <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modalImportTestimonial">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
            <a href="{{ route('lp-testimonials.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> {{ __('Tambah Testimoni') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-lp-testimonials">
            <thead>
              <tr>
                <th width="50">#</th>
                <th>{{ __('Urutan') }}</th>
                <th width="60">{{ __('Avatar') }}</th>
                <th>{{ __('Nama & Lokasi') }}</th>
                <th>{{ __('Layanan') }}</th>
                <th>{{ __('Rating') }}</th>
                <th>{{ __('Testimoni') }}</th>
                <th>{{ __('Status') }}</th>
                <th width="195">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($testimonials as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><span class="badge badge-light">{{ $item->order }}</span></td>
                  <td>
                    <img src="{{ $item->avatar_url }}" alt="{{ $item->name }}" class="rounded-circle" style="width: 42px; height: 42px; object-fit: cover;">
                  </td>
                  <td>
                    <div class="font-weight-bold">{{ $item->name }}</div>
                    @if ($item->location)
                      <small class="text-muted">{{ $item->location }}</small>
                    @endif
                  </td>
                  <td><span class="badge badge-info">{{ $item->service ?: '-' }}</span></td>
                  <td>
                    <span class="text-warning">
                      @for ($i = 0; $i < $item->rating; $i++)
                        <i class="fas fa-star"></i>
                      @endfor
                    </span>
                  </td>
                  <td>{{ \Illuminate\Support\Str::limit($item->quote, 70) }}</td>
                  <td>
                    @if ($item->is_active)
                      <span class="badge badge-success">{{ __('Aktif') }}</span>
                    @else
                      <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('lp-testimonials.show', $item) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('lp-testimonials.create')
                      <form action="{{ route('lp-testimonials.duplicate', $item) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="{{ __('Duplikat') }}">
                          <i class="fas fa-copy"></i>
                        </button>
                      </form>
                    @endcan
                    @can('lp-testimonials.edit')
                      <a href="{{ route('lp-testimonials.edit', $item) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="{{ __('Edit') }}">
                        <i class="fas fa-edit"></i>
                      </a>
                    @endcan
                    @can('lp-testimonials.delete')
                      <form action="{{ route('lp-testimonials.destroy', $item) }}" method="POST" class="d-inline">
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
  @can('lp-testimonials.create')
    <div class="modal fade" id="modalImportTestimonial" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('lp-testimonials.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-file-upload mr-2 text-info"></i> {{ __('Import Testimoni Klien') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Tutup') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-light border">
              {{ __('Gunakan template Excel yang disediakan agar format kolom sesuai.') }}
              <div class="mt-2">
                <a href="{{ route('lp-testimonials.template') }}" class="btn btn-sm btn-outline-primary">
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
