@extends('layouts.app')

@section('title', __('Akun Bank Pembayaran'))

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

    $("#table-payment-banks").dataTable({
      "pageLength": 10,
      "order": [[5, "asc"]],
      "columnDefs": [
        { "sortable": false, "targets": [1, 7] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Apakah Anda yakin?')),
        text: @json(__('Akun bank ini akan dihapus permanen!')),
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
    <h1>{{ __('Akun Bank Pembayaran') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Bank Pembayaran') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Kelola Rekening Bank') }}</h2>
    <p class="section-lead">
      {{ __('Atur daftar rekening bank tujuan transfer pembayaran untuk pesanan manual customer.') }}
    </p>

    <!-- Stats Row -->
    <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-university"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Bank') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Bank Aktif') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['active'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Nonaktif') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['inactive'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Table Card -->
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Daftar Rekening Bank') }}</h4>
        <div class="card-header-action">
          <a href="{{ route('payment-banks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('Tambah Rekening Bank') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-payment-banks">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 70px;">{{ __('Logo') }}</th>
                <th>{{ __('Nama Bank') }}</th>
                <th>{{ __('Nomor Rekening') }}</th>
                <th>{{ __('Atas Nama') }}</th>
                <th style="width: 70px;">{{ __('Urutan') }}</th>
                <th style="width: 90px;">{{ __('Status') }}</th>
                <th style="width: 140px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($banks as $index => $item)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>
                    @if ($item->logo_url)
                      <img src="{{ $item->logo_url }}" alt="{{ $item->bank_name }}" style="width: 50px; height: 32px; object-fit: contain; border-radius: 4px; border: 1px solid #eee; padding: 2px;">
                    @else
                      <div class="bg-light text-muted d-flex align-items-center justify-content-center" style="width: 50px; height: 32px; border-radius: 4px;">
                        <i class="fas fa-building"></i>
                      </div>
                    @endif
                  </td>
                  <td>
                    <strong>{{ $item->bank_name }}</strong>
                    @if ($item->bank_code)
                      <span class="badge badge-light ml-1 font-weight-normal">Kode: <code>{{ $item->bank_code }}</code></span>
                    @endif
                    @if ($item->branch)
                      <div class="text-muted small">Cabang: {{ $item->branch }}</div>
                    @endif
                  </td>
                  <td>
                    <span class="font-weight-bold font-monospace text-dark" style="font-size: 15px;">{{ $item->account_number }}</span>
                  </td>
                  <td>{{ $item->account_name }}</td>
                  <td>{{ $item->order }}</td>
                  <td>
                    <form action="{{ route('payment-banks.toggle', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm badge {{ $item->is_active ? 'badge-success' : 'badge-secondary' }} border-0" style="cursor: pointer;" title="{{ __('Klik untuk ubah status') }}">
                        {{ $item->is_active ? __('Aktif') : __('Nonaktif') }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <a href="{{ route('payment-banks.edit', $item->id) }}" class="btn btn-sm btn-info mr-1" title="{{ __('Edit') }}">
                        <i class="fas fa-pencil-alt"></i>
                      </a>
                      <form action="{{ route('payment-banks.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete" title="{{ __('Hapus') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
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

