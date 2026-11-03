@extends('layouts.app')

@section('title', __('Modules › SPP Payment Detail'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('spp-payments.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('SPP Payment Detail') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('spp-payments.index') }}">{{ __('SPP Payments') }}</a></div>
      <div class="breadcrumb-item">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $sppPayment->invoice_number }}</h2>
    <p class="section-lead">
      {{ __('Detail information about this SPP payment.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('SPP Payment Information') }}</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('ID') }}</label>
                <div>{{ $sppPayment->id }}</div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Invoice Number') }}</label>
                <div>{{ $sppPayment->invoice_number }}</div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Student') }}</label>
                <div>{{ $sppPayment->student?->nis }} - {{ $sppPayment->student?->name ?? '-' }}</div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Class') }}</label>
                <div>{{ $sppPayment->student?->schoolClass?->class_name ?? '-' }}</div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Period') }}</label>
                <div>{{ $sppPayment->monthName() }} {{ $sppPayment->year }}</div>
              </div>
              <div class="form-group col-md-6">
                <label class="d-block">{{ __('Amount') }}</label>
                <div>Rp {{ number_format($sppPayment->amount, 0, ',', '.') }}</div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label class="d-block">{{ __('Payment Date') }}</label>
                <div>{{ $sppPayment->payment_date?->format('Y-m-d') }}</div>
              </div>
              <div class="form-group col-md-4">
                <label class="d-block">{{ __('Payment Method') }}</label>
                <div>{{ $sppPayment->payment_method === 'cash' ? __('Cash') : __('Bank Transfer') }}</div>
              </div>
              <div class="form-group col-md-4">
                <label class="d-block">{{ __('Status') }}</label>
                <div>
                  @if ($sppPayment->status === 'paid')
                    <span class="badge badge-success">{{ __('Paid') }}</span>
                  @else
                    <span class="badge badge-warning">{{ __('Unpaid') }}</span>
                  @endif
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="d-block">{{ __('Notes') }}</label>
              <div>{{ $sppPayment->notes ?? '-' }}</div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-0">
                <label class="d-block">{{ __('Created At') }}</label>
                <div>{{ $sppPayment->created_at?->format('Y-m-d H:i:s') }}</div>
              </div>
              <div class="form-group col-md-6 mb-0">
                <label class="d-block">{{ __('Updated At') }}</label>
                <div>{{ $sppPayment->updated_at?->format('Y-m-d H:i:s') }}</div>
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('spp-payments.index') }}" class="btn btn-secondary btn-sm btn-icon mr-1" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
            @can('spp-payments.edit')
              <a href="{{ route('spp-payments.edit', $sppPayment) }}" class="btn btn-primary btn-sm btn-icon" data-toggle="tooltip" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

