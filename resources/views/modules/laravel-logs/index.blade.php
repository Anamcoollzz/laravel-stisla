@extends('layouts.app')

@section('title', __('Laravel Log Viewer'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
  <style>
    .log-message-cell {
      max-width: 520px;
      word-break: break-word;
    }
    .log-stack-pre {
      max-height: 450px;
      overflow: auto;
      font-size: 12px;
      line-height: 1.5;
      white-space: pre-wrap;
      word-break: break-word;
    }
  </style>
@endpush

@push('js')
  <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    var logEntries = @json($entries);

    var logTable = $("#table-laravel-logs").DataTable({
      "pageLength": 25,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      "order": [[0, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [5] }
      ],
      "language": {
        "search": "Cari Log:",
        "lengthMenu": "Tampilkan _MENU_ entri",
        "info": "Menampilkan _START_ - _END_ dari _TOTAL_ entri",
        "infoEmpty": "Tidak ada data",
        "zeroRecords": "Tidak ada log yang cocok dengan pencarian",
        "paginate": {
          "previous": "Sebelumnya",
          "next": "Berikutnya"
        }
      }
    });

    // Quick filter by Level
    $(document).on('click', '.btn-filter-level', function() {
      $('.btn-filter-level').removeClass('active');
      $(this).addClass('active');
      var level = $(this).data('level');
      logTable.column(1).search(level).draw();
    });

    // Open Detail Modal
    $(document).on('click', '.btn-view-log', function() {
      var idx = parseInt($(this).data('index'), 10);
      var entry = logEntries[idx];
      if (!entry) return;

      $('#modalLogLevel').text(entry.level);
      $('#modalLogEnv').text(entry.env);
      $('#modalLogTime').text(entry.timestamp);
      $('#modalLogMessage').text(entry.message);

      if (entry.stack && entry.stack.length > 0) {
        $('#modalLogStackContainer').show();
        $('#modalLogStack').text(entry.stack);
      } else {
        $('#modalLogStackContainer').hide();
        $('#modalLogStack').text('');
      }

      $('#modalLogDetail').modal('show');
    });

    // Copy Full Log in Modal
    $(document).on('click', '#btnCopyLogDetail', function() {
      var text = '[' + $('#modalLogTime').text() + '] ' + $('#modalLogEnv').text() + '.' + $('#modalLogLevel').text() + ': ' + $('#modalLogMessage').text();
      var stack = $('#modalLogStack').text();
      if (stack) {
        text += "\n" + stack;
      }

      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text);
      } else {
        var temp = $('<textarea>');
        $('body').append(temp);
        temp.val(text).select();
        document.execCommand('copy');
        temp.remove();
      }

      var $btn = $(this);
      var orig = $btn.html();
      $btn.html('<i class="fas fa-check mr-1"></i> Tersalin!').removeClass('btn-outline-primary').addClass('btn-success text-white');
      setTimeout(function() {
        $btn.html(orig).removeClass('btn-success text-white').addClass('btn-outline-primary');
      }, 1800);
    });

    // SweetAlert confirmations
    $(document).on('click', '.btn-clear-log', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Kosongkan File Log?')),
        text: @json(__('Seluruh isi file log ini akan dihapus (file tetap dipertahankan).')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willClear) => {
        if (willClear) {
          form.submit();
        }
      });
    });

    $(document).on('click', '.btn-delete-log', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Hapus File Log?')),
        text: @json(__('File log ini akan dihapus secara permanen dari server!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Laravel Log Viewer') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Access Control') }}</div>
      <div class="breadcrumb-item">{{ __('Laravel Logs') }}</div>
    </div>
  </div>

  <div class="section-body">
    <!-- Statistics Widget -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-file-alt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Entri Log') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Error / Critical') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['error']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Warning') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['warning']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-info-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Info / Debug') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['info']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- File Selector & Actions Card -->
    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h4><i class="fas fa-folder-open mr-2 text-primary"></i> {{ __('File Log') }}</h4>
        @if ($currentFile)
          <div class="card-header-action d-flex flex-wrap align-items-center">
            <a href="{{ route('laravel-logs.download', ['file' => $currentFile]) }}" class="btn btn-sm btn-primary mr-2">
              <i class="fas fa-download mr-1"></i> {{ __('Download') }}
            </a>
            @can('laravel-logs.delete')
              <form action="{{ route('laravel-logs.clear') }}" method="POST" class="d-inline mr-2">
                @csrf
                <input type="hidden" name="file" value="{{ $currentFile }}">
                <button type="button" class="btn btn-sm btn-warning btn-clear-log">
                  <i class="fas fa-eraser mr-1"></i> {{ __('Kosongkan Isi') }}
                </button>
              </form>
              <form action="{{ route('laravel-logs.destroy') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <input type="hidden" name="file" value="{{ $currentFile }}">
                <button type="button" class="btn btn-sm btn-danger btn-delete-log">
                  <i class="fas fa-trash mr-1"></i> {{ __('Hapus File') }}
                </button>
              </form>
            @endcan
          </div>
        @endif
      </div>
      <div class="card-body">
        @if (empty($files))
          <div class="alert alert-light border text-muted mb-0">
            <i class="fas fa-info-circle mr-1"></i> {{ __('Tidak ada file .log di direktori storage/logs.') }}
          </div>
        @else
          <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
              <form method="GET" action="{{ route('laravel-logs.index') }}" class="form-inline">
                <label for="selectLogFile" class="font-weight-bold mr-2">{{ __('Pilih File:') }}</label>
                <select name="file" id="selectLogFile" class="form-control mr-2" onchange="this.form.submit()">
                  @foreach ($files as $file)
                    <option value="{{ $file['name'] }}" {{ $currentFile === $file['name'] ? 'selected' : '' }}>
                      {{ $file['name'] }} ({{ $file['size'] }} - {{ $file['modified_at'] }})
                    </option>
                  @endforeach
                </select>
              </form>
            </div>
            <div class="col-md-6 text-md-right">
              <div class="btn-group btn-group-sm" role="group" aria-label="Filter Level">
                <button type="button" class="btn btn-outline-secondary btn-filter-level active" data-level="">Semua</button>
                <button type="button" class="btn btn-outline-danger btn-filter-level" data-level="ERROR">ERROR</button>
                <button type="button" class="btn btn-outline-warning btn-filter-level" data-level="WARNING">WARNING</button>
                <button type="button" class="btn btn-outline-info btn-filter-level" data-level="INFO">INFO</button>
                <button type="button" class="btn btn-outline-secondary btn-filter-level" data-level="DEBUG">DEBUG</button>
              </div>
            </div>
          </div>

          @if ($isTruncated)
            <div class="alert alert-warning mt-3 mb-0">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              {{ __('Ukuran file log melebihi 5 MB. Hanya 5 MB data terakhir yang ditampilkan untuk menjaga performa.') }}
            </div>
          @endif
        @endif
      </div>
    </div>

    <!-- DataTable Card -->
    <div class="card">
      <div class="card-header">
        <h4>
          <i class="fas fa-list-alt mr-2 text-primary"></i>
          {{ __('Daftar Log') }}
          @if ($currentFileMeta)
            <small class="text-muted ml-2">({{ $currentFileMeta['name'] }} &bull; {{ $currentFileMeta['size'] }})</small>
          @endif
        </h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover" id="table-laravel-logs">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 110px;">{{ __('Level') }}</th>
                <th style="width: 90px;">{{ __('Env') }}</th>
                <th style="width: 155px;">{{ __('Waktu') }}</th>
                <th>{{ __('Pesan') }}</th>
                <th class="text-center" style="width: 90px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($entries as $idx => $entry)
                @php
                  $levelBadges = [
                    'EMERGENCY' => 'badge-danger',
                    'ALERT' => 'badge-danger',
                    'CRITICAL' => 'badge-danger',
                    'ERROR' => 'badge-danger',
                    'WARNING' => 'badge-warning',
                    'NOTICE' => 'badge-primary',
                    'INFO' => 'badge-info',
                    'DEBUG' => 'badge-secondary',
                  ];
                  $badgeClass = $levelBadges[$entry['level']] ?? 'badge-secondary';
                @endphp
                <tr>
                  <td class="font-weight-bold text-muted">{{ $entry['id'] }}</td>
                  <td>
                    <span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold">{{ $entry['level'] }}</span>
                  </td>
                  <td>
                    <span class="badge badge-light border">{{ $entry['env'] }}</span>
                  </td>
                  <td class="text-nowrap" style="font-size: 13px;">{{ $entry['timestamp'] }}</td>
                  <td class="log-message-cell">
                    <div class="font-weight-600" style="font-size: 13px;">
                      {{ \Illuminate\Support\Str::limit($entry['message'], 160) }}
                    </div>
                    @if ($entry['stack'] !== '')
                      <small class="text-muted"><i class="fas fa-layer-group mr-1"></i> Memiliki stacktrace</small>
                    @endif
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-info btn-view-log" data-index="{{ $idx }}" title="{{ __('Lihat Detail & Stacktrace') }}">
                      <i class="fas fa-eye"></i> Detail
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detail Log -->
  <div class="modal fade" id="modalLogDetail" tabindex="-1" role="dialog" aria-labelledby="modalLogDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold" id="modalLogDetailLabel">
            <i class="fas fa-bug text-danger mr-2"></i> {{ __('Detail Log Entry') }}
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="d-flex flex-wrap align-items-center mb-3">
            <span class="badge badge-danger mr-2 px-2 py-1" id="modalLogLevel">-</span>
            <span class="badge badge-light border mr-2 px-2 py-1" id="modalLogEnv">-</span>
            <span class="text-muted small"><i class="far fa-clock mr-1"></i> <span id="modalLogTime">-</span></span>
          </div>

          <h6 class="font-weight-bold">{{ __('Pesan:') }}</h6>
          <div class="alert alert-light border text-dark mb-3" id="modalLogMessage" style="word-break: break-word; font-size: 13px;"></div>

          <div id="modalLogStackContainer" style="display: none;">
            <h6 class="font-weight-bold">{{ __('Stacktrace / Context:') }}</h6>
            <pre class="bg-dark text-white p-3 rounded log-stack-pre mb-0" id="modalLogStack"></pre>
          </div>
        </div>
        <div class="modal-footer bg-whitesmoke">
          <button type="button" class="btn btn-outline-primary" id="btnCopyLogDetail">
            <i class="fas fa-copy mr-1"></i> {{ __('Salin Log') }}
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Tutup') }}</button>
        </div>
      </div>
    </div>
  </div>
@endsection
