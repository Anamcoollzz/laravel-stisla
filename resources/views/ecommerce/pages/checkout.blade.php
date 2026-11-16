@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Checkout - Ecommerce')

@section('vendor-css')
  <link href="{{ asset('mantu/assets/css/vendor/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/animate.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/slick.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/nouislider.css') }}" rel="stylesheet">
@endsection

@section('vendor-js')
  <script src="{{ asset('mantu/assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/slick.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/countdownTimer.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/infiniteslidev2.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/nouislider.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/smoothscroll.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/jquery.zoom.min.js') }}"></script>
@endsection

@section('content')
<!-- Main Content -->
<div class="mn-main-content">
	<!-- Breadcrumb -->
	<div class="mn-breadcrumb m-b-30">
		<div class="row">
			<div class="col-12">
				<div class="row gi_breadcrumb_inner">
					<div class="col-md-6 col-sm-12">
						<h2 class="mn-breadcrumb-title">Checkout</h2>
					</div>
					<div class="col-md-6 col-sm-12">
						<ul class="mn-breadcrumb-list">
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.cart') }}">Keranjang</a></li>
							<li class="mn-breadcrumb-item active">Checkout</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Checkout section -->
	<section class="mn-checkout-section p-b-40">
		<h2 class="d-none">Checkout</h2>

		@if (session('warning'))
			<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
				<strong><i class="ri-alert-line me-1"></i> {{ session('warning') }}</strong>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif
		@if (session('success'))
			<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
				<strong><i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}</strong>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<form action="#" method="POST" id="checkoutForm">
			@csrf
			<div class="row g-4">
				<!-- Left Column: Checkout Details -->
				<div class="mn-checkout-leftside col-lg-8 col-md-12">
					<div class="mn-checkout-content">
						<div class="mn-checkout-inner">
							@guest
							<div class="mn-checkout-wrap m-b-30">
								<div class="mn-checkout-block mn-check-new">
									<h3 class="mn-checkout-title">Pelanggan Baru</h3>
									<div class="mn-check-block-content">
										<p class="text-muted small">Buat akun untuk mempermudah melacak pesanan dan belanja lebih cepat.</p>
										<div class="mn-new-btn">
											<a href="{{ route('ecommerce.register') }}" class="mn-btn-2"><span>Daftar Akun</span></a>
										</div>
									</div>
								</div>
								<div class="mn-checkout-block mn-check-login mt-3">
									<h3 class="mn-checkout-title">Sudah Punya Akun?</h3>
									<div class="mn-check-login-form">
										<div class="mn-check-login-wrap mn-check-login-btn">
											<a href="{{ route('ecommerce.login') }}" class="mn-btn-2"><span>Login Sekarang</span></a>
										</div>
									</div>
								</div>
							</div>
							@endguest

							<!-- 1. Shipping Address Section -->
							<div class="mn-checkout-wrap m-b-30">
								<div class="mn-checkout-block mn-check-bill">
									<div class="d-flex justify-content-between align-items-center mb-3">
										<h3 class="mn-checkout-title mb-0" style="font-size: 18px;">
											<i class="ri-map-pin-user-line me-1 text-primary"></i> 1. Alamat Pengiriman
										</h3>
										<a href="{{ route('ecommerce.address-list') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 13px;">
											<i class="ri-map-pin-line me-1"></i> Kelola Alamat
										</a>
									</div>
									<div class="mn-bl-block-content">
										@if (isset($defaultAddress) && $defaultAddress)
											<div class="card border p-3" style="border-radius: 10px; background-color: #f8f9fa;">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="d-flex align-items-center gap-2">
														<span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 11px;">{{ $defaultAddress->label }}</span>
														<span class="badge bg-success px-2 py-1" style="font-size: 11px;">
															<i class="ri-check-line me-1"></i> Alamat Utama
														</span>
													</div>
													<a href="{{ route('ecommerce.address-list') }}" class="text-primary small fw-semibold">
														<i class="ri-arrow-left-right-line me-1"></i> Ganti Alamat Lain
													</a>
												</div>
												<h5 class="fw-bold mb-1" style="font-size: 15px;">{{ $defaultAddress->recipient_name }}</h5>
												<p class="text-secondary small mb-2"><i class="ri-phone-line me-1"></i> {{ $defaultAddress->phone_number }}</p>
												<p class="text-muted mb-1" style="font-size: 14px; line-height: 1.5;">
													{{ $defaultAddress->address_line }}
												</p>
												@if ($defaultAddress->city || $defaultAddress->state || $defaultAddress->postal_code)
													<p class="text-muted small mb-1">
														<i class="ri-map-pin-2-line me-1"></i>
														{{ collect([$defaultAddress->city, $defaultAddress->state, $defaultAddress->postal_code])->filter()->join(', ') }}
													</p>
												@endif
												@if ($defaultAddress->notes)
													<p class="text-secondary small mb-0 fst-italic">
														<strong>Catatan:</strong> {{ $defaultAddress->notes }}
													</p>
												@endif
											</div>
										@elseif (isset($addresses) && $addresses->isNotEmpty())
											<div class="alert alert-warning p-3 mb-0" style="border-radius: 8px;">
												<p class="mb-2">Anda belum memilih alamat pengiriman utama.</p>
												<a href="{{ route('ecommerce.address-list') }}" class="btn btn-sm btn-primary">Pilih Alamat Utama</a>
											</div>
										@else
											<div class="alert alert-warning p-3 mb-0" style="border-radius: 8px;">
												<p class="mb-2">Anda belum memiliki alamat pengiriman tersimpan.</p>
												<a href="{{ route('ecommerce.address-list') }}" class="btn btn-sm btn-primary">
													<i class="ri-add-line me-1"></i> Tambah Alamat Sekarang
												</a>
											</div>
										@endif
									</div>
								</div>
							</div>

							<!-- 2. Delivery Method Section -->
							<div class="mn-checkout-wrap m-b-30">
								<div class="mn-checkout-block">
									<h3 class="mn-checkout-title mb-3" style="font-size: 18px;">
										<i class="ri-truck-line me-1 text-primary"></i> 2. Metode Pengiriman
									</h3>
									<div class="mn-check-block-content">
										<p class="text-muted small mb-3">Pilih opsi pengiriman yang diinginkan untuk pesanan Anda:</p>
										<div class="row g-3">
											<div class="col-md-6">
												<label class="card p-3 border h-100 d-flex flex-row align-items-center gap-3" for="del1" style="cursor: pointer; border-radius: 8px; transition: all 0.2s;">
													<input type="radio" id="del1" name="delivery_method" value="free" checked class="form-check-input mt-0" style="width: 20px; height: 20px;">
													<div>
														<strong class="d-block text-dark" style="font-size: 15px;">Reguler / Standard Shipping</strong>
														<span class="text-success small fw-semibold">Gratis Ongkir</span>
														<div class="text-muted small">Estimasi tiba 3 - 5 hari kerja</div>
													</div>
												</label>
											</div>
											<div class="col-md-6">
												<label class="card p-3 border h-100 d-flex flex-row align-items-center gap-3" for="del2" style="cursor: pointer; border-radius: 8px; transition: all 0.2s;">
													<input type="radio" id="del2" name="delivery_method" value="express" class="form-check-input mt-0" style="width: 20px; height: 20px;">
													<div>
														<strong class="d-block text-dark" style="font-size: 15px;">Express / Kilat</strong>
														<span class="text-dark small fw-semibold">Rp 25.000</span>
														<div class="text-muted small">Estimasi tiba 1 - 2 hari kerja</div>
													</div>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- 3. Payment Method Section -->
							<div class="mn-checkout-wrap m-b-30">
								<div class="mn-checkout-block">
									<h3 class="mn-checkout-title mb-3" style="font-size: 18px;">
										<i class="ri-bank-card-line me-1 text-primary"></i> 3. Metode Pembayaran
									</h3>
									<div class="mn-check-block-content">
										<p class="text-muted small mb-3">Pilih metode pembayaran yang paling nyaman untuk Anda:</p>
										<div class="row g-3 mb-4">
											<div class="col-md-6">
												<label class="card p-3 border h-100 d-flex flex-row align-items-center gap-3" for="pay1" style="cursor: pointer; border-radius: 8px; transition: all 0.2s;">
													<input type="radio" id="pay1" name="payment_method" value="cod" checked class="form-check-input mt-0" style="width: 20px; height: 20px;">
													<div>
														<strong class="d-block text-dark" style="font-size: 15px;">Cash On Delivery (COD)</strong>
														<span class="text-muted small">Bayar langsung secara tunai saat kurir tiba</span>
													</div>
												</label>
											</div>
											<div class="col-md-6">
												<label class="card p-3 border h-100 d-flex flex-row align-items-center gap-3" for="pay2" style="cursor: pointer; border-radius: 8px; transition: all 0.2s;">
													<input type="radio" id="pay2" name="payment_method" value="transfer" class="form-check-input mt-0" style="width: 20px; height: 20px;">
													<div>
														<strong class="d-block text-dark" style="font-size: 15px;">Transfer Bank / VA</strong>
														<span class="text-muted small">BCA, Mandiri, BNI, BRI, Permata</span>
													</div>
												</label>
											</div>
										</div>

										<div class="mb-2">
											<label class="form-label fw-bold small text-muted">Catatan untuk Penjual / Kurir (Opsional)</label>
											<textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Titipkan di pos satpam atau hubungi sebelum antar..." style="border-radius: 8px; font-size: 14px;"></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Right Column: Order Summary & Place Order Button -->
				<div class="mn-checkout-rightside col-lg-4 col-md-12">
					<div class="mn-sidebar-wrap sticky-top" style="top: 24px; z-index: 10;">
						<div class="card border shadow-sm p-4" style="border-radius: 12px; background: #fff;">
							<div class="border-bottom pb-2 mb-3">
								<h4 class="fw-bold m-0" style="font-size: 18px;">Ringkasan Pesanan</h4>
							</div>

							<!-- Products List in Cart -->
							<div class="mb-3" style="max-height: 270px; overflow-y: auto; padding-right: 4px;">
								@forelse ($cartItems as $item)
									<div class="d-flex align-items-center gap-3 py-2 border-bottom">
										<img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" style="width: 52px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
										<div class="flex-grow-1" style="min-width: 0;">
											<h6 class="mb-0 text-truncate fw-bold" style="font-size: 13px;">
												<a href="{{ route('ecommerce.products.detail', $item['slug']) }}" class="text-dark">{{ $item['title'] }}</a>
											</h6>
											<small class="text-muted">{{ $item['quantity'] }} &times; Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
										</div>
										<div class="text-end fw-bold text-nowrap" style="font-size: 13px;">
											Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
										</div>
									</div>
								@empty
									<p class="text-muted small py-2 mb-0">Tidak ada produk di keranjang belanja.</p>
								@endforelse
							</div>

							<!-- Summary Breakdown -->
							<div class="py-2 border-bottom mb-3">
								<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
									<span class="text-muted">Subtotal Produk</span>
									<span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
								</div>
								<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
									<span class="text-muted">Biaya Pengiriman</span>
									<span class="text-success fw-semibold">Gratis</span>
								</div>
								<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
									<span class="text-muted">Kupon Diskon</span>
									<a href="javascript:void(0)" class="text-primary small" onclick="$('#couponCollapse').slideToggle();">Gunakan Kupon</a>
								</div>
								<div id="couponCollapse" style="display: none;" class="mb-2">
									<div class="input-group input-group-sm">
										<input type="text" class="form-control" placeholder="Kode Kupon" name="coupon_code">
										<button class="btn btn-outline-dark" type="button">Terapkan</button>
									</div>
								</div>
								<div class="d-flex justify-content-between pt-2 border-top mt-2">
									<span class="fw-bold text-dark" style="font-size: 15px;">Total Pembayaran</span>
									<span class="fw-bold text-primary" style="font-size: 18px;">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
								</div>
							</div>

							<!-- Terms Agreement -->
							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" id="termsAgree" required checked>
								<label class="form-check-label text-muted small" for="termsAgree" style="font-size: 12px; line-height: 1.4;">
									Saya telah membaca dan menyetujui <a href="{{ route('ecommerce.terms.html') }}" target="_blank" class="text-primary">Syarat & Ketentuan</a> yang berlaku.
								</label>
							</div>

							<!-- PLACE ORDER BUTTON (PALING BAWAH KANAN) -->
							<div class="mn-checkout-order-btn">
								<button type="button" class="mn-btn-1 btn w-100 py-3 text-center shadow" style="border-radius: 8px; font-size: 16px; font-weight: bold;" onclick="alert('Terima kasih! Pesanan Anda berhasil diproses.');">
									<span><i class="ri-shopping-cart-2-line me-1"></i> Place Order</span>
								</button>
							</div>

							<!-- Trust & Security Badges -->
							<div class="text-center pt-3 mt-3 border-top">
								<small class="text-muted d-block mb-2" style="font-size: 11px;">
									<i class="ri-shield-check-line text-success me-1"></i> Pembayaran Aman & Terenkripsi
								</small>
								<img src="{{ asset('mantu/assets/img/footer/payment.png') }}" alt="Pembayaran Aman" style="max-height: 22px; filter: grayscale(15%);">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
@endsection
