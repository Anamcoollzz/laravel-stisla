@extends('layouts.pdf')

@section('title', __('SPP Payment List'))
@section('total', $sppPayments->count())

@section('content')
  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 35px;">#</th>
        <th style="width: 110px;">{{ __('Invoice Number') }}</th>
        <th>{{ __('Student Name') }}</th>
        <th style="width: 80px;">{{ __('Class') }}</th>
        <th style="width: 90px;">{{ __('Period') }}</th>
        <th class="text-right" style="width: 95px;">{{ __('Amount') }}</th>
        <th style="width: 80px;">{{ __('Payment Date') }}</th>
        <th style="width: 85px;">{{ __('Method') }}</th>
        <th class="text-center" style="width: 75px;">{{ __('Status') }}</th>
      </tr>
    </thead>
    <tbody>
      @php $grandTotal = 0; @endphp
      @forelse ($sppPayments as $payment)
        @php $grandTotal += $payment->amount; @endphp
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td><code>{{ $payment->invoice_number }}</code></td>
          <td><strong>{{ $payment->student?->name ?? '-' }}</strong></td>
          <td>{{ $payment->student?->schoolClass?->class_name ?? '-' }}</td>
          <td>{{ $payment->monthName() }} {{ $payment->year }}</td>
          <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
          <td>{{ $payment->payment_date?->format('Y-m-d') }}</td>
          <td>{{ $payment->payment_method === 'cash' ? __('Cash') : __('Bank Transfer') }}</td>
          <td class="text-center">
            @if ($payment->status === 'paid')
              <span class="badge badge-success">{{ __('Paid') }}</span>
            @else
              <span class="badge badge-warning">{{ __('Unpaid') }}</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="text-center">{{ __('No data available.') }}</td>
        </tr>
      @endforelse
    </tbody>
    @if ($sppPayments->isNotEmpty())
      <tfoot>
        <tr>
          <th colspan="5" class="text-right">{{ __('Grand Total') }}:</th>
          <th class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
          <th colspan="3"></th>
        </tr>
      </tfoot>
    @endif
  </table>
@endsection

