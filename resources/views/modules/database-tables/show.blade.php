@extends('layouts.app')

@section('title', __('Tabel: ') . $table)

@push('css')
  <style>
    .cell-truncate {
      max-width: 240px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .cell-clickable {
      cursor: pointer;
    }
    .cell-clickable:hover {
      text-decoration: underline;
    }
  </style>
@endpush

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $('[data-toggle="tooltip"]').tooltip();

    @if (session('success'))
      swal(@json(__('Berhasil')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif

    $(document).on('click', '.btn-edit-row', function() {
      var $btn = $(this);
      var row = $btn.data('row');
      var columns = @json($columns);
      var primaryKey = @json($primaryKey);
      var $container = $('#editRowFields').empty();

      $('#formEditRow').attr('action', $btn.data('action'));
      $('#editRowPkLabel').text(primaryKey + ': ' + (row[primaryKey] ?? ''));

      columns.forEach(function(col) {
        var name = col.name;
        var val = row[name];
        var isPkAi = (name === primaryKey && col.auto_increment);
        var type = String(col.type_name || col.type || '').toLowerCase();
        var isLong = type.indexOf('text') !== -1 || type.indexOf('json') !== -1 || (val !== null && String(val).length > 100);
        var isNull = (val === null || val === undefined);

        var html = '<div class="form-group">';
        html += '<div class="d-flex justify-content-between align-items-center mb-1">';
        html += '<label class="mb-0 font-weight-bold"><code>' + name + '</code> <small class="text-muted">(' + (col.type || col.type_name) + ')</small></label>';
        if (col.nullable && !isPkAi) {
          html += '<label class="mb-0 small"><input type="checkbox" name="nulls[' + name + ']" value="1" class="row-null-toggle" data-target="rowInput_' + name + '"' + (isNull ? ' checked' : '') + '> NULL</label>';
        }
        html += '</div>';

        var safeVal = isNull ? '' : String(val).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        if (isLong) {
          html += '<textarea id="rowInput_' + name + '" name="values[' + name + ']" rows="3" class="form-control"' + (isPkAi ? ' readonly' : '') + (isNull && col.nullable ? ' disabled' : '') + '>' + safeVal + '</textarea>';
        } else {
          html += '<input type="text" id="rowInput_' + name + '" name="values[' + name + ']" value="' + safeVal + '" class="form-control"' + (isPkAi ? ' readonly' : '') + (isNull && col.nullable ? ' disabled' : '') + '>';
        }
        html += '</div>';
        $container.append(html);
      });

      $('#modalEditRow').modal('show');
    });

    $(document).on('change', '.row-null-toggle', function() {
      var targetId = $(this).data('target');
      $('#' + targetId).prop('disabled', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-delete-row', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      var pk = $(this).data('pk');
      swal({
        title: @json(__('Apakah Anda yakin?')),
        text: @json(__('Baris data ini akan dihapus permanen:')) + ' #' + pk,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    $(document).on('click', '.btn-edit-column', function() {
      var $btn = $(this);
      $('#formEditColumn').attr('action', $btn.data('action'));
      $('#editColCurrentName').text($btn.data('column'));
      $('#editColNewName').val($btn.data('column'));
      $('#editColType').val($btn.data('mapped-type'));
      $('#editColLength').val($btn.data('length') || '');
      $('#editColNullable').prop('checked', Boolean($btn.data('nullable')));
      $('#editColDefault').val($btn.data('default') || '');
      $('#editColComment').val($btn.data('comment') || '');
      $('#modalEditColumn').modal('show');
    });

    $(document).on('click', '.btn-drop-column', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      var colName = $(this).data('column');
      swal({
        title: @json(__('Apakah Anda yakin?')),
        text: @json(__('Kolom akan dihapus permanen beserta datanya:')) + ' ' + colName,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    $(document).on('click', '.btn-view-cell', function() {
      var col = $(this).data('column');
      var val = $(this).data('value');
      $('#modalCellColumn').text(col);
      $('#modalCellValue').text(val);
      $('#modalCellDetail').modal('show');
    });

    $(document).on('click', '#btnCopyCellValue', function() {
      var text = $('#modalCellValue').text();
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
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('database-tables.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Tabel:') }} <code>{{ $table }}</code></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('database-tables.index') }}">{{ __('Database Tables') }}</a></div>
      <div class="breadcrumb-item">{{ $table }}</div>
    </div>
  </div>

  <div class="section-body">
    <!-- Summary Badges -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card mb-0">
          <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center">
            <div>
              <span class="badge badge-primary mr-2 px-3 py-2"><i class="fas fa-columns mr-1"></i> {{ count($columns) }} Kolom</span>
              <span class="badge badge-success mr-2 px-3 py-2"><i class="fas fa-database mr-1"></i> {{ number_format($totalRows) }} Total Baris</span>
              <span class="badge badge-info mr-2 px-3 py-2"><i class="fas fa-key mr-1"></i> {{ count($indexes) }} Indexes</span>
              <span class="badge badge-warning px-3 py-2"><i class="fas fa-link mr-1"></i> {{ count($foreignKeys) }} Foreign Keys</span>
            </div>
            <div class="mt-2 mt-md-0">
              <ul class="nav nav-pills" id="tableTabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link {{ $activeTab === 'data' ? 'active' : '' }}" id="tab-data-link" data-toggle="tab" href="#tab-data" role="tab">
                    <i class="fas fa-table mr-1"></i> {{ __('Data Tabel') }}
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ $activeTab === 'columns' ? 'active' : '' }}" id="tab-columns-link" data-toggle="tab" href="#tab-columns" role="tab">
                    <i class="fas fa-columns mr-1"></i> {{ __('Detail Columns & Schema') }}
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content" id="tableTabsContent">
      <!-- TAB 1: DATA PAGINATION -->
      <div class="tab-pane fade {{ $activeTab === 'data' ? 'show active' : '' }}" id="tab-data" role="tabpanel">
        <div class="card">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h4><i class="fas fa-table mr-2 text-primary"></i> {{ __('Data Baris') }} ({{ $table }})</h4>
            <div class="card-header-action text-muted small">
              Menampilkan {{ $rows->firstItem() ?? 0 }} - {{ $rows->lastItem() ?? 0 }} dari {{ number_format($rows->total()) }} baris
            </div>
          </div>
          <div class="card-body">
            <!-- Search, Sort & Per Page Controls -->
            <form method="GET" action="{{ route('database-tables.show', $table) }}" class="mb-4">
              <input type="hidden" name="tab" value="data">
              <div class="row">
                <div class="col-md-4 col-12 mb-2">
                  <label class="font-weight-bold small">{{ __('Cari di Semua Kolom') }}</label>
                  <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Ketik kata kunci...">
                </div>
                <div class="col-md-3 col-6 mb-2">
                  <label class="font-weight-bold small">{{ __('Urutkan Kolom') }}</label>
                  <select name="sort" class="form-control">
                    @foreach ($columnNames as $colName)
                      <option value="{{ $colName }}" {{ $sort === $colName ? 'selected' : '' }}>{{ $colName }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2 col-6 mb-2">
                  <label class="font-weight-bold small">{{ __('Arah') }}</label>
                  <select name="direction" class="form-control">
                    <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>DESC (Terbaru)</option>
                    <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>ASC (Terlama)</option>
                  </select>
                </div>
                <div class="col-md-1 col-4 mb-2">
                  <label class="font-weight-bold small">{{ __('Per Hal') }}</label>
                  <select name="per_page" class="form-control">
                    @foreach ([10, 15, 25, 50, 100] as $pp)
                      <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2 col-8 mb-2 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary mr-1"><i class="fas fa-search"></i> Filter</button>
                  <a href="{{ route('database-tables.show', ['table' => $table, 'tab' => 'data']) }}" class="btn btn-secondary" title="Reset"><i class="fas fa-undo"></i></a>
                </div>
              </div>
            </form>

            <!-- Data Table -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover table-sm mb-0">
                <thead class="thead-light">
                  <tr>
                    @foreach ($columnNames as $colName)
                      <th class="text-nowrap">
                        <a href="{{ route('database-tables.show', array_merge(request()->query(), ['table' => $table, 'tab' => 'data', 'sort' => $colName, 'direction' => ($sort === $colName && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                          {{ $colName }}
                          @if ($sort === $colName)
                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1 text-primary"></i>
                          @endif
                        </a>
                      </th>
                    @endforeach
                    @if ($primaryKey !== null)
                      <th class="text-nowrap" style="width: 100px;">{{ __('Aksi') }}</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @forelse ($rows as $row)
                    <tr>
                      @foreach ($columnNames as $colName)
                        @php
                          $val = $row->{$colName} ?? null;
                        @endphp
                        <td class="cell-truncate">
                          @if ($val === null)
                            <span class="badge badge-light text-muted border">NULL</span>
                          @elseif (is_bool($val))
                            <span class="badge {{ $val ? 'badge-success' : 'badge-secondary' }}">{{ $val ? 'true' : 'false' }}</span>
                          @elseif (mb_strlen((string) $val) > 50)
                            <span class="text-primary cell-clickable btn-view-cell" data-column="{{ $colName }}" data-value="{{ (string) $val }}" title="Klik untuk melihat isi penuh">
                              {{ \Illuminate\Support\Str::limit((string) $val, 50) }}
                            </span>
                          @else
                            {{ (string) $val }}
                          @endif
                        </td>
                      @endforeach
                      @if ($primaryKey !== null)
                        <td class="text-nowrap">
                          <button
                            type="button"
                            class="btn btn-warning btn-sm btn-edit-row"
                            data-toggle="tooltip"
                            title="{{ __('Ubah Data') }}"
                            data-action="{{ route('database-tables.rows.update', [$table, $row->{$primaryKey}]) }}"
                            data-row='@json($row)'
                          >
                            <i class="fas fa-edit"></i>
                          </button>
                          <form action="{{ route('database-tables.rows.destroy', [$table, $row->{$primaryKey}]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button
                              type="button"
                              class="btn btn-danger btn-sm btn-delete-row"
                              data-toggle="tooltip"
                              title="{{ __('Hapus Data') }}"
                              data-pk="{{ $row->{$primaryKey} }}"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </td>
                      @endif
                    </tr>
                  @empty
                    <tr>
                      <td colspan="{{ max(1, count($columnNames) + ($primaryKey !== null ? 1 : 0)) }}" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                        {{ __('Tidak ada data pada tabel ini.') }}
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          @if ($rows->hasPages())
            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center">
              <span class="small text-muted">Halaman {{ $rows->currentPage() }} dari {{ $rows->lastPage() }}</span>
              {{ $rows->links('pagination::bootstrap-4') }}
            </div>
          @endif
        </div>
      </div>

      <!-- TAB 2: DETAIL COLUMNS & SCHEMA -->
      <div class="tab-pane fade {{ $activeTab === 'columns' ? 'show active' : '' }}" id="tab-columns" role="tabpanel">
        <!-- Columns Card -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-columns mr-2 text-primary"></i> {{ __('Detail Columns') }} ({{ count($columns) }})</h4>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead>
                  <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('Nama Kolom') }}</th>
                    <th>{{ __('Tipe Data') }}</th>
                    <th class="text-center">{{ __('Nullable') }}</th>
                    <th>{{ __('Default') }}</th>
                    <th class="text-center">{{ __('Auto Increment') }}</th>
                    <th>{{ __('Collation / Komentar') }}</th>
                    <th style="width: 110px;">{{ __('Aksi') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($columns as $idx => $col)
                    @php
                      $rawType = strtolower((string) ($col['type_name'] ?? $col['type'] ?? 'string'));
                      $mappedType = match (true) {
                          str_contains($rawType, 'bigint') => 'bigInteger',
                          str_contains($rawType, 'tinyint(1)') || $rawType === 'boolean' || $rawType === 'bool' => 'boolean',
                          str_contains($rawType, 'int') => 'integer',
                          str_contains($rawType, 'longtext') => 'longText',
                          str_contains($rawType, 'text') => 'text',
                          str_contains($rawType, 'decimal') => 'decimal',
                          str_contains($rawType, 'float') || str_contains($rawType, 'double') => 'float',
                          $rawType === 'date' => 'date',
                          str_contains($rawType, 'datetime') || str_contains($rawType, 'timestamp') => 'dateTime',
                          str_contains($rawType, 'json') => 'json',
                          default => 'string',
                      };
                      $colLength = '';
                      if (preg_match('/\((\d+)\)/', (string) ($col['type'] ?? ''), $m)) {
                          $colLength = $m[1];
                      }
                    @endphp
                    <tr>
                      <td class="text-muted font-weight-bold">{{ $idx + 1 }}</td>
                      <td>
                        <code class="font-weight-bold text-dark" style="font-size: 14px;">{{ $col['name'] }}</code>
                      </td>
                      <td>
                        <span class="badge badge-light border text-primary font-weight-bold">{{ $col['type'] ?? $col['type_name'] }}</span>
                      </td>
                      <td class="text-center">
                        @if ($col['nullable'])
                          <span class="badge badge-success">YES</span>
                        @else
                          <span class="badge badge-danger">NO</span>
                        @endif
                      </td>
                      <td>
                        @if ($col['default'] !== null)
                          <code>{{ $col['default'] }}</code>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($col['auto_increment'])
                          <span class="badge badge-info"><i class="fas fa-check mr-1"></i> AI</span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td class="small text-muted">
                        {{ $col['collation'] ?? '-' }}
                        @if (!empty($col['comment']))
                          <div class="text-dark">{{ $col['comment'] }}</div>
                        @endif
                      </td>
                      <td>
                        <button
                          type="button"
                          class="btn btn-warning btn-sm btn-edit-column"
                          data-toggle="tooltip"
                          title="{{ __('Ubah Kolom') }}"
                          data-action="{{ route('database-tables.columns.update', [$table, $col['name']]) }}"
                          data-column="{{ $col['name'] }}"
                          data-mapped-type="{{ $mappedType }}"
                          data-length="{{ $colLength }}"
                          data-nullable="{{ $col['nullable'] ? 1 : 0 }}"
                          data-default="{{ $col['default'] }}"
                          data-comment="{{ $col['comment'] ?? '' }}"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                        @if ($col['name'] !== 'id' && count($columns) > 1)
                          <form action="{{ route('database-tables.columns.destroy', [$table, $col['name']]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button
                              type="button"
                              class="btn btn-danger btn-sm btn-drop-column"
                              data-toggle="tooltip"
                              title="{{ __('Hapus Kolom') }}"
                              data-column="{{ $col['name'] }}"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Indexes & Foreign Keys -->
        <div class="row">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-key mr-2 text-warning"></i> {{ __('Indexes') }} ({{ count($indexes) }})</h4>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped mb-0">
                    <thead>
                      <tr>
                        <th>{{ __('Nama Index') }}</th>
                        <th>{{ __('Kolom') }}</th>
                        <th>{{ __('Tipe') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($indexes as $index)
                        <tr>
                          <td><code>{{ $index['name'] }}</code></td>
                          <td>{{ implode(', ', $index['columns']) }}</td>
                          <td>
                            @if ($index['primary'])
                              <span class="badge badge-primary">PRIMARY</span>
                            @elseif ($index['unique'])
                              <span class="badge badge-success">UNIQUE</span>
                            @else
                              <span class="badge badge-light border">INDEX</span>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="3" class="text-center text-muted py-3">{{ __('Tidak ada index.') }}</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-link mr-2 text-info"></i> {{ __('Foreign Keys') }} ({{ count($foreignKeys) }})</h4>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped mb-0">
                    <thead>
                      <tr>
                        <th>{{ __('Kolom') }}</th>
                        <th>{{ __('Referensi Tabel') }}</th>
                        <th>{{ __('On Delete / Update') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($foreignKeys as $fk)
                        <tr>
                          <td><code>{{ implode(', ', $fk['columns']) }}</code></td>
                          <td>
                            <a href="{{ route('database-tables.show', $fk['foreign_table']) }}">
                              <code>{{ $fk['foreign_table'] }}.{{ implode(', ', $fk['foreign_columns']) }}</code>
                            </a>
                          </td>
                          <td class="small text-muted">
                            DEL: {{ $fk['on_delete'] ?? '-' }} / UPD: {{ $fk['on_update'] ?? '-' }}
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="3" class="text-center text-muted py-3">{{ __('Tidak ada foreign key.') }}</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Full Cell Value -->
  <div class="modal fade" id="modalCellDetail" tabindex="-1" role="dialog" aria-labelledby="modalCellDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold" id="modalCellDetailLabel">
            <i class="fas fa-eye text-primary mr-2"></i> {{ __('Isi Kolom:') }} <code id="modalCellColumn"></code>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <pre class="bg-dark text-white p-3 rounded mb-0" id="modalCellValue" style="max-height: 420px; overflow: auto; white-space: pre-wrap; word-break: break-word; font-size: 13px;"></pre>
        </div>
        <div class="modal-footer bg-whitesmoke">
          <button type="button" class="btn btn-outline-primary" id="btnCopyCellValue">
            <i class="fas fa-copy mr-1"></i> {{ __('Salin') }}
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Tutup') }}</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Column -->
  <div class="modal fade" id="modalEditColumn" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form id="formEditColumn" action="" method="POST" class="modal-content">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit text-warning mr-2"></i> {{ __('Ubah Kolom') }}: <code id="editColCurrentName"></code></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Tutup') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="editColNewName">{{ __('Nama Kolom') }} <span class="text-danger">*</span></label>
            <input type="text" name="new_name" id="editColNewName" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <label for="editColType">{{ __('Tipe Data') }} <span class="text-danger">*</span></label>
                <select name="type" id="editColType" class="form-control" required>
                  <option value="string">string (VARCHAR)</option>
                  <option value="text">text (TEXT)</option>
                  <option value="longText">longText (LONGTEXT)</option>
                  <option value="integer">integer (INT)</option>
                  <option value="bigInteger">bigInteger (BIGINT)</option>
                  <option value="unsignedBigInteger">unsignedBigInteger</option>
                  <option value="float">float (FLOAT)</option>
                  <option value="decimal">decimal (15,2)</option>
                  <option value="boolean">boolean (TINYINT)</option>
                  <option value="date">date (DATE)</option>
                  <option value="dateTime">dateTime (DATETIME)</option>
                  <option value="json">json (JSON)</option>
                </select>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label for="editColLength">{{ __('Panjang (VARCHAR)') }}</label>
                <input type="number" name="length" id="editColLength" min="1" max="65535" class="form-control" placeholder="255">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <label for="editColDefault">{{ __('Default Value') }}</label>
                <input type="text" name="default_value" id="editColDefault" class="form-control">
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label class="d-block">{{ __('Nullable') }}</label>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="nullable" id="editColNullable" value="1" class="custom-switch-input">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Ya') }}</span>
                </label>
              </div>
            </div>
          </div>
          <div class="form-group mb-0">
            <label for="editColComment">{{ __('Komentar') }}</label>
            <input type="text" name="comment" id="editColComment" class="form-control">
          </div>
        </div>
        <div class="modal-footer bg-whitesmoke">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Batal') }}</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan Perubahan') }}</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Edit Row -->
  <div class="modal fade" id="modalEditRow" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="formEditRow" action="" method="POST" class="modal-content">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit text-warning mr-2"></i> {{ __('Ubah Data Baris') }} (<code id="editRowPkLabel"></code>)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Tutup') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="editRowFields" style="max-height: 65vh; overflow-y: auto;"></div>
        <div class="modal-footer bg-whitesmoke">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Batal') }}</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Simpan Perubahan') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
