@extends('layouts.app')

@section('title', __('Metode Pembayaran'))

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

    $("#table-payment-methods").dataTable({
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
        text: @json(__('Metode pembayaran ini akan dihapus permanen!')),
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
    <h1>{{ __('Metode Pembayaran') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Metode Pembayaran') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Kelola Metode Pembayaran') }}</h2>
    <p class="section-lead">
      {{ __('Atur metode pembayaran (Cash On Delivery, Transfer Bank, E-Wallet, dll.) yang tersedia pada checkout toko.') }}
    </p>

    <!-- Stats Row -->
    <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-credit-card"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Metode') }}</h4>
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
              <h4>{{ __('Metode Aktif') }}</h4>
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
        <h4>{{ __('Daftar Metode Pembayaran') }}</h4>
        <div class="card-header-action">
          <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('Tambah Metode Pembayaran') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-payment-methods">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 70px;">{{ __('Logo') }}</th>
                <th>{{ __('Nama Metode') }}</th>
                <th>{{ __('Tipe') }}</th>
                <th>{{ __('Biaya Layanan') }}</th>
                <th style="width: 70px;">{{ __('Urutan') }}</th>
                <th style="width: 90px;">{{ __('Status') }}</th>
                <th style="width: 140px;">{{ __('Aksi') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($paymentMethods as $index => $item)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>
                    @if ($item->logo_url)
                      <img src="{{ $item->logo_url }}" alt="{{ $item->name }}" style="width: 44px; height: 44px; object-fit: contain; border-radius: 6px; border: 1px solid #eee; padding: 2px;">
                    @else
                      <div class="bg-light text-muted d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 6px;">
                        <i class="fas fa-wallet"></i>
                      </div>
                    @endif
                  </td>
                  <td>
                    <strong>{{ $item->name }}</strong>
                    @if ($item->code)
                      <span class="badge badge-light ml-1 font-weight-normal"><code>{{ $item->code }}</code></span>
                    @endif
                    @if ($item->description)
                      <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $item->description }}</div>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-info">{{ strtoupper($item->type) }}</span>
                  </td>
                  <td>
                    @if ($item->fee <= 0)
                      <span class="badge badge-success">{{ __('Bebas Biaya') }}</span>
                    @else
                      <span class="font-weight-bold text-dark">Rp {{ number_format($item->fee, 0, ',', '.') }}</span>
                    @endif
                  </td>
                  <td>{{ $item->order }}</td>
                  <td>
                    <form action="{{ route('payment-methods.toggle', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm badge {{ $item->is_active ? 'badge-success' : 'badge-secondary' }} border-0" style="cursor: pointer;" title="{{ __('Klik untuk ubah status') }}">
                        {{ $item->is_active ? __('Aktif') : __('Nonaktif') }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <a href="{{ route('payment-methods.edit', $item->id) }}" class="btn btn-sm btn-info mr-1" title="{{ __('Edit') }}">
                        <i class="fas fa-pencil-alt"></i>
                      </a>
                      <form action="{{ route('payment-methods.destroy', $item->id) }}" method="POST" class="d-inline">
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

