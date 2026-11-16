@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - Ecommerce')

@section('vendor-css')
  <link href="{{ asset('mantu/assets/css/vendor/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/animate.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/slick.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/swiper-bundle.min.css') }}" rel="stylesheet">
@endsection

@section('vendor-js')
  <script src="{{ asset('mantu/assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
@endsection

@section('content')
<div class="mn-main-content">
	<!-- Breadcrumb -->
	<div class="mn-breadcrumb m-b-30">
		<div class="row">
			<div class="col-12">
				<div class="row gi_breadcrumb_inner">
					<div class="col-md-6 col-sm-12">
						<h2 class="mn-breadcrumb-title">Detail Checkout Pesanan</h2>
					</div>
					<div class="col-md-6 col-sm-12">
						<ul class="mn-breadcrumb-list">
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.shop') }}">Shop</a></li>
							<li class="mn-breadcrumb-item active">Detail Pesanan</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Checkout Detail Section -->
	<section class="mn-checkout-detail-section p-b-40">
		@if (session('success'))
			<div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
				<div class="d-flex align-items-center">
					<i class="ri-checkbox-circle-fill text-success fs-3 me-2"></i>
					<div>
						<h5 class="alert-heading mb-0 fw-bold">Pesanan Berhasil Dibuat!</h5>
						<p class="mb-0 small">{{ session('success') }}</p>
					</div>
				</div>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<!-- Order Status Header Card -->
		<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff;">
			<div class="card-body p-4">
				<div class="row align-items-center g-3">
					<div class="col-md-7">
						<span class="badge bg-white text-primary fw-bold px-3 py-1 mb-2" style="font-size: 12px; border-radius: 20px;">
							<i class="ri-shopping-bag-3-line me-1"></i> No. Pesanan: #{{ $order->order_number }}
						</span>
						<h3 class="fw-bold mb-1 text-white" style="font-size: 22px;">Terima Kasih Atas Pesanan Anda!</h3>
						<p class="mb-0 text-white-50 small">
							<i class="ri-calendar-line me-1"></i> {{ $order->created_at->format('d F Y, H:i') }} WIB &bull; 
							<i class="ri-user-3-line me-1"></i> {{ $order->recipient_name }}
						</p>
					</div>
					<div class="col-md-5 text-md-end">
						<div class="d-inline-flex flex-column align-items-md-end gap-1">
							<div>
								<span class="text-white-50 small d-block">Status Pesanan:</span>
								@if ($order->status === 'completed')
									<span class="badge bg-success px-3 py-2 fs-6">Selesai</span>
								@elseif ($order->status === 'cancelled')
									<span class="badge bg-danger px-3 py-2 fs-6">Dibatalkan</span>
								@elseif ($order->status === 'processing' || $order->status === 'shipped')
									<span class="badge bg-info text-dark px-3 py-2 fs-6">Diproses / Dikirim</span>
								@else
									<span class="badge bg-warning text-dark px-3 py-2 fs-6">Menunggu Pembayaran</span>
								@endif
							</div>
							<div class="mt-1">
								<span class="badge bg-light text-dark px-2 py-1 small">
									Status Bayar: <strong>{{ strtoupper($order->payment_status) }}</strong>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<!-- Left Column: Details -->
			<div class="col-lg-8 col-md-12">
				<!-- 1. DATA PRODUK -->
				<div class="card border shadow-sm mb-4" style="border-radius: 12px;">
					<div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
						<h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
							<i class="ri-shopping-basket-line me-1 text-primary"></i> 1. Data Produk Yang Dipesan
						</h5>
						<span class="badge bg-light text-dark border">{{ $order->items->count() }} Item</span>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table align-middle mb-0">
								<thead class="table-light">
									<tr>
										<th scope="col" class="ps-4" style="font-size: 13px;">Produk</th>
										<th scope="col" class="text-center" style="font-size: 13px;">Harga Satuan</th>
										<th scope="col" class="text-center" style="font-size: 13px;">Jumlah</th>
										<th scope="col" class="text-end pe-4" style="font-size: 13px;">Total</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($order->items as $item)
										<tr>
											<td class="ps-4 py-3">
												<div class="d-flex align-items-center gap-3">
													@if ($item->product_thumbnail)
														<img src="{{ $item->product_thumbnail }}" alt="{{ $item->product_title }}" style="width: 55px; height: 55px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
													@else
														<div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 55px; height: 55px; font-size: 20px;">
															<i class="ri-image-line"></i>
														</div>
													@endif
													<div>
														<h6 class="mb-1 fw-bold" style="font-size: 14px;">
															@if ($item->product_slug)
																<a href="{{ route('ecommerce.products.detail', $item->product_slug) }}" class="text-dark">{{ $item->product_title }}</a>
															@else
																{{ $item->product_title }}
															@endif
														</h6>
														<small class="text-muted">Item ID: #{{ $item->product_id ?? '-' }}</small>
													</div>
												</div>
											</td>
											<td class="text-center py-3 text-muted" style="font-size: 14px;">
												Rp {{ number_format($item->price, 0, ',', '.') }}
											</td>
											<td class="text-center py-3 fw-bold" style="font-size: 14px;">
												{{ $item->quantity }}
											</td>
											<td class="text-end pe-4 py-3 fw-bold text-dark" style="font-size: 14px;">
												Rp {{ number_format($item->subtotal, 0, ',', '.') }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- 2. DATA PENGIRIMAN -->
				<div class="card border shadow-sm mb-4" style="border-radius: 12px;">
					<div class="card-header bg-white py-3 border-bottom">
						<h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
							<i class="ri-truck-line me-1 text-primary"></i> 2. Data Pengiriman
						</h5>
					</div>
					<div class="card-body p-4">
						<div class="row g-4">
							<!-- Shipping Address Card -->
							<div class="col-md-6">
								<div class="p-3 border rounded-3 h-100 bg-light">
									<div class="d-flex align-items-center justify-content-between mb-2">
										<span class="badge bg-secondary-subtle text-secondary border fw-bold" style="font-size: 11px;">
											<i class="ri-map-pin-line me-1"></i> Alamat Pengiriman
										</span>
									</div>
									<h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ $order->recipient_name }}</h6>
									<p class="text-secondary small mb-2"><i class="ri-phone-line me-1"></i> {{ $order->phone_number }}</p>
									<p class="text-muted small mb-1" style="line-height: 1.5;">
										{{ $order->address_line }}
									</p>
									@if ($order->city || $order->state || $order->postal_code)
										<p class="text-muted small mb-0">
											<i class="ri-building-line me-1"></i> {{ collect([$order->city, $order->state, $order->postal_code])->filter()->join(', ') }}
										</p>
									@endif
									@if ($order->notes)
										<div class="mt-2 pt-2 border-top">
											<small class="text-secondary fst-italic"><strong>Catatan:</strong> {{ $order->notes }}</small>
										</div>
									@endif
								</div>
							</div>

							<!-- Shipping Courier Card -->
							<div class="col-md-6">
								<div class="p-3 border rounded-3 h-100 bg-light">
									<div class="d-flex align-items-center justify-content-between mb-2">
										<span class="badge bg-info-subtle text-info-emphasis border fw-bold" style="font-size: 11px;">
											<i class="ri-truck-fill me-1"></i> Ekspedisi / Kurir
										</span>
										@if ($order->shipping_cost <= 0)
											<span class="badge bg-success fw-bold" style="font-size: 11px;">Gratis Ongkir</span>
										@endif
									</div>
									<div class="d-flex align-items-center gap-2 mb-2">
										@if ($order->shippingMethod && $order->shippingMethod->logo_url)
											<img src="{{ $order->shippingMethod->logo_url }}" alt="{{ $order->shipping_name }}" style="max-height: 28px; max-width: 65px; object-fit: contain;">
										@endif
										<h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">{{ $order->shipping_name }}</h6>
									</div>
									<div class="mb-2">
										<span class="text-muted small">Tarif Ongkir: </span>
										<strong class="text-dark small">
											{{ $order->shipping_cost <= 0 ? 'Rp 0 (Gratis Ongkir)' : 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') }}
										</strong>
									</div>
									@if ($order->shipping_estimated_delivery)
										<div class="text-muted small">
											<i class="ri-time-line me-1"></i> Estimasi Sampai: <strong>{{ $order->shipping_estimated_delivery }}</strong>
										</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- 3. DATA PEMBAYARAN -->
				<div class="card border shadow-sm mb-4" style="border-radius: 12px;">
					<div class="card-header bg-white py-3 border-bottom">
						<h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
							<i class="ri-bank-card-line me-1 text-primary"></i> 3. Data Pembayaran
						</h5>
					</div>
					<div class="card-body p-4">
						<div class="row g-4">
							<div class="col-md-6">
								<div class="p-3 border rounded-3 h-100 bg-light">
									<span class="badge bg-primary-subtle text-primary border fw-bold mb-2" style="font-size: 11px;">
										Metode Pembayaran
									</span>
									<div class="d-flex align-items-center gap-2 mb-2">
										@if ($order->paymentMethod && $order->paymentMethod->logo_url)
											<img src="{{ $order->paymentMethod->logo_url }}" alt="{{ $order->payment_name }}" style="max-height: 28px; max-width: 65px; object-fit: contain;">
										@endif
										<h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">{{ $order->payment_name }}</h6>
									</div>
									<div class="mb-2 text-muted small">
										Biaya Layanan: 
										<strong class="text-dark">
											{{ $order->payment_fee <= 0 ? 'Bebas Biaya' : 'Rp ' . number_format($order->payment_fee, 0, ',', '.') }}
										</strong>
									</div>
									@if ($order->paymentMethod && $order->paymentMethod->instructions)
										<div class="mt-2 pt-2 border-top">
											<p class="text-secondary small mb-0" style="font-size: 12px; line-height: 1.5;">
												<i class="ri-information-line me-1 text-primary"></i> {{ $order->paymentMethod->instructions }}
											</p>
										</div>
									@endif
								</div>
							</div>

							<!-- Bank Transfer Detail (if applicable) -->
							<div class="col-md-6">
								@if ($order->payment_type === 'bank_transfer' || $order->bank_name)
									<div class="p-3 border rounded-3 h-100 bg-white border-primary" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<span class="badge bg-primary text-white fw-bold" style="font-size: 11px;">
												<i class="ri-bank-line me-1"></i> Rekening Tujuan Transfer
											</span>
											@if ($order->paymentBank && $order->paymentBank->qr_code_url)
												<button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#modalQrDetail" style="font-size: 11px; border-radius: 4px;">
													<i class="ri-qr-code-line me-1"></i> QR Code
												</button>
											@endif
										</div>
										<h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ $order->bank_name ?? 'Bank Transfer' }}</h6>
										<div class="d-flex align-items-center gap-2 mb-2">
											<span class="font-monospace fw-bold text-primary fs-5" id="detailAccNumber">{{ $order->bank_account_number }}</span>
											<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-copy-detail" data-account="{{ $order->bank_account_number }}" style="font-size: 11px; border-radius: 4px;" title="Salin">
												<i class="ri-file-copy-line"></i> Salin
											</button>
										</div>
										<p class="text-muted small mb-0">
											Atas Nama: <strong>{{ $order->bank_account_name }}</strong>
										</p>
									</div>
								@elseif ($order->payment_type === 'cod')
									<div class="p-3 border rounded-3 h-100 bg-light">
										<span class="badge bg-success-subtle text-success border fw-bold mb-2" style="font-size: 11px;">
											<i class="ri-hand-coin-line me-1"></i> Pembayaran COD
										</span>
										<p class="text-muted small mb-0" style="line-height: 1.5;">
											Mohon siapkan uang tunai sebesar <strong>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong> saat kurir mengantarkan pesanan ke alamat Anda.
										</p>
									</div>
								@else
									<div class="p-3 border rounded-3 h-100 bg-light">
										<span class="badge bg-info-subtle text-info-emphasis border fw-bold mb-2" style="font-size: 11px;">
											Status Pembayaran
										</span>
										<p class="text-muted small mb-0">
											Silakan ikuti instruksi pembayaran yang telah ditentukan untuk menyelesaikan transaksi.
										</p>
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Right Column: Bill Summary & Action Buttons -->
			<div class="col-lg-4 col-md-12">
				<div class="sticky-top" style="top: 24px; z-index: 10;">
					<div class="card border shadow-sm p-4 mb-3" style="border-radius: 12px; background: #fff;">
						<h5 class="fw-bold border-bottom pb-3 mb-3 text-dark" style="font-size: 17px;">
							<i class="ri-file-list-3-line me-1 text-primary"></i> Ringkasan Pembayaran
						</h5>

						<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
							<span class="text-muted">Subtotal Produk</span>
							<span class="fw-semibold">{{ $order->formatted_subtotal }}</span>
						</div>

						<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
							<span class="text-muted">Biaya Pengiriman</span>
							<span class="fw-semibold text-primary">{{ $order->formatted_shipping_cost }}</span>
						</div>

						@if ($order->payment_fee > 0)
							<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
								<span class="text-muted">Biaya Layanan</span>
								<span class="fw-semibold text-dark">{{ $order->formatted_payment_fee }}</span>
							</div>
						@endif

						@if ($order->discount_amount > 0)
							<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
								<span class="text-muted">Diskon ({{ $order->coupon_code }})</span>
								<span class="fw-semibold text-danger">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
							</div>
						@endif

						<div class="d-flex justify-content-between pt-3 border-top mt-2 mb-4">
							<span class="fw-bold text-dark" style="font-size: 16px;">Total Pembayaran</span>
							<span class="fw-bold text-primary" style="font-size: 20px;">{{ $order->formatted_grand_total }}</span>
						</div>

						<!-- Action Buttons -->
						<div class="d-flex flex-column gap-2">
							<button type="button" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-1" onclick="window.print()" style="border-radius: 8px; font-weight: 600;">
								<i class="ri-printer-line"></i> Cetak / Simpan Bukti
							</button>
							<a href="{{ route('ecommerce.shop') }}" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-1 text-white" style="border-radius: 8px; font-weight: 600;">
								<i class="ri-shopping-bag-line"></i> Belanja Produk Lain
							</a>
							<a href="{{ route('ecommerce.index.html') }}" class="btn btn-link text-muted small text-center mt-1">
								Kembali ke Beranda
							</a>
						</div>
					</div>

					<!-- Trust badge -->
					<div class="card border p-3 text-center bg-light" style="border-radius: 10px;">
						<p class="small text-muted mb-0">
							<i class="ri-customer-service-2-line text-primary me-1"></i> Butuh bantuan dengan pesanan Anda? Hubungi Layanan Pelanggan kami 24/7.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<!-- Modal QR Code Detail -->
@if ($order->paymentBank && $order->paymentBank->qr_code_url)
	<div class="modal fade" id="modalQrDetail" tabindex="-1" aria-labelledby="modalQrDetailLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content text-center p-3" style="border-radius: 12px;">
				<div class="modal-header border-0 pb-0">
					<h5 class="modal-title w-100 fw-bold" id="modalQrDetailLabel" style="font-size: 15px;">QR Code {{ $order->bank_name }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<img src="{{ $order->paymentBank->qr_code_url }}" alt="QR Code {{ $order->bank_name }}" class="img-fluid rounded border p-2 mb-2" style="max-height: 240px; width: 100%; object-fit: contain;">
					<p class="small text-muted mb-1">a.n. <strong>{{ $order->bank_account_name }}</strong></p>
					<p class="font-monospace fw-bold text-primary mb-0" style="font-size: 15px;">{{ $order->bank_account_number }}</p>
				</div>
			</div>
		</div>
	</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.btn-copy-detail').forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			const account = this.dataset.account;
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(account);
			} else {
				const tempInput = document.createElement('input');
				tempInput.value = account;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand('copy');
				document.body.removeChild(tempInput);
			}

			const originalHtml = this.innerHTML;
			this.innerHTML = '<i class="ri-check-line text-success"></i> Tersalin!';
			this.classList.add('btn-success', 'text-white');
			this.classList.remove('btn-outline-secondary');
			const self = this;
			setTimeout(function() {
				self.innerHTML = originalHtml;
				self.classList.remove('btn-success', 'text-white');
				self.classList.add('btn-outline-secondary');
			}, 1800);
		});
	});
});
</script>
@endsection

