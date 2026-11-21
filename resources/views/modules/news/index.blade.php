@extends('layouts.app')

@section('title', __('News Articles'))

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

    $("#table-news").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [1, 7] }
      ]
    });

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('News Articles') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('News') }}</div>
      <div class="breadcrumb-item">{{ __('Articles') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4><i class="fas fa-newspaper mr-2 text-primary"></i> {{ __('Daftar Berita') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          <form method="GET" action="{{ route('admin.news.index') }}" class="form-inline mr-2">
            <select name="news_category_id" class="form-control form-control-sm mr-1" onchange="this.form.submit()">
              <option value="">{{ __('Semua Kategori') }}</option>
              @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) $selectedCategory === (string) $cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
              <option value="">{{ __('Semua Status') }}</option>
              <option value="draft" @selected($selectedStatus === 'draft')>Draft</option>
              <option value="published" @selected($selectedStatus === 'published')>Published</option>
              <option value="archived" @selected($selectedStatus === 'archived')>Archived</option>
            </select>
          </form>
          @can('news.view')
            <a href="{{ route('admin.news.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('admin.news.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('news.create')
            <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modalImportNews">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> {{ __('Tambah Berita') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-news">
            <thead>
              <tr>
                <th class="text-center" style="width: 45px;">#</th>
                <th style="width: 70px;">{{ __('Thumbnail') }}</th>
                <th>{{ __('Judul & Slug') }}</th>
                <th>{{ __('Kategori') }}</th>
                <th>{{ __('Publisher') }}</th>
                <th>{{ __('Published At') }}</th>
                <th class="text-center">{{ __('Status') }}</th>
                <th class="text-center" style="width: 140px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($newsList as $item)
                <tr>
                  <td class="text-center">{{ $loop->iteration }}</td>
                  <td>
                    @if ($item->thumbnail_mini_url || $item->thumbnail_url)
                      <img src="{{ $item->thumbnail_mini_url ?? $item->thumbnail_url }}" alt="{{ $item->title }}" class="rounded" style="width: 52px; height: 38px; object-fit: cover;">
                    @else
                      <span class="badge badge-light border text-muted">No Img</span>
                    @endif
                  </td>
                  <td>
                    <div class="font-weight-bold">{{ $item->title }}</div>
                    <small class="text-muted"><code>{{ $item->slug }}</code></small>
                  </td>
                  <td>
                    @if ($item->category)
                      <span class="badge badge-info">{{ $item->category->name }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    {{ $item->publisher?->name ?? '-' }}
                  </td>
                  <td class="text-nowrap" style="font-size: 13px;">
                    {{ $item->published_at ? $item->published_at->format('d/m/Y H:i') : '-' }}
                  </td>
                  <td class="text-center">
                    @if ($item->status === 'published')
                      <span class="badge badge-success">Published</span>
                    @elseif ($item->status === 'archived')
                      <span class="badge badge-secondary">Archived</span>
                    @else
                      <span class="badge badge-warning text-dark">Draft</span>
                    @endif
                  </td>
                  <td class="text-center text-nowrap">
                    <a href="{{ route('admin.news.show', $item) }}" class="btn btn-sm btn-icon btn-info mr-1" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('news.edit')
                      <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-icon btn-primary mr-1" title="{{ __('Edit') }}">
                        <i class="fas fa-pencil-alt"></i>
                      </a>
                    @endcan
                    @can('news.delete')
                      <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-icon btn-danger btn-delete" title="{{ __('Hapus') }}">
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

  @can('news.create')
    <div class="modal fade" id="modalImportNews" tabindex="-1" role="dialog" aria-labelledby="modalImportNewsLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="modalImportNewsLabel">
              <i class="fas fa-file-excel text-success mr-2"></i> {{ __('Import Berita') }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('admin.news.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="alert alert-light border small mb-3">
                <i class="fas fa-info-circle text-info mr-1"></i>
                {{ __('Unduh template terlebih dahulu agar format kolom sesuai:') }}
                <a href="{{ route('admin.news.template') }}" class="font-weight-bold ml-1">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Template') }}
                </a>
              </div>
              <div class="form-group mb-0">
                <label for="import_news_file" class="font-weight-bold">{{ __('File Excel / CSV') }} <span class="text-danger">*</span></label>
                <input type="file" id="import_news_file" name="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
              </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Batal') }}</button>
              <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> {{ __('Import') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endcan
@endsection
