@extends('layouts.app')

@section('title', __('Contact Inquiries'))

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

    $("#table-lp-contact-inquiries").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [7] }
      ]
    });

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Apakah Anda yakin?')),
        text: @json(__('Pesan kontak ini akan dihapus permanen!')),
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
    <h1>{{ __('Contact Inquiries') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Company Profile') }}</div>
      <div class="breadcrumb-item">{{ __('Contact Inquiries') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i class="fas fa-envelope"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Total Pesan') }}</h4></div>
            <div class="card-body">{{ $totalCount }}</div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning"><i class="fas fa-envelope-open-text"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Belum Dibaca') }}</h4></div>
            <div class="card-body">{{ $unreadCount }}</div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i class="fas fa-check-double"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Sudah Dibaca') }}</h4></div>
            <div class="card-body">{{ $readCount }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-inbox mr-2 text-primary"></i> {{ __('Daftar Pesan Kontak') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          <div class="btn-group mr-2" role="group">
            <a href="{{ route('lp-contact-inquiries.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('Semua') }}</a>
            <a href="{{ route('lp-contact-inquiries.index', ['status' => 'unread']) }}" class="btn btn-sm {{ $status === 'unread' ? 'btn-warning' : 'btn-outline-warning' }}">{{ __('Belum Dibaca') }}</a>
            <a href="{{ route('lp-contact-inquiries.index', ['status' => 'read']) }}" class="btn btn-sm {{ $status === 'read' ? 'btn-success' : 'btn-outline-success' }}">{{ __('Sudah Dibaca') }}</a>
          </div>
          @can('lp-contact-inquiries.view')
            <a href="{{ route('lp-contact-inquiries.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('lp-contact-inquiries.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('lp-contact-inquiries.create')
            <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#modalImportContactInquiry">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-lp-contact-inquiries">
            <thead>
              <tr>
                <th width="45">#</th>
                <th>{{ __('Pengirim') }}</th>
                <th>{{ __('Telepon') }}</th>
                <th>{{ __('Subjek') }}</th>
                <th>{{ __('Pesan') }}</th>
                <th>{{ __('Waktu') }}</th>
                <th>{{ __('Status') }}</th>
                <th width="160">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($inquiries as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <div class="font-weight-bold">{{ $item->full_name }}</div>
                    <small class="text-muted">{{ $item->email }}</small>
                  </td>
                  <td>{{ $item->phone }}</td>
                  <td class="font-weight-bold">{{ \Illuminate\Support\Str::limit($item->subject, 40) }}</td>
                  <td>{{ \Illuminate\Support\Str::limit($item->message, 65) }}</td>
                  <td>{{ $item->created_at?->format('d M Y H:i') }}</td>
                  <td>
                    @if ($item->is_read)
                      <span class="badge badge-success">{{ __('Sudah Dibaca') }}</span>
                    @else
                      <span class="badge badge-warning">{{ __('Belum Dibaca') }}</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('lp-contact-inquiries.show', $item) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('lp-contact-inquiries.edit')
                      <form action="{{ route('lp-contact-inquiries.toggleRead', $item) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $item->is_read ? 'btn-secondary' : 'btn-success' }}" data-toggle="tooltip" title="{{ $item->is_read ? __('Tandai Belum Dibaca') : __('Tandai Sudah Dibaca') }}">
                          <i class="fas {{ $item->is_read ? 'fa-envelope' : 'fa-check' }}"></i>
                        </button>
                      </form>
                    @endcan
                    @can('lp-contact-inquiries.delete')
                      <form action="{{ route('lp-contact-inquiries.destroy', $item) }}" method="POST" class="d-inline">
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
  @can('lp-contact-inquiries.create')
    <div class="modal fade" id="modalImportContactInquiry" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('lp-contact-inquiries.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-file-upload mr-2 text-info"></i> {{ __('Import Pesan Kontak') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Tutup') }}">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-light border">
              {{ __('Gunakan template Excel yang disediakan agar format kolom sesuai.') }}
              <div class="mt-2">
                <a href="{{ route('lp-contact-inquiries.template') }}" class="btn btn-sm btn-outline-primary">
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
