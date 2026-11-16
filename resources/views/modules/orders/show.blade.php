@extends('layouts.app')

@section('title', __('Shop › Order #') . $order->order_number)

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('orders.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Order') }} #{{ $order->order_number }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('orders.index') }}">{{ __('Orders') }}</a></div>
      <div class="breadcrumb-item">#{{ $order->order_number }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-lg-8">
        <!-- Order Items -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-shopping-basket mr-2 text-primary"></i> {{ __('Order Items') }}</h4>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Product') }}</th>
                    <th class="text-center">{{ __('Price') }}</th>
                    <th class="text-center">{{ __('Qty') }}</th>
                    <th class="text-right">{{ __('Subtotal') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($order->items as $item)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          @if ($item->product_thumbnail)
                            <img src="{{ $item->product_thumbnail }}" alt="{{ $item->product_title }}" class="rounded mr-3" style="width: 48px; height: 48px; object-fit: cover;">
                          @endif
                          <div>
                            <div class="font-weight-600">{{ $item->product_title }}</div>
                            <small class="text-muted">ID: #{{ $item->product_id ?? '-' }}</small>
                          </div>
                        </div>
                      </td>
                      <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                      <td class="text-center">{{ $item->quantity }}</td>
                      <td class="text-right font-weight-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer bg-whitesmoke">
            <div class="row justify-content-end">
              <div class="col-md-6">
                <div class="d-flex justify-content-between mb-1">
                  <span>{{ __('Subtotal') }}</span>
                  <span>{{ $order->formatted_subtotal }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                  <span>{{ __('Shipping') }} ({{ $order->shipping_name }})</span>
                  <span>{{ $order->formatted_shipping_cost }}</span>
                </div>
                @if ($order->payment_fee > 0)
                  <div class="d-flex justify-content-between mb-1">
                    <span>{{ __('Payment Fee') }}</span>
                    <span>{{ $order->formatted_payment_fee }}</span>
                  </div>
                @endif
                @if ($order->discount_amount > 0)
                  <div class="d-flex justify-content-between mb-1 text-danger">
                    <span>{{ __('Discount') }} ({{ $order->coupon_code }})</span>
                    <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                  </div>
                @endif
                <div class="d-flex justify-content-between pt-2 border-top font-weight-bold text-dark" style="font-size: 16px;">
                  <span>{{ __('Grand Total') }}</span>
                  <span class="text-primary">{{ $order->formatted_grand_total }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping & Customer Details -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-truck mr-2 text-primary"></i> {{ __('Shipping & Customer Info') }}</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6 class="font-weight-bold text-dark">{{ __('Recipient & Address') }}</h6>
                <p class="mb-1"><strong>{{ $order->recipient_name }}</strong> ({{ $order->phone_number }})</p>
                <p class="text-muted mb-1">{{ $order->address_line }}</p>
                @if ($order->city || $order->state || $order->postal_code)
                  <p class="text-muted mb-1">{{ collect([$order->city, $order->state, $order->postal_code])->filter()->join(', ') }}</p>
                @endif
                @if ($order->notes)
                  <p class="text-muted small mb-0"><em>{{ __('Notes:') }} {{ $order->notes }}</em></p>
                @endif
              </div>
              <div class="col-md-6">
                <h6 class="font-weight-bold text-dark">{{ __('Shipping & Payment Method') }}</h6>
                <p class="mb-1">{{ __('Courier:') }} <strong>{{ $order->shipping_name }}</strong> ({{ $order->shipping_estimated_delivery ?: '-' }})</p>
                <p class="mb-1">{{ __('Payment:') }} <strong>{{ $order->payment_name }}</strong></p>
                @if ($order->bank_name)
                  <p class="text-muted small mb-0">{{ $order->bank_name }} &mdash; {{ $order->bank_account_number }} (a.n. {{ $order->bank_account_name }})</p>
                @endif
                @if ($order->user)
                  <p class="text-muted small mt-2 mb-0">{{ __('Account:') }} {{ $order->user->name }} ({{ $order->user->email }})</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <!-- Update Status -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-edit mr-2 text-primary"></i> {{ __('Order Status') }}</h4>
          </div>
          <div class="card-body">
            @can('orders.edit')
              <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                  <label for="status">{{ __('Order Status') }}</label>
                  <select name="status" id="status" class="form-control">
                    @foreach (['pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                      <option value="{{ $val }}" @selected($order->status === $val)>{{ __($label) }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="payment_status">{{ __('Payment Status') }}</label>
                  <select name="payment_status" id="payment_status" class="form-control">
                    @foreach (['unpaid' => 'Unpaid', 'waiting_verification' => 'Waiting Verification', 'paid' => 'Paid', 'failed' => 'Failed', 'expired' => 'Expired'] as $val => $label)
                      <option value="{{ $val }}" @selected($order->payment_status === $val)>{{ __($label) }}</option>
                    @endforeach
                  </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fas fa-save mr-1"></i> {{ __('Update Status') }}
                </button>
              </form>
            @else
              <p class="mb-1">{{ __('Order Status:') }} <span class="badge badge-info">{{ ucfirst($order->status) }}</span></p>
              <p class="mb-0">{{ __('Payment Status:') }} <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></p>
            @endcan
          </div>
        </div>

        <!-- Payment Proof -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-receipt mr-2 text-primary"></i> {{ __('Payment Proof') }}</h4>
          </div>
          <div class="card-body text-center">
            @if ($order->payment_proof_url)
              @php
                $isPdfProof = str_ends_with(strtolower($order->payment_proof ?? ''), '.pdf');
              @endphp
              @if ($isPdfProof)
                <div class="py-4 bg-light rounded mb-3">
                  <i class="fas fa-file-pdf fa-3x text-danger"></i>
                  <p class="mt-2 mb-0 small text-muted">{{ __('PDF Payment Proof') }}</p>
                </div>
              @else
                <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener">
                  <img src="{{ $order->payment_proof_url }}" alt="{{ __('Payment Proof') }}" class="img-fluid rounded border mb-3" style="max-height: 260px; object-fit: contain;">
                </a>
              @endif
              <p class="text-muted small mb-3">
                {{ __('Uploaded at:') }} {{ $order->payment_proof_uploaded_at?->format('d M Y H:i') ?? '-' }}
              </p>
              <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-block">
                <i class="fas fa-external-link-alt mr-1"></i> {{ __('Open Full File') }}
              </a>
            @else
              <div class="py-4 text-muted">
                <i class="fas fa-image fa-3x mb-2 d-block text-light"></i>
                <p class="mb-0 small">{{ __('Customer has not uploaded payment proof yet.') }}</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Shipping Receipt / Resi -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-shipping-fast mr-2 text-primary"></i> {{ __('Shipping Receipt (Resi)') }}</h4>
          </div>
          <div class="card-body">
            @if ($order->tracking_proof_url)
              @php
                $isPdfTracking = str_ends_with(strtolower($order->tracking_proof ?? ''), '.pdf');
              @endphp
              <div class="text-center mb-3">
                @if ($isPdfTracking)
                  <div class="py-3 bg-light rounded mb-2">
                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                    <p class="mt-1 mb-0 small text-muted">{{ __('PDF Shipping Receipt') }}</p>
                  </div>
                @else
                  <a href="{{ $order->tracking_proof_url }}" target="_blank" rel="noopener">
                    <img src="{{ $order->tracking_proof_url }}" alt="{{ __('Shipping Receipt') }}" class="img-fluid rounded border mb-2" style="max-height: 200px; object-fit: contain;">
                  </a>
                @endif
                <a href="{{ $order->tracking_proof_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary btn-block">
                  <i class="fas fa-external-link-alt mr-1"></i> {{ __('Open Receipt File') }}
                </a>
              </div>
            @endif

            @can('orders.edit')
              <form action="{{ route('orders.updateTracking', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group">
                  <label for="tracking_number">{{ __('Tracking Number (No. Resi)') }}</label>
                  <input type="text" name="tracking_number" id="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. JNE1234567890">
                  @error('tracking_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="tracking_proof">{{ __('Receipt Proof (Bukti Resi)') }}</label>
                  <input type="file" name="tracking_proof" id="tracking_proof" accept=".jpg,.jpeg,.png,.webp,.pdf" class="form-control-file @error('tracking_proof') is-invalid @enderror">
                  <small class="form-text text-muted">{{ __('JPG, PNG, WebP, PDF (Max 4MB).') }}</small>
                  @error('tracking_proof')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                @if ($order->tracking_proof)
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remove_tracking_proof" value="1" class="custom-control-input" id="remove_tracking_proof">
                      <label class="custom-control-label text-danger" for="remove_tracking_proof">{{ __('Remove uploaded receipt proof') }}</label>
                    </div>
                  </div>
                @endif

                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fas fa-save mr-1"></i> {{ __('Save Receipt / Resi') }}
                </button>
              </form>
            @else
              <p class="mb-0">{{ __('No. Resi:') }} <strong>{{ $order->tracking_number ?: '-' }}</strong></p>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
