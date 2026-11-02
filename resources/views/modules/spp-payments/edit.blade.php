@extends('layouts.app')

@section('title', __('Modules › Edit SPP Payment'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('spp-payments.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit SPP Payment') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('spp-payments.index') }}">{{ __('SPP Payments') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit SPP Payment') }}</h2>
    <p class="section-lead">
      {{ __('Update the SPP payment information below.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <form action="{{ route('spp-payments.update', $sppPayment) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('SPP Payment Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="invoice_number">{{ __('Invoice Number') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="invoice_number"
                    name="invoice_number"
                    class="form-control @error('invoice_number') is-invalid @enderror"
                    value="{{ old('invoice_number', $sppPayment->invoice_number) }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('invoice_number')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the invoice number.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="student_id">{{ __('Student') }} <span class="text-danger">*</span></label>
                  <select
                    id="student_id"
                    name="student_id"
                    class="form-control @error('student_id') is-invalid @enderror"
                    required=""
                  >
                    <option value="">-- {{ __('Select Student') }} --</option>
                    @foreach ($students as $student)
                      <option value="{{ $student->id }}" @selected((string) old('student_id', $sppPayment->student_id) === (string) $student->id)>
                        {{ $student->nis }} - {{ $student->name }} ({{ $student->schoolClass?->class_name ?? '-' }})
                      </option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">
                    @error('student_id')
                      {{ $message }}
                    @else
                      {{ __('Please select a student.') }}
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="month">{{ __('Month') }} <span class="text-danger">*</span></label>
                  <select
                    id="month"
                    name="month"
                    class="form-control @error('month') is-invalid @enderror"
                    required=""
                  >
                    <option value="">-- {{ __('Select Month') }} --</option>
                    @foreach ($months as $monthNumber => $monthLabel)
                      <option value="{{ $monthNumber }}" @selected((string) old('month', $sppPayment->month) === (string) $monthNumber)>
                        {{ __($monthLabel) }}
                      </option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">
                    @error('month')
                      {{ $message }}
                    @else
                      {{ __('Please select a month.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-4">
                  <label for="year">{{ __('Year') }} <span class="text-danger">*</span></label>
                  <input
                    type="number"
                    id="year"
                    name="year"
                    class="form-control @error('year') is-invalid @enderror"
                    value="{{ old('year', $sppPayment->year) }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('year')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the year.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-4">
                  <label for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                  <input
                    type="number"
                    id="amount"
                    name="amount"
                    class="form-control @error('amount') is-invalid @enderror"
                    value="{{ old('amount', $sppPayment->amount) }}"
                    min="0"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('amount')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the amount.') }}
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="payment_date">{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                  <input
                    type="date"
                    id="payment_date"
                    name="payment_date"
                    class="form-control @error('payment_date') is-invalid @enderror"
                    value="{{ old('payment_date', $sppPayment->payment_date?->format('Y-m-d')) }}"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('payment_date')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the payment date.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-4">
                  <label for="payment_method">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                  <select
                    id="payment_method"
                    name="payment_method"
                    class="form-control @error('payment_method') is-invalid @enderror"
                    required=""
                  >
                    <option value="">-- {{ __('Select Payment Method') }} --</option>
                    <option value="cash" @selected(old('payment_method', $sppPayment->payment_method) === 'cash')>{{ __('Cash') }}</option>
                    <option value="transfer" @selected(old('payment_method', $sppPayment->payment_method) === 'transfer')>{{ __('Bank Transfer') }}</option>
                  </select>
                  <div class="invalid-feedback">
                    @error('payment_method')
                      {{ $message }}
                    @else
                      {{ __('Please select a payment method.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-4">
                  <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                  <select
                    id="status"
                    name="status"
                    class="form-control @error('status') is-invalid @enderror"
                    required=""
                  >
                    <option value="">-- {{ __('Select Status') }} --</option>
                    <option value="paid" @selected(old('status', $sppPayment->status) === 'paid')>{{ __('Paid') }}</option>
                    <option value="unpaid" @selected(old('status', $sppPayment->status) === 'unpaid')>{{ __('Unpaid') }}</option>
                  </select>
                  <div class="invalid-feedback">
                    @error('status')
                      {{ $message }}
                    @else
                      {{ __('Please select a status.') }}
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group mb-0">
                <label for="notes">{{ __('Notes') }}</label>
                <textarea
                  id="notes"
                  name="notes"
                  class="form-control @error('notes') is-invalid @enderror"
                  rows="3"
                >{{ old('notes', $sppPayment->notes) }}</textarea>
                @error('notes')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('spp-payments.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

