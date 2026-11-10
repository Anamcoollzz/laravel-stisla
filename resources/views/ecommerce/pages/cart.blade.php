@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Cart - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Cart Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Cart Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Cart section -->
			<section class="mn-cart-section p-b-15">
				<h2 class="d-none">Cart Page</h2>
				<div class="row">
					<div class="mn-cart-leftside col-lg-8 col-md-12">
						<!-- cart content Start -->
						<div class="mn-cart-content">
							<div class="mn-cart-inner cart_list">
								<div class="row">
									<form action="#">
										<div class="table-content cart-table-content">
											<table>
												<thead>
													<tr>
														<th>Product</th>
														<th>Price</th>
														<th style="text-align: center;">Quantity</th>
														<th>Total</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													@forelse ($cartItems as $item)
													<tr class="mn-cart-product" data-product-id="{{ $item['id'] }}">
														<td data-label="Product" class="mn-cart-pro-name">
															<a href="{{ route('ecommerce.products.detail', $item['slug']) }}">
																<img class="mn-cart-pro-img"
																	src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}">{{ $item['title'] }}
															</a>
														</td>
														<td data-label="Price" class="mn-cart-pro-price">
															<span class="amount">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
														</td>
														<td data-label="Quantity" class="mn-cart-pro-qty"
															style="text-align: center;">
															<div class="cart-qty-plus-minus cart-item-qty-control" data-product-id="{{ $item['id'] }}">
																<input class="cart-plus-minus cart-qty-input" type="text"
																	name="cartqtybutton" value="{{ $item['quantity'] }}" data-product-id="{{ $item['id'] }}">
															</div>
														</td>
														<td data-label="Total" class="mn-cart-pro-subtotal">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
														</td>
														<td data-label="Remove" class="mn-cart-pro-remove">
															<a href="javascript:void(0)" class="cart-remove-item" data-product-id="{{ $item['id'] }}"><i class="ri-delete-bin-line"></i></a>
														</td>
													</tr>
													@empty
													<tr>
														<td colspan="5" class="text-center p-4">
															<p class="mn-cart-page-msg m-0">Your Cart is empty!</p>
														</td>
													</tr>
													@endforelse
												</tbody>
											</table>
										</div>
											<div class="col-lg-12">
												<div class="mn-cart-update-bottom">
													<a href="{{ route('ecommerce.shop') }}">Continue Shopping</a>
													<button type="button" class="mn-btn-2" onclick="window.location.href='{{ route('ecommerce.checkout.html') }}'"><span>Check Out</span></button>
												</div>
											</div>
									</form>
								</div>
							</div>
						</div>
						<!--cart content End -->
					</div>
					<!-- Sidebar Area Start -->
					<div class="mn-cart-rightside col-lg-4 col-md-12 m-t-991">
						<div class="mn-sidebar-wrap">
							<!-- Sidebar Summary Block -->
							<div class="mn-sidebar-block">
								<div class="mn-sb-title">
									<h3 class="mn-sidebar-title">Summary</h3>
								</div>
								<div class="mn-sb-block-content">
									<div class="mn-cart-form">
										<p>Enter your destination to get a shipping estimate</p>
										<form action="#" method="post">
											<span class="mn-cart-wrap">
												<label>Country *</label>
												<span class="mn-cart-select-inner">
													<select name="gi_cart_country" id="mn-cart-select-country"
														class="mn-cart-select">
														<option selected="" disabled="">United States</option>
														<option value="1">Country 1</option>
														<option value="2">Country 2</option>
														<option value="3">Country 3</option>
														<option value="4">Country 4</option>
														<option value="5">Country 5</option>
													</select>
												</span>
											</span>
											<span class="mn-cart-wrap">
												<label>State/Province</label>
												<span class="mn-cart-select-inner">
													<select name="gi_cart_state" id="mn-cart-select-state"
														class="mn-cart-select">
														<option selected="" disabled="">Please Select a region,
															state
														</option>
														<option value="1">Region/State 1</option>
														<option value="2">Region/State 2</option>
														<option value="3">Region/State 3</option>
														<option value="4">Region/State 4</option>
														<option value="5">Region/State 5</option>
													</select>
												</span>
											</span>
											<span class="mn-cart-wrap">
												<label>Zip/Postal Code</label>
												<input type="text" name="postalcode" placeholder="Zip/Postal Code">
											</span>
										</form>
									</div>
								</div>

								<div class="mn-sb-block-content">
									<div class="mn-cart-summary-bottom">
										<div class="mn-cart-summary">
											<div>
												<span class="text-left">Sub-Total</span>
												<span class="text-right cart-subtotal-val">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
											</div>
											<div>
												<span class="text-left">Delivery Charges</span>
												<span class="text-right">Free</span>
											</div>
											<div>
												<span class="text-left">Coupan Discount</span>
												<span class="text-right"><a class="mn-cart-coupan">Apply
														Coupan</a></span>
											</div>
											<div class="mn-cart-coupan-content">
												<form class="mn-cart-coupan-form" name="mn-cart-coupan-form"
													method="post" action="#">
													<input class="mn-coupan" type="text" required=""
														placeholder="Enter Your Coupan Code" name="mn-coupan" value="">
													<button class="mn-btn-2" type="submit" name="subscribe"
														value=""><span>Apply</span></button>
												</form>
											</div>
											<div class="mn-cart-summary-total">
												<span class="text-left">Total Amount</span>
												<span class="text-right cart-total-val">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
