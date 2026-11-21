@extends('layouts.app')

@section('title', __('Database Tables'))

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

    $("#table-database-tables").DataTable({
      "pageLength": 25,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      "order": [[1, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [5] }
      ],
      "language": {
        "search": "Cari Tabel:",
        "lengthMenu": "Tampilkan _MENU_ tabel",
        "info": "Menampilkan _START_ - _END_ dari _TOTAL_ tabel",
        "infoEmpty": "Tidak ada tabel",
        "zeroRecords": "Tidak ada tabel yang cocok",
        "paginate": {
          "previous": "Sebelumnya",
          "next": "Berikutnya"
        }
      }
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Database Tables') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Access Control') }}</div>
      <div class="breadcrumb-item">{{ __('Database Tables') }}</div>
    </div>
  </div>

  <div class="section-body">
    <!-- Statistics Widget -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-table"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Tabel') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total_tables']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-database"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Baris Data') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total_rows']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-server"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Koneksi Driver') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['connection'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-hdd"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Database') }}</h4>
            </div>
            <div class="card-body text-truncate" style="font-size: 16px;" title="{{ $stats['database'] }}">
              {{ $stats['database'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tables List Card -->
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-list mr-2 text-primary"></i> {{ __('Daftar Tabel Database') }}</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover" id="table-database-tables">
            <thead>
              <tr>
                <th style="width: 60px;">#</th>
                <th>{{ __('Nama Tabel') }}</th>
                <th class="text-center" style="width: 130px;">{{ __('Jumlah Kolom') }}</th>
                <th class="text-center" style="width: 140px;">{{ __('Jumlah Baris') }}</th>
                <th style="width: 150px;">{{ __('Schema / Engine') }}</th>
                <th class="text-center" style="width: 180px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($tables as $idx => $table)
                <tr>
                  <td class="font-weight-bold text-muted">{{ $idx + 1 }}</td>
                  <td>
                    <a href="{{ route('database-tables.show', $table['name']) }}" class="font-weight-bold text-dark">
                      <code>{{ $table['name'] }}</code>
                    </a>
                  </td>
                  <td class="text-center">
                    <span class="badge badge-info px-2 py-1">{{ $table['columns_count'] }} kolom</span>
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $table['rows_count'] > 0 ? 'badge-success' : 'badge-light border' }} px-2 py-1">
                      {{ number_format($table['rows_count']) }} baris
                    </span>
                  </td>
                  <td>
                    <span class="text-muted small">
                      {{ $table['engine'] ?? $table['schema'] ?? '-' }}
                      @if ($table['size'])
                        ({{ $table['size'] }})
                      @endif
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <a href="{{ route('database-tables.show', ['table' => $table['name'], 'tab' => 'data']) }}" class="btn btn-sm btn-primary" title="{{ __('Lihat Data') }}">
                        <i class="fas fa-table mr-1"></i> Data
                      </a>
                      <a href="{{ route('database-tables.show', ['table' => $table['name'], 'tab' => 'columns']) }}" class="btn btn-sm btn-info ml-1" title="{{ __('Lihat Struktur Kolom') }}">
                        <i class="fas fa-columns mr-1"></i> Columns
                      </a>
                    </div>
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
