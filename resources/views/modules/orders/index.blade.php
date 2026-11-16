@extends('layouts.app')

@section('title', __('Shop › Orders'))

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

    $("#table-orders").dataTable({
      "pageLength": 15,
      "order": [[7, "desc"]],
      "columnDefs": [
        { "sortable": false, "targets": [8] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This order will be permanently deleted!')),
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
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Orders') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('orders.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Orders') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Orders Management') }}</h2>
    <p class="section-lead">
      {{ __('Monitor customer orders, verify payment proofs, and update fulfillment statuses.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-shopping-bag"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Orders') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Pending / Process') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['pending'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Need Verification') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['waiting_verification'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-wallet"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Paid Revenue') }}</h4>
            </div>
            <div class="card-body" style="font-size: 16px;">
              Rp {{ number_format($stats['revenue'], 0, ',', '.') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Card -->
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-filter mr-1"></i> {{ __('Filter Orders') }}</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('orders.index') }}" method="GET">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="search">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="{{ __('Order #, Name, Phone, Email') }}" value="{{ $filters['search'] }}">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="status">{{ __('Order Status') }}</label>
                <select name="status" id="status" class="form-control">
                  <option value="">{{ __('All Status') }}</option>
                  @foreach (['pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                    <option value="{{ $val }}" @selected($filters['status'] === $val)>{{ __($label) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="payment_status">{{ __('Payment Status') }}</label>
                <select name="payment_status" id="payment_status" class="form-control">
                  <option value="">{{ __('All Payment') }}</option>
                  @foreach (['unpaid' => 'Unpaid', 'waiting_verification' => 'Waiting Verification', 'paid' => 'Paid', 'failed' => 'Failed', 'expired' => 'Expired'] as $val => $label)
                    <option value="{{ $val }}" @selected($filters['payment_status'] === $val)>{{ __($label) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="payment_type">{{ __('Payment Type') }}</label>
                <select name="payment_type" id="payment_type" class="form-control">
                  <option value="">{{ __('All Types') }}</option>
                  @foreach ($paymentTypes as $type)
                    <option value="{{ $type }}" @selected($filters['payment_type'] === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="has_proof">{{ __('Payment Proof') }}</label>
                <select name="has_proof" id="has_proof" class="form-control">
                  <option value="">{{ __('All') }}</option>
                  <option value="1" @selected($filters['has_proof'] === '1')>{{ __('Uploaded') }}</option>
                  <option value="0" @selected($filters['has_proof'] === '0')>{{ __('Not Uploaded') }}</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-md-0">
                <label for="date_from">{{ __('Date From') }}</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filters['date_from'] }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-md-0">
                <label for="date_to">{{ __('Date To') }}</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filters['date_to'] }}">
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary mr-2">
                  <i class="fas fa-search mr-1"></i> {{ __('Apply Filter') }}
                </button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                  <i class="fas fa-undo mr-1"></i> {{ __('Reset') }}
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Table Card -->
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Order List') }} ({{ $orders->count() }})</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-orders">
            <thead>
              <tr>
                <th>{{ __('Order #') }}</th>
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Items') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Order Status') }}</th>
                <th>{{ __('Payment Status') }}</th>
                <th>{{ __('Proof') }}</th>
                <th>{{ __('Date') }}</th>
                <th class="text-center" style="width: 110px;">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($orders as $order)
                @php
                  $statusBadge = match ($order->status) {
                      'completed' => 'badge-success',
                      'shipped', 'processing' => 'badge-info',
                      'cancelled' => 'badge-danger',
                      default => 'badge-warning',
                  };
                  $paymentBadge = match ($order->payment_status) {
                      'paid' => 'badge-success',
                      'waiting_verification' => 'badge-info',
                      'failed', 'expired' => 'badge-danger',
                      default => 'badge-warning',
                  };
                @endphp
                <tr>
                  <td>
                    <a href="{{ route('orders.show', $order) }}" class="font-weight-bold">
                      #{{ $order->order_number }}
                    </a>
                  </td>
                  <td>
                    <div class="font-weight-600">{{ $order->recipient_name }}</div>
                    <div class="text-muted small">{{ $order->phone_number }}</div>
                  </td>
                  <td>
                    <span class="badge badge-light">{{ $order->items->sum('quantity') }} pcs ({{ $order->items->count() }} item)</span>
                  </td>
                  <td class="font-weight-600">{{ $order->formatted_grand_total }}</td>
                  <td>
                    <span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
                  </td>
                  <td>
                    <span class="badge {{ $paymentBadge }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                  </td>
                  <td>
                    @if ($order->payment_proof_url)
                      <a href="{{ $order->payment_proof_url }}" target="_blank" class="badge badge-success">
                        <i class="fas fa-check-circle mr-1"></i> {{ __('Uploaded') }}
                      </a>
                    @else
                      <span class="badge badge-light text-muted">{{ __('None') }}</span>
                    @endif
                  </td>
                  <td data-order="{{ $order->created_at?->timestamp }}">
                    {{ $order->created_at?->format('d M Y H:i') }}
                  </td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      @can('orders.view')
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      @endcan
                      @can('orders.delete')
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="button" class="btn btn-sm btn-danger btn-delete" title="{{ __('Delete') }}">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      @endcan
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
