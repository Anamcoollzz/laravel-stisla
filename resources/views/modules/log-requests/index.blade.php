@extends('layouts.app')

@section('title', __('Log Requests'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Apakah Anda yakin?')),
        text: @json(__('Data log request ini akan dihapus secara permanen!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    $(document).on('click', '#btnSubmitClear', function(e) {
      e.preventDefault();
      var form = $('#formClearLogs');
      var scopeText = $('#selectScope option:selected').text();
      swal({
        title: @json(__('Konfirmasi Pembersihan Log')),
        text: @json(__('Apakah Anda yakin ingin membersihkan: ')) + scopeText + '?',
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
      swal(@json(__('Gagal')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Log Requests') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Access Control') }}</div>
      <div class="breadcrumb-item">{{ __('Log Requests') }}</div>
    </div>
  </div>

  <div class="section-body">
    <!-- Statistics Widget -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-history"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Request') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['total']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-calendar-day"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Hari Ini') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['today']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-edit"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Write Ops (POST/PUT/DEL)') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['write']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-bolt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('AJAX Requests') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($stats['ajax']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Card -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-filter mr-2 text-primary"></i> {{ __('Filter Pencarian') }}</h4>
        <div class="card-header-action">
          @can('log-requests.delete')
            <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modalClearLogs">
              <i class="fas fa-trash-alt mr-1"></i> {{ __('Bersihkan Log') }}
            </button>
          @endcan
        </div>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('log-requests.index') }}">
          <div class="row">
            <div class="col-md-3 col-12 mb-3">
              <label class="font-weight-bold">{{ __('Pencarian') }}</label>
              <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="URI, IP, User Agent, User...">
            </div>
            <div class="col-md-2 col-6 mb-3">
              <label class="font-weight-bold">{{ __('Method') }}</label>
              <select name="method" class="form-control">
                <option value="">{{ __('Semua Method') }}</option>
                @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $m)
                  <option value="{{ $m }}" {{ $filters['method'] === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 col-6 mb-3">
              <label class="font-weight-bold">{{ __('Device') }}</label>
              <select name="device" class="form-control">
                <option value="">{{ __('Semua Device') }}</option>
                @foreach (['Desktop', 'Mobile', 'Tablet', 'Bot / Crawler'] as $d)
                  <option value="{{ $d }}" {{ $filters['device'] === $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 col-6 mb-3">
              <label class="font-weight-bold">{{ __('Tipe Request') }}</label>
              <select name="is_ajax" class="form-control">
                <option value="">{{ __('Semua') }}</option>
                <option value="1" {{ $filters['is_ajax'] === '1' ? 'selected' : '' }}>{{ __('AJAX') }}</option>
                <option value="0" {{ $filters['is_ajax'] === '0' ? 'selected' : '' }}>{{ __('Standard (Non-AJAX)') }}</option>
              </select>
            </div>
            <div class="col-md-3 col-6 mb-3">
              <label class="font-weight-bold">{{ __('Tanggal (Dari - Sampai)') }}</label>
              <div class="input-group">
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
              </div>
            </div>
            <div class="col-12 text-right">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> {{ __('Terapkan Filter') }}</button>
              <a href="{{ route('log-requests.index') }}" class="btn btn-secondary ml-1"><i class="fas fa-undo mr-1"></i> {{ __('Reset') }}</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-list mr-2 text-primary"></i> {{ __('Daftar Log Request') }}</h4>
        <div class="card-header-action text-muted small">
          Menampilkan {{ $logRequests->firstItem() ?? 0 }} - {{ $logRequests->lastItem() ?? 0 }} dari {{ $logRequests->total() }} entri
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th style="width: 60px;">#ID</th>
                <th style="width: 150px;">{{ __('Waktu') }}</th>
                <th style="width: 80px;">{{ __('Method') }}</th>
                <th>{{ __('URI') }}</th>
                <th style="width: 170px;">{{ __('IP & Device') }}</th>
                <th style="width: 160px;">{{ __('User') }}</th>
                <th class="text-center" style="width: 70px;">{{ __('AJAX') }}</th>
                <th class="text-center" style="width: 100px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($logRequests as $log)
                <tr>
                  <td class="font-weight-bold text-muted">#{{ $log->id }}</td>
                  <td>
                    <div class="font-weight-600" style="font-size: 13px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                  </td>
                  <td>
                    @php
                      $methodBadges = [
                        'GET' => 'badge-success',
                        'POST' => 'badge-primary',
                        'PUT' => 'badge-warning',
                        'PATCH' => 'badge-warning',
                        'DELETE' => 'badge-danger',
                      ];
                      $badgeClass = $methodBadges[$log->method] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold">{{ $log->method }}</span>
                  </td>
                  <td>
                    <div class="font-weight-600 text-truncate" style="max-width: 320px;" title="{{ $log->uri }}">
                      <a href="{{ route('log-requests.show', $log->id) }}" class="text-dark">
                        <code>{{ $log->uri }}</code>
                      </a>
                    </div>
                    @if ($log->query_string)
                      <small class="text-muted text-truncate d-block" style="max-width: 320px;" title="?{{ $log->query_string }}">
                        ?{{ $log->query_string }}
                      </small>
                    @endif
                  </td>
                  <td>
                    <div class="font-weight-600" style="font-size: 13px;">{{ $log->ip ?? '-' }}</div>
                    <div class="mt-1">
                      @if ($log->device === 'Mobile')
                        <span class="badge badge-success px-1 py-0 small"><i class="fas fa-mobile-alt mr-1"></i> Mobile</span>
                      @elseif ($log->device === 'Tablet')
                        <span class="badge badge-warning px-1 py-0 small"><i class="fas fa-tablet-alt mr-1"></i> Tablet</span>
                      @elseif ($log->device === 'Bot / Crawler')
                        <span class="badge badge-dark px-1 py-0 small"><i class="fas fa-robot mr-1"></i> Bot</span>
                      @else
                        <span class="badge badge-info px-1 py-0 small"><i class="fas fa-desktop mr-1"></i> Desktop</span>
                      @endif

                      @if ($log->browser)
                        <small class="text-muted ml-1">{{ $log->browser }}</small>
                      @endif
                    </div>
                  </td>
                  <td>
                    @if ($log->user)
                      <div class="font-weight-600">{{ $log->user->name }}</div>
                      @if (!empty($log->roles))
                        <div>
                          @foreach ($log->roles as $role)
                            <span class="badge badge-light border text-primary small py-0">{{ $role }}</span>
                          @endforeach
                        </div>
                      @endif
                    @else
                      <span class="badge badge-secondary small">{{ __('Guest / Tamu') }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if ($log->is_ajax)
                      <span class="badge badge-info"><i class="fas fa-check"></i></span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <a href="{{ route('log-requests.show', $log->id) }}" class="btn btn-sm btn-info" title="{{ __('Detail') }}">
                        <i class="fas fa-eye"></i>
                      </a>
                      @can('log-requests.delete')
                        <form action="{{ route('log-requests.destroy', $log->id) }}" method="POST" class="d-inline ml-1">
                          @csrf
                          @method('DELETE')
                          <button type="button" class="btn btn-sm btn-danger btn-delete" title="{{ __('Hapus') }}">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      @endcan
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-3x mb-2 d-block text-secondary"></i>
                    {{ __('Belum ada data log request yang tercatat.') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if ($logRequests->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
          <span class="small text-muted">Halaman {{ $logRequests->currentPage() }} dari {{ $logRequests->lastPage() }}</span>
          {{ $logRequests->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- Modal Clear Logs -->
  @can('log-requests.delete')
    <div class="modal fade" id="modalClearLogs" tabindex="-1" role="dialog" aria-labelledby="modalClearLogsLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="modalClearLogsLabel">
              <i class="fas fa-trash-alt text-danger mr-2"></i> {{ __('Bersihkan Log Request') }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('log-requests.clear') }}" method="POST" id="formClearLogs">
            @csrf
            @method('DELETE')
            <div class="modal-body">
              <div class="form-group">
                <label class="font-weight-bold">{{ __('Pilih Cakupan Pembersihan') }}</label>
                <select name="scope" id="selectScope" class="form-control">
                  <option value="30_days">{{ __('Hapus log yang lebih lama dari 30 hari') }}</option>
                  <option value="7_days">{{ __('Hapus log yang lebih lama dari 7 hari') }}</option>
                  <option value="all">{{ __('Hapus SEMUA log request (Kosongkan tabel)') }}</option>
                </select>
                <small class="form-text text-muted">
                  Tindakan ini tidak dapat dibatalkan. Log yang dihapus akan hilang permanen dari database.
                </small>
              </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Batal') }}</button>
              <button type="button" class="btn btn-danger" id="btnSubmitClear">{{ __('Proses Pembersihan') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endcan
@endsection
