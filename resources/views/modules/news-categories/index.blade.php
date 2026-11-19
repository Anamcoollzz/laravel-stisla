@extends('layouts.app')

@section('title', __('News Categories'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $("#table-news-categories").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [4] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Apakah Anda yakin?')),
        text: @json(__('Kategori berita ini akan dihapus permanen!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    @if (session('success'))
      swal(@json(__('Berhasil')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('News Categories') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('News') }}</div>
      <div class="breadcrumb-item">{{ __('Categories') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-tags mr-2 text-primary"></i> {{ __('Daftar Kategori Berita') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          @can('news-categories.view')
            <a href="{{ route('news-categories.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('news-categories.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('news-categories.create')
            <button type="button" class="btn btn-outline-info btn-sm mr-1" data-toggle="modal" data-target="#modalImportNewsCategory">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
            <a href="{{ route('news-categories.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> {{ __('Tambah Kategori') }}
            </a>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-news-categories">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th>{{ __('Nama Kategori') }}</th>
                <th>{{ __('Deskripsi') }}</th>
                <th class="text-center" style="width: 120px;">{{ __('Jumlah Berita') }}</th>
                <th class="text-center" style="width: 150px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($categories as $category)
                <tr>
                  <td class="text-center">{{ $loop->iteration }}</td>
                  <td class="font-weight-bold">{{ $category->name }}</td>
                  <td class="text-muted">{{ \Illuminate\Support\Str::limit($category->description ?? '-', 100) }}</td>
                  <td class="text-center">
                    <span class="badge badge-info px-2 py-1">{{ $category->news_count }} berita</span>
                  </td>
                  <td class="text-center text-nowrap">
                    <a href="{{ route('news-categories.show', $category) }}" class="btn btn-sm btn-icon btn-info mr-1" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('news-categories.edit')
                      <a href="{{ route('news-categories.edit', $category) }}" class="btn btn-sm btn-icon btn-primary mr-1" title="{{ __('Edit') }}">
                        <i class="fas fa-pencil-alt"></i>
                      </a>
                    @endcan
                    @can('news-categories.delete')
                      <form action="{{ route('news-categories.destroy', $category) }}" method="POST" class="d-inline">
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

  @can('news-categories.create')
    <div class="modal fade" id="modalImportNewsCategory" tabindex="-1" role="dialog" aria-labelledby="modalImportNewsCategoryLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="modalImportNewsCategoryLabel">
              <i class="fas fa-file-excel text-success mr-2"></i> {{ __('Import Kategori Berita') }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('news-categories.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="alert alert-light border small mb-3">
                <i class="fas fa-info-circle text-info mr-1"></i>
                {{ __('Unduh template terlebih dahulu agar format kolom sesuai:') }}
                <a href="{{ route('news-categories.template') }}" class="font-weight-bold ml-1">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Template') }}
                </a>
              </div>
              <div class="form-group mb-0">
                <label for="import_category_file" class="font-weight-bold">{{ __('File Excel / CSV') }} <span class="text-danger">*</span></label>
                <input type="file" id="import_category_file" name="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
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
