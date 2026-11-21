@extends('layouts.app')

@section('title', __('News Comments'))

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

    $("#table-news-comments").dataTable({
      "pageLength": 25,
      "columnDefs": [
        { "sortable": false, "targets": [6] }
      ]
    });

    $(document).on('click', '.btn-open-reply', function() {
      var actionUrl = $(this).data('action');
      var authorName = $(this).data('name');
      var originalComment = $(this).data('comment');
      $('#formQuickReply').attr('action', actionUrl);
      $('#replyTargetAuthor').text(authorName);
      $('#replyTargetOriginal').text(originalComment);
      $('#modalQuickReply').modal('show');
    });

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('News Comments') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('News') }}</div>
      <div class="breadcrumb-item">{{ __('News Comments') }}</div>
    </div>
  </div>

  <div class="section-body">
    <!-- Statistics -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-comments"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Komentar') }}</h4>
            </div>
            <div class="card-body">{{ number_format($stats['total']) }}</div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Terverifikasi') }}</h4>
            </div>
            <div class="card-body">{{ number_format($stats['approved']) }}</div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Menunggu Verifikasi') }}</h4>
            </div>
            <div class="card-body">{{ number_format($stats['pending']) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4><i class="fas fa-comments mr-2 text-primary"></i> {{ __('Daftar Komentar Berita') }}</h4>
        <div class="card-header-action d-flex flex-wrap align-items-center">
          <form method="GET" action="{{ route('news-comments.index') }}" class="form-inline mr-2">
            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
              <option value="">{{ __('Semua Status') }}</option>
              <option value="approved" @selected($selectedStatus === 'approved')>{{ __('Terverifikasi') }}</option>
              <option value="pending" @selected($selectedStatus === 'pending')>{{ __('Menunggu Verifikasi') }}</option>
            </select>
          </form>
          @can('news-comments.view')
            <a href="{{ route('news-comments.export') }}" class="btn btn-outline-success btn-sm mr-1">
              <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
            </a>
            <a href="{{ route('news-comments.exportPdf') }}" class="btn btn-outline-danger btn-sm mr-1">
              <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
            </a>
          @endcan
          @can('news-comments.create')
            <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#modalImportNewsComment">
              <i class="fas fa-file-upload mr-1"></i> {{ __('Import Excel') }}
            </button>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-news-comments">
            <thead>
              <tr>
                <th class="text-center" style="width: 45px;">#</th>
                <th>{{ __('Pengirim') }}</th>
                <th>{{ __('Berita') }}</th>
                <th>{{ __('Komentar') }}</th>
                <th style="width: 140px;">{{ __('Waktu') }}</th>
                <th class="text-center" style="width: 120px;">{{ __('Status') }}</th>
                <th class="text-center" style="width: 175px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($comments as $comment)
                <tr>
                  <td class="text-center">{{ $loop->iteration }}</td>
                  <td>
                    <div class="font-weight-bold">{{ $comment->name }}</div>
                    <small class="text-muted">{{ $comment->email }}</small>
                    @if ($comment->parent_id)
                      <div><span class="badge badge-light border text-muted small">Balasan #{{ $comment->parent_id }}</span></div>
                    @endif
                  </td>
                  <td>
                    @if ($comment->news)
                      <a href="{{ route('admin.news.show', $comment->news) }}" class="font-weight-600">
                        {{ \Illuminate\Support\Str::limit($comment->news->title, 45) }}
                      </a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    {{ \Illuminate\Support\Str::limit($comment->comment, 90) }}
                  </td>
                  <td class="text-nowrap" style="font-size: 13px;">
                    {{ $comment->created_at?->format('d/m/Y H:i') }}
                  </td>
                  <td class="text-center">
                    @if ($comment->is_approved)
                      <span class="badge badge-success">{{ __('Terverifikasi') }}</span>
                    @else
                      <span class="badge badge-warning text-dark">{{ __('Pending') }}</span>
                    @endif
                  </td>
                  <td class="text-center text-nowrap">
                    @can('news-comments.edit')
                      <form action="{{ route('news-comments.toggleApproval', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-icon {{ $comment->is_approved ? 'btn-warning' : 'btn-success' }} mr-1" title="{{ $comment->is_approved ? __('Batalkan Verifikasi') : __('Verifikasi Komentar') }}">
                          <i class="fas {{ $comment->is_approved ? 'fa-times' : 'fa-check' }}"></i>
                        </button>
                      </form>
                    @endcan
                    @can('news-comments.create')
                      <button type="button" class="btn btn-sm btn-icon btn-primary mr-1 btn-open-reply"
                        data-action="{{ route('news-comments.reply', $comment) }}"
                        data-name="{{ $comment->name }}"
                        data-comment="{{ $comment->comment }}"
                        title="{{ __('Balas Komentar') }}">
                        <i class="fas fa-reply"></i>
                      </button>
                    @endcan
                    <a href="{{ route('news-comments.show', $comment) }}" class="btn btn-sm btn-icon btn-info mr-1" title="{{ __('Detail') }}">
                      <i class="fas fa-eye"></i>
                    </a>
                    @can('news-comments.delete')
                      <form action="{{ route('news-comments.destroy', $comment) }}" method="POST" class="d-inline">
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

  <!-- Modal Quick Reply -->
  @can('news-comments.create')
    <div class="modal fade" id="modalQuickReply" tabindex="-1" role="dialog" aria-labelledby="modalQuickReplyLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="modalQuickReplyLabel">
              <i class="fas fa-reply text-primary mr-2"></i> {{ __('Balas Komentar') }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="#" method="POST" id="formQuickReply">
            @csrf
            <div class="modal-body">
              <div class="alert alert-light border mb-3">
                <div class="font-weight-bold small text-muted mb-1">Komentar dari <span id="replyTargetAuthor"></span>:</div>
                <div class="small text-dark" id="replyTargetOriginal"></div>
              </div>
              <div class="form-group mb-0">
                <label for="replyCommentText" class="font-weight-bold">{{ __('Balasan Admin') }} <span class="text-danger">*</span></label>
                <textarea id="replyCommentText" name="comment" class="form-control" style="height: 120px;" required placeholder="Tulis balasan Anda..."></textarea>
                <small class="form-text text-muted">{{ __('Balasan admin otomatis terverifikasi dan langsung tampil.') }}</small>
              </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Batal') }}</button>
              <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> {{ __('Kirim Balasan') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="modalImportNewsComment" tabindex="-1" role="dialog" aria-labelledby="modalImportNewsCommentLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="modalImportNewsCommentLabel">
              <i class="fas fa-file-excel text-success mr-2"></i> {{ __('Import Komentar Berita') }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('news-comments.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="alert alert-light border small mb-3">
                <i class="fas fa-info-circle text-info mr-1"></i>
                {{ __('Unduh template terlebih dahulu agar format kolom sesuai:') }}
                <a href="{{ route('news-comments.template') }}" class="font-weight-bold ml-1">
                  <i class="fas fa-download mr-1"></i> {{ __('Download Template') }}
                </a>
              </div>
              <div class="form-group mb-0">
                <label for="import_comment_file" class="font-weight-bold">{{ __('File Excel / CSV') }} <span class="text-danger">*</span></label>
                <input type="file" id="import_comment_file" name="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
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
