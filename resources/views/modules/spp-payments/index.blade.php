@extends('layouts.app')

@section('title', __('Modules › SPP Payments'))

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

    $("#table-spp-payments").dataTable({
      "columnDefs": [
        { "sortable": false, "targets": [8] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('Once deleted, you will not be able to recover this SPP payment!')),
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
    <h1>{{ __('SPP Payments') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('index.html') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('spp-payments.index') }}">{{ __('Modules') }}</a></div>
      <div class="breadcrumb-item">{{ __('SPP Payments') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('SPP Payments') }}</h2>
    <p class="section-lead">
      {{ __('Manage all SPP payments data using DataTables.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('SPP Payment List') }}</h4>
            <div class="card-header-action">
              <a href="{{ route('spp-payments.create') }}" class="btn btn-primary">
                {{ __('Add New') }}
              </a>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="table-spp-payments">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('Invoice Number') }}</th>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Period') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Payment Date') }}</th>
                    <th>{{ __('Payment Method') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sppPayments as $sppPayment)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>{{ $sppPayment->invoice_number }}</td>
                      <td>
                        {{ $sppPayment->student?->name ?? '-' }}
                        @if ($sppPayment->student?->schoolClass)
                          <div class="text-muted text-small">{{ $sppPayment->student->schoolClass->class_name }}</div>
                        @endif
                      </td>
                      <td>{{ $sppPayment->monthName() }} {{ $sppPayment->year }}</td>
                      <td>Rp {{ number_format($sppPayment->amount, 0, ',', '.') }}</td>
                      <td>{{ $sppPayment->payment_date?->format('Y-m-d') }}</td>
                      <td>{{ $sppPayment->payment_method === 'cash' ? __('Cash') : __('Bank Transfer') }}</td>
                      <td>
                        @if ($sppPayment->status === 'paid')
                          <span class="badge badge-success">{{ __('Paid') }}</span>
                        @else
                          <span class="badge badge-warning">{{ __('Unpaid') }}</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('spp-payments.show', $sppPayment) }}" class="btn btn-sm btn-icon btn-info mr-1" data-toggle="tooltip" title="{{ __('Detail') }}"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('spp-payments.edit', $sppPayment) }}" class="btn btn-sm btn-icon btn-primary mr-1" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
                        <form action="{{ route('spp-payments.destroy', $sppPayment) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-icon btn-danger btn-delete" data-toggle="tooltip" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

