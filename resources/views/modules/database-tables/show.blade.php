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

@push('page-js')
  <script>
    "use strict";

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
                    </tr>
                  @empty
                    <tr>
                      <td colspan="{{ max(1, count($columnNames)) }}" class="text-center py-4 text-muted">
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
                  </tr>
                </thead>
                <tbody>
                  @foreach ($columns as $idx => $col)
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
@endsection
