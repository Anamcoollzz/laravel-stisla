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
			<div class="mn-breadcrumb m-b-30">
				<div class="row">
					<div class="col-12">
						<div class="row gi_breadcrumb_inner">
							<div class="col-md-6 col-sm-12">
								<h2 class="mn-breadcrumb-title">Checkout Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Checkout Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Checkout section -->
			<section class="mn-checkout-section p-b-15">
				<h2 class="d-none">Checkout Page</h2>

				@if (session('warning'))
					<div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 14px;">
						{{ session('warning') }}
					</div>
				@endif
				@if (session('success'))
					<div class="alert alert-success py-2 px-3 mb-3" style="font-size: 14px;">
						{{ session('success') }}
					</div>
				@endif

				<div class="row">
					<div class="mn-checkout-leftside col-lg-8 col-md-12">
						<!-- checkout content Start -->
						<div class="mn-checkout-content">
							<div class="mn-checkout-inner">
								@guest
								<div class="mn-checkout-wrap m-b-30">
									<div class="mn-checkout-block mn-check-new">
										<h3 class="mn-checkout-title">New Customer</h3>
										<div class="mn-check-block-content">
											<div class="mn-check-subtitle">Checkout Options</div>
											<form action="#">
												<span class="mn-new-option">
													<span class="m-b-15">
														<input type="radio" id="account1" name="radio-group" checked>
														<label for="account1">Register Account</label>
													</span>
													<span class="m-b-15">
														<input type="radio" id="account2" name="radio-group">
														<label for="account2">Guest Account</label>
													</span>
												</span>
											</form>
											<div class="mn-new-desc">By creating an account you will be able to shop
												faster,
												be up to date on an order's status, and keep track of the orders you
												have
												previously made.
											</div>
											<div class="mn-new-btn"><a href="{{ route('ecommerce.register') }}"
													class="mn-btn-2"><span>Continue</span></a></div>

										</div>
									</div>
									<div class="mn-checkout-block mn-check-login">
										<h3 class="mn-checkout-title">Returning Customer</h3>
										<div class="mn-check-login-form">
											<form action="{{ route('ecommerce.login.submit') }}" method="post">
												@csrf
												<span class="mn-check-login-wrap">
													<label>Email Address</label>
													<input type="email" name="email"
														placeholder="Enter your email address" required>
												</span>
												<span class="mn-check-login-wrap">
													<label>Password</label>
													<input type="password" name="password"
														placeholder="Enter your password" required>
												</span>

												<span class="mn-check-login-wrap mn-check-login-btn">
													<button class="mn-btn-2" type="submit"><span>Login</span></button>
													<a class="mn-check-login-fp" href="{{ route('password.request') }}">Forgot Password?</a>
												</span>
											</form>
										</div>
									</div>

								</div>
								@endguest
								<div class="mn-checkout-wrap m-b-30 padding-bottom-3">
									<div class="mn-checkout-block mn-check-bill">
										<div class="d-flex justify-content-between align-items-center mb-3">
											<h3 class="mn-checkout-title mb-0">Billing Details</h3>
											<a href="{{ route('ecommerce.address-list') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 13px;">
												<i class="ri-map-pin-line me-1"></i> Kelola Daftar Alamat
											</a>
										</div>
										<div class="mn-bl-block-content">
											@if (isset($defaultAddress) && $defaultAddress)
												<div class="alert alert-light border p-3 mb-3" style="border-radius: 8px;">
													<div class="d-flex justify-content-between align-items-center mb-1">
														<span class="badge bg-success" style="font-size: 11px;"><i class="ri-check-line me-1"></i> Alamat Utama ({{ $defaultAddress->label }})</span>
														<a href="{{ route('ecommerce.address-list') }}" class="text-primary small fw-semibold">Pilih Alamat Lain</a>
													</div>
													<strong>{{ $defaultAddress->recipient_name }}</strong> ({{ $defaultAddress->phone_number }})<br>
													<span class="text-muted small">{{ $defaultAddress->address_line }}{{ $defaultAddress->city ? ', '.$defaultAddress->city : '' }}{{ $defaultAddress->state ? ', '.$defaultAddress->state : '' }}{{ $defaultAddress->postal_code ? ' '.$defaultAddress->postal_code : '' }}</span>
												</div>
											@endif

											<div class="mn-check-bill-form">
												<form action="#" method="post">
													<span class="mn-bill-wrap mn-bill-half">
														<label>First Name*</label>
														<input type="text" name="firstname" value="{{ old('firstname', isset($defaultAddress) && $defaultAddress ? (explode(' ', $defaultAddress->recipient_name)[0] ?? '') : ($user->first_name ?? (explode(' ', $user->name ?? '')[0] ?? ''))) }}"
															placeholder="Enter your first name" required>
													</span>
													<span class="mn-bill-wrap mn-bill-half">
														<label>Last Name*</label>
														<input type="text" name="lastname" value="{{ old('lastname', isset($defaultAddress) && $defaultAddress ? (explode(' ', $defaultAddress->recipient_name, 2)[1] ?? '') : ($user->last_name ?? (explode(' ', $user->name ?? '', 2)[1] ?? ''))) }}"
															placeholder="Enter your last name">
													</span>
													<span class="mn-bill-wrap">
														<label>Address</label>
														<input type="text" name="address" value="{{ old('address', $defaultAddress->address_line ?? ($user->address ?? '')) }}" placeholder="Address Line 1">
													</span>
													<span class="mn-bill-wrap mn-bill-half">
														<label>City *</label>
														<span class="mn-bl-select-inner">
															<select name="gi_select_city" id="mn-select-city"
																class="mn-bill-select">
																<option selected disabled>City</option>
																<option value="1">City 1</option>
																<option value="2">City 2</option>
																<option value="3">City 3</option>
																<option value="4">City 4</option>
																<option value="5">City 5</option>
															</select>
														</span>
													</span>
													<span class="mn-bill-wrap mn-bill-half">
														<label>Post Code</label>
														<input type="text" name="postalcode" placeholder="Post Code">
													</span>
													<span class="mn-bill-wrap mn-bill-half">
														<label>Country *</label>
														<span class="mn-bl-select-inner">
															<select name="gi_select_country" id="mn-select-country"
																class="mn-bill-select">
																<option selected disabled>Country</option>
																<option value="1">Country 1</option>
																<option value="2">Country 2</option>
																<option value="3">Country 3</option>
																<option value="4">Country 4</option>
																<option value="5">Country 5</option>
															</select>
														</span>
													</span>
													<span class="mn-bill-wrap mn-bill-half">
														<label>Region State</label>
														<span class="mn-bl-select-inner">
															<select name="gi_select_state" id="mn-select-state"
																class="mn-bill-select">
																<option selected disabled>Region/State</option>
																<option value="1">Region/State 1</option>
																<option value="2">Region/State 2</option>
																<option value="3">Region/State 3</option>
																<option value="4">Region/State 4</option>
																<option value="5">Region/State 5</option>
															</select>
														</span>
													</span>
												</form>
											</div>

										</div>
									</div>

								</div>
								<span class="mn-check-order-btn m-t-30">
									<a class="mn-btn-2" href="#"><span>Place Order</span></a>
								</span>
							</div>
						</div>
						<!--cart content End -->
					</div>
					<!-- Sidebar Area Start -->
					<div class="mn-checkout-rightside col-lg-4 col-md-12 m-t-991">
						<div class="mn-sidebar-wrap">
							<!-- Sidebar Summary Block -->
							<div class="mn-sidebar-block">
								<div class="mn-sb-title">
									<h3 class="mn-sidebar-title">Summary</h3>
								</div>
								<div class="mn-sb-block-content">
									<div class="mn-checkout-summary">
										<div>
											<span class="text-left">Sub-Total</span>
											<span class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
										</div>
										<div>
											<span class="text-left">Delivery Charges</span>
											<span class="text-right">Gratis</span>
										</div>
										<div>
											<span class="text-left">Coupon Discount</span>
											<span class="text-right"><a class="mn-checkout-coupan">Apply
													Coupon</a></span>
										</div>
										<div class="mn-checkout-coupan-content">
											<form class="mn-checkout-coupan-form" name="mn-checkout-coupan-form"
												method="post" action="#">
												<input class="mn-coupan" type="text" required=""
													placeholder="Enter Your Coupon Code" name="mn-coupan" value="">
												<button class="mn-coupan-btn mn-btn-2" type="submit" name="subscribe"
													value=""><span>Apply</span></button>
											</form>
										</div>
										<div class="mn-checkout-summary-total">
											<span class="text-left">Total Amount</span>
											<span class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
										</div>
									</div>
									<div class="mn-checkout-pro">
										@forelse ($cartItems as $item)
											<div class="col-sm-12 mb-3">
												<div class="mn-product-inner">
													<div class="mn-pro-image-outer">
														<div class="mn-pro-image">
															<a href="{{ route('ecommerce.products.detail', $item['slug']) }}" class="image">
																<img class="main-image" src="{{ $item['thumbnail'] }}"
																	alt="{{ $item['title'] }}">
															</a>
														</div>
													</div>
													<div class="mn-pro-content">
														<h5 class="mn-pro-title"><a href="{{ route('ecommerce.products.detail', $item['slug']) }}">{{ $item['title'] }}</a></h5>
														<span class="mn-price">
															<span class="new-price">Rp {{ number_format($item['price'], 0, ',', '.') }} &times; {{ $item['quantity'] }}</span>
														</span>
														<span class="text-muted small d-block">
															Total: Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
														</span>
													</div>
												</div>
											</div>
										@empty
											<div class="col-sm-12 mb-0">
												<p class="text-muted">Tidak ada produk di keranjang belanja.</p>
											</div>
										@endforelse
									</div>
								</div>
							</div>
							<!-- Sidebar Summary Block -->
						</div>
						<div class="mn-sidebar-wrap mn-checkout-del-wrap">
							<!-- Sidebar Summary Block -->
							<div class="mn-sidebar-block">
								<div class="mn-sb-title">
									<h3 class="mn-sidebar-title">Delivery Method</h3>
								</div>
								<div class="mn-sb-block-content">
									<div class="mn-checkout-del">
										<div class="mn-del-desc">Please select the preferred shipping method to use
											on this
											order.</div>
										<form action="#">
											<span class="mn-del-option">
												<span>
													<span class="mn-del-opt-head">Free Shipping</span>
													<input type="radio" id="del1" name="radio-group" checked>
													<label for="del1">Rate - $0 .00</label>
												</span>
												<span>
													<span class="mn-del-opt-head">Flat Rate</span>
													<input type="radio" id="del2" name="radio-group">
													<label for="del2">Rate - $5.00</label>
												</span>
											</span>
										</form>
									</div>
								</div>
							</div>
							<!-- Sidebar Summary Block -->
						</div>
						<div class="mn-sidebar-wrap mn-checkout-del-wrap">
							<!-- Sidebar Payment Block -->
							<div class="mn-sidebar-block">
								<div class="mn-sb-title">
									<h3 class="mn-sidebar-title">Payment Method</h3>
								</div>
								<div class="mn-sb-block-content">
									<div class="mn-checkout-pay">
										<div class="mn-pay-desc">Please select the preferred payment method to use
											on this
											order.</div>
										<form action="#">
											<span class="mn-pay-option m-b-15">
												<span>
													<input type="radio" id="pay1" name="radio-group" checked>
													<label for="pay1">Cash On Delivery</label>
												</span>
											</span>
											<span class="mn-pay-commemt">
												<span class="mn-pay-opt-head">Add extra note</span>
												<textarea name="your-commemt" placeholder="Comments"></textarea>
											</span>
											<span class="mn-pay-agree">
												<input type="checkbox" value="">
												I have agree with <a href="#">Terms & Conditions.</a>
												<span class="checked"></span>
											</span>
										</form>
									</div>
								</div>
							</div>
							<!-- Sidebar Payment Block -->
						</div>
						<div class="mn-sidebar-wrap mn-checkout-del-wrap">
							<!-- Sidebar Payment Block -->
							<div class="mn-sidebar-block">
								<div class="mn-sb-title">
									<h3 class="mn-sidebar-title">Payment Method</h3>
								</div>
								<div class="mn-sb-block-content">
									<div class="mn-check-pay-img-inner">
										<div class="mn-check-pay-img">
											<img src="{{ asset('mantu/assets/img/footer/payment.png') }}" alt="payment">
										</div>
									</div>
								</div>
							</div>
							<!-- Sidebar Payment Block -->
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
