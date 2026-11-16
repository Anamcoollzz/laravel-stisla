@extends('ecommerce.layouts.app-ecommerce')

@section('title', __('Checkout - Ecommerce'))

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
						<h2 class="mn-breadcrumb-title">{{ __('Checkout') }}</h2>
					</div>
					<div class="col-md-6 col-sm-12">
						<ul class="mn-breadcrumb-list">
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">{{ __('Home') }}</a></li>
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.cart') }}">{{ __('Cart') }}</a></li>
							<li class="mn-breadcrumb-item active">{{ __('Checkout') }}</li>
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

		<form action="{{ route('ecommerce.checkout.store') }}" method="POST" id="checkoutForm">
			@csrf
			<input type="hidden" name="address_id" value="{{ $defaultAddress->id ?? '' }}" id="selectedAddressId">
			<div class="row g-4">
				<!-- Left Column: Checkout Details -->
				<div class="mn-checkout-leftside col-lg-8 col-md-12">
					<div class="mn-checkout-content">
						<div class="mn-checkout-inner">
							@guest
							<div class="mn-checkout-wrap m-b-30">
								<div class="mn-checkout-block mn-check-new">
									<h3 class="mn-checkout-title">{{ __('New Customer') }}</h3>
									<div class="mn-check-block-content">
										<p class="text-muted small">{{ __('Create an account to easily track orders and shop faster.') }}</p>
										<div class="mn-new-btn">
											<a href="{{ route('ecommerce.register') }}" class="mn-btn-2"><span>{{ __('Register Account') }}</span></a>
										</div>
									</div>
								</div>
								<div class="mn-checkout-block mn-check-login mt-3">
									<h3 class="mn-checkout-title">{{ __('Already Have an Account?') }}</h3>
									<div class="mn-check-login-form">
										<div class="mn-check-login-wrap mn-check-login-btn">
											<a href="{{ route('ecommerce.login') }}" class="mn-btn-2"><span>{{ __('Login Now') }}</span></a>
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
											<i class="ri-map-pin-user-line me-1 text-primary"></i> 1. {{ __('Shipping Address') }}
										</h3>
										<a href="{{ route('ecommerce.address-list') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 13px;">
											<i class="ri-map-pin-line me-1"></i> {{ __('Manage Addresses') }}
										</a>
									</div>
									<div class="mn-bl-block-content">
										@if (isset($defaultAddress) && $defaultAddress)
											<div class="card border p-3" style="border-radius: 10px; background-color: #f8f9fa;">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="d-flex align-items-center gap-2">
														<span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 11px;">{{ $defaultAddress->label }}</span>
														<span class="badge bg-success px-2 py-1" style="font-size: 11px;">
															<i class="ri-check-line me-1"></i> {{ __('Primary Address') }}
														</span>
													</div>
													<a href="{{ route('ecommerce.address-list') }}" class="text-primary small fw-semibold">
														<i class="ri-arrow-left-right-line me-1"></i> {{ __('Change Another Address') }}
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
														<strong>{{ __('Notes:') }}</strong> {{ $defaultAddress->notes }}
													</p>
												@endif
											</div>
										@elseif (isset($addresses) && $addresses->isNotEmpty())
											<div class="alert alert-warning p-3 mb-0" style="border-radius: 8px;">
												<p class="mb-2">{{ __('You have not selected a primary shipping address yet.') }}</p>
												<a href="{{ route('ecommerce.address-list') }}" class="btn btn-sm btn-primary">{{ __('Select Primary Address') }}</a>
											</div>
										@else
											<div class="alert alert-warning p-3 mb-0" style="border-radius: 8px;">
												<p class="mb-2">{{ __('You do not have any saved shipping addresses yet.') }}</p>
												<a href="{{ route('ecommerce.address-list') }}" class="btn btn-sm btn-primary">
													<i class="ri-add-line me-1"></i> {{ __('Add Address Now') }}
												</a>
											</div>
										@endif
									</div>
								</div>
							</div>

							<!-- 2. Delivery Method Section -->
							<div class="mn-checkout-wrap m-b-30">
								<div class="mn-checkout-block">
									<div class="d-flex justify-content-between align-items-center mb-3">
										<h3 class="mn-checkout-title mb-0" style="font-size: 18px;">
											<i class="ri-truck-line me-1 text-primary"></i> 2. {{ __('Shipping Method') }}
										</h3>
										<span class="text-muted small">{{ __('Select courier / expedition') }}</span>
									</div>
									<div class="mn-check-block-content">
										<p class="text-muted small mb-3">{{ __('Choose your preferred shipping option for your order:') }}</p>
										<div class="row g-3">
											@forelse ($shippingMethods as $shipMethod)
												<div class="col-md-6">
													<label class="card p-3 border h-100 d-flex flex-column justify-content-between shipping-method-card {{ $loop->first ? 'border-primary shadow-sm bg-light' : '' }}" for="shipping_{{ $shipMethod->id }}" style="cursor: pointer; border-radius: 10px; transition: all 0.2s;">
														<div class="d-flex align-items-start gap-3">
															<input type="radio" id="shipping_{{ $shipMethod->id }}" name="shipping_method_id" value="{{ $shipMethod->id }}" data-cost="{{ $shipMethod->cost }}" data-name="{{ $shipMethod->name }}" {{ $loop->first ? 'checked' : '' }} class="form-check-input mt-1 shipping-radio" style="width: 20px; height: 20px; cursor: pointer;">
															<div class="flex-grow-1">
																<div class="d-flex align-items-center justify-content-between gap-2 mb-1">
																	<strong class="text-dark" style="font-size: 14px;">{{ $shipMethod->name }}</strong>
																	@if ($shipMethod->logo_url)
																		<img src="{{ $shipMethod->logo_url }}" alt="{{ $shipMethod->name }}" style="max-height: 24px; max-width: 55px; object-fit: contain;">
																	@endif
																</div>
																<div class="mb-1">
																	@if ($shipMethod->cost <= 0)
																		<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 12px;">{{ __('Free Shipping') }}</span>
																	@else
																		<span class="text-primary fw-bold" style="font-size: 14px;">Rp {{ number_format($shipMethod->cost, 0, ',', '.') }}</span>
																	@endif
																</div>
																@if ($shipMethod->estimated_delivery)
																	<div class="text-muted small">
																		<i class="ri-time-line me-1"></i>{{ __('Estimate') }}: {{ $shipMethod->estimated_delivery }}
																	</div>
																@endif
																@if ($shipMethod->description)
																	<div class="text-secondary small mt-1" style="font-size: 12px; line-height: 1.4;">{{ $shipMethod->description }}</div>
																@endif
															</div>
														</div>
													</label>
												</div>
											@empty
												<div class="col-12">
													<div class="alert alert-warning py-3 mb-0" style="border-radius: 8px;">
														<i class="ri-information-line me-1"></i> {{ __('No active shipping methods available at the moment.') }}
													</div>
												</div>
											@endforelse
										</div>
									</div>
								</div>
							</div>

							<!-- 3. Payment Method Section -->
							<div class="mn-checkout-wrap m-b-30">
								<div class="mn-checkout-block">
									<div class="d-flex justify-content-between align-items-center mb-3">
										<h3 class="mn-checkout-title mb-0" style="font-size: 18px;">
											<i class="ri-bank-card-line me-1 text-primary"></i> 3. {{ __('Payment Method') }}
										</h3>
										<span class="text-muted small">{{ __('Select payment option') }}</span>
									</div>
									<div class="mn-check-block-content">
										<p class="text-muted small mb-3">{{ __('Choose the most convenient payment method for you:') }}</p>
										<div class="row g-3 mb-3">
											@forelse ($paymentMethods as $payMethod)
												<div class="col-md-6">
													<label class="card p-3 border h-100 d-flex flex-column justify-content-between payment-method-card {{ $loop->first ? 'border-primary shadow-sm bg-light' : '' }}" for="payment_{{ $payMethod->id }}" style="cursor: pointer; border-radius: 10px; transition: all 0.2s;">
														<div class="d-flex align-items-start gap-3">
															<input type="radio" id="payment_{{ $payMethod->id }}" name="payment_method_id" value="{{ $payMethod->id }}" data-fee="{{ $payMethod->fee }}" data-type="{{ $payMethod->type }}" data-name="{{ $payMethod->name }}" {{ $loop->first ? 'checked' : '' }} class="form-check-input mt-1 payment-radio" style="width: 20px; height: 20px; cursor: pointer;">
															<div class="flex-grow-1">
																<div class="d-flex align-items-center justify-content-between gap-2 mb-1">
																	<strong class="text-dark" style="font-size: 14px;">{{ $payMethod->name }}</strong>
																	@if ($payMethod->logo_url)
																		<img src="{{ $payMethod->logo_url }}" alt="{{ $payMethod->name }}" style="max-height: 24px; max-width: 55px; object-fit: contain;">
																	@endif
																</div>
																<div class="mb-1">
																	@if ($payMethod->fee > 0)
																		<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 11px;">
																			+{{ __('Fee') }} Rp {{ number_format($payMethod->fee, 0, ',', '.') }}
																		</span>
																	@else
																		<span class="badge bg-light text-muted border" style="font-size: 11px;">{{ __('No Fee') }}</span>
																	@endif
																</div>
																@if ($payMethod->description)
																	<div class="text-muted small" style="font-size: 12px; line-height: 1.4;">{{ $payMethod->description }}</div>
																@endif
															</div>
														</div>

														@if ($payMethod->instructions)
															<div class="payment-instruction-box text-secondary small mt-2 pt-2 border-top" id="instruction_{{ $payMethod->id }}" style="{{ $loop->first ? '' : 'display: none;' }} font-size: 12px;">
																<i class="ri-information-line me-1 text-primary"></i> {{ $payMethod->instructions }}
															</div>
														@endif
													</label>
												</div>
											@empty
												<div class="col-12">
													<div class="alert alert-warning py-3 mb-0" style="border-radius: 8px;">
														<i class="ri-information-line me-1"></i> {{ __('No active payment methods available at the moment.') }}
													</div>
												</div>
											@endforelse
										</div>

										<!-- Bank Accounts Detail Section (Shown when bank_transfer is selected) -->
										<div id="bankAccountsSection" class="p-3 border rounded-3 mb-3 bg-light" style="display: none;">
											<div class="d-flex align-items-center justify-content-between mb-2">
												<h5 class="fw-bold mb-0 text-dark" style="font-size: 14px;">
													<i class="ri-bank-line me-1 text-primary"></i> {{ __('Transfer Destination Account') }}
												</h5>
												<span class="badge bg-primary text-white" style="font-size: 11px;">{{ __('Manual Transfer') }}</span>
											</div>
											<p class="text-muted small mb-3" style="font-size: 12px;">{{ __('Please select one of the following destination bank accounts:') }}</p>
											<div class="row g-2">
												@forelse ($paymentBanks as $bank)
													<div class="col-12">
														<label class="card p-3 border bg-white d-flex flex-row align-items-center justify-content-between gap-3 bank-option-card {{ $loop->first ? 'border-primary' : '' }}" for="bank_{{ $bank->id }}" style="cursor: pointer; border-radius: 8px; transition: all 0.2s;">
															<div class="d-flex align-items-center gap-3">
																<input type="radio" name="payment_bank_id" id="bank_{{ $bank->id }}" value="{{ $bank->id }}" {{ $loop->first ? 'checked' : '' }} class="form-check-input bank-radio" style="width: 18px; height: 18px; cursor: pointer;">
																@if ($bank->logo_url)
																	<img src="{{ $bank->logo_url }}" alt="{{ $bank->bank_name }}" style="max-height: 26px; max-width: 50px; object-fit: contain;">
																@else
																	<div class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 12px;">{{ $bank->bank_code ?? 'BANK' }}</div>
																@endif
																<div>
																	<div class="fw-bold text-dark" style="font-size: 14px;">{{ $bank->bank_name }}</div>
																	<div class="d-flex align-items-center gap-2 mt-1">
																		<span class="font-monospace fw-bold text-primary" style="font-size: 14px;" id="accNum_{{ $bank->id }}">{{ $bank->account_number }}</span>
																		<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-copy-acc" data-account="{{ $bank->account_number }}" style="font-size: 11px; border-radius: 4px;" title="{{ __('Copy Account Number') }}">
																			<i class="ri-file-copy-line"></i> {{ __('Copy') }}
																		</button>
																	</div>
																	<div class="text-muted small" style="font-size: 12px;">
																		{{ __('a.n.') }} <strong>{{ $bank->account_name }}</strong> {{ $bank->branch ? '('.$bank->branch.')' : '' }}
																	</div>
																</div>
															</div>
															@if ($bank->qr_code_url)
																<div>
																	<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $bank->id }}" style="font-size: 11px; border-radius: 6px;">
																		<i class="ri-qr-code-line me-1"></i> QR Code
																	</button>
																</div>
															@endif
														</label>
													</div>
												@empty
													<div class="col-12">
														<p class="text-muted small mb-0">{{ __('No active bank accounts available.') }}</p>
													</div>
												@endforelse
											</div>
										</div>

										<div class="mb-2">
											<label class="form-label fw-bold small text-muted">{{ __('Notes for Seller / Courier (Optional)') }}</label>
											<textarea name="notes" class="form-control" rows="2" placeholder="{{ __('e.g. Leave at security post or call before delivery...') }}" style="border-radius: 8px; font-size: 14px;"></textarea>
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
								<h4 class="fw-bold m-0" style="font-size: 18px;">{{ __('Order Summary') }}</h4>
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
									<p class="text-muted small py-2 mb-0">{{ __('No products in your shopping cart.') }}</p>
								@endforelse
							</div>

							<!-- Summary Breakdown -->
							<div class="py-2 border-bottom mb-3">
								<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
									<span class="text-muted">{{ __('Product Subtotal') }}</span>
									<span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
								</div>
								<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
									<span class="text-muted">{{ __('Shipping Cost') }}</span>
									<span class="fw-semibold text-primary" id="summaryShippingCost">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between mb-2" id="summaryFeeRow" style="font-size: 14px; display: none;">
									<span class="text-muted">{{ __('Service Fee') }}</span>
									<span class="fw-semibold text-dark" id="summaryPaymentFee">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
									<span class="text-muted">{{ __('Discount Coupon') }}</span>
									<a href="javascript:void(0)" class="text-primary small" onclick="$('#couponCollapse').slideToggle();">{{ __('Use Coupon') }}</a>
								</div>
								<div id="couponCollapse" style="display: none;" class="mb-2">
									<div class="input-group input-group-sm">
										<input type="text" class="form-control" placeholder="{{ __('Coupon Code') }}" name="coupon_code">
										<button class="btn btn-outline-dark" type="button">{{ __('Apply') }}</button>
									</div>
								</div>
								<div class="d-flex justify-content-between pt-2 border-top mt-2">
									<span class="fw-bold text-dark" style="font-size: 15px;">{{ __('Total Payment') }}</span>
									<span class="fw-bold text-primary" style="font-size: 18px;" id="summaryTotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
								</div>
							</div>

							<!-- Terms Agreement -->
							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" id="termsAgree" required checked>
								<label class="form-check-label text-muted small" for="termsAgree" style="font-size: 12px; line-height: 1.4;">
									{!! __('I have read and agree to the applicable :terms.', ['terms' => '<a href="' . route('ecommerce.terms.html') . '" target="_blank" class="text-primary">' . __('Terms & Conditions') . '</a>']) !!}
								</label>
							</div>

							<!-- PLACE ORDER BUTTON (PALING BAWAH KANAN) -->
							<div class="mn-checkout-order-btn">
								<button type="button" id="btnPlaceOrder" class="mn-btn-1 btn w-100 py-3 text-center shadow" style="border-radius: 8px; font-size: 16px; font-weight: bold;" onclick="handlePlaceOrder()">
									<span><i class="ri-shopping-cart-2-line me-1"></i> {{ __('Place Order') }}</span>
								</button>
							</div>

							<!-- Trust & Security Badges -->
							<div class="text-center pt-3 mt-3 border-top">
								<small class="text-muted d-block mb-2" style="font-size: 11px;">
									<i class="ri-shield-check-line text-success me-1"></i> {{ __('Secure & Encrypted Payment') }}
								</small>
								<img src="{{ asset('mantu/assets/img/footer/payment.png') }}" alt="{{ __('Secure Payment') }}" style="max-height: 22px; filter: grayscale(15%);">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>

<!-- Modals for QR Code Bank -->
@foreach ($paymentBanks as $bank)
	@if ($bank->qr_code_url)
		<div class="modal fade" id="qrModal{{ $bank->id }}" tabindex="-1" aria-labelledby="qrModalLabel{{ $bank->id }}" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-sm">
				<div class="modal-content text-center p-3" style="border-radius: 12px;">
					<div class="modal-header border-0 pb-0">
						<h5 class="modal-title w-100 fw-bold" id="qrModalLabel{{ $bank->id }}" style="font-size: 15px;">QR Code {{ $bank->bank_name }}</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<img src="{{ $bank->qr_code_url }}" alt="QR Code {{ $bank->bank_name }}" class="img-fluid rounded border p-2 mb-2" style="max-height: 240px; width: 100%; object-fit: contain;">
						<p class="small text-muted mb-1">a.n. <strong>{{ $bank->account_name }}</strong></p>
						<p class="font-monospace fw-bold text-primary mb-0" style="font-size: 15px;">{{ $bank->account_number }}</p>
					</div>
				</div>
			</div>
		</div>
	@endif
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
	const subtotal = {{ (float) $subtotal }};

	function formatRupiah(num) {
		return 'Rp ' + Number(num).toLocaleString('id-ID');
	}

	function updateTotalSummary() {
		const selectedShipping = document.querySelector('input[name="shipping_method_id"]:checked');
		const shippingCost = selectedShipping ? parseFloat(selectedShipping.dataset.cost || 0) : 0;

		const selectedPayment = document.querySelector('input[name="payment_method_id"]:checked');
		const paymentFee = selectedPayment ? parseFloat(selectedPayment.dataset.fee || 0) : 0;
		const paymentType = selectedPayment ? selectedPayment.dataset.type : '';

		// Update Shipping summary
		const shippingCostEl = document.getElementById('summaryShippingCost');
		if (shippingCostEl) {
			if (shippingCost <= 0) {
				shippingCostEl.innerHTML = '<span class="text-success fw-bold">' + @json(__('Free')) + '</span>';
			} else {
				shippingCostEl.textContent = formatRupiah(shippingCost);
			}
		}

		// Update Payment Fee summary
		const feeRow = document.getElementById('summaryFeeRow');
		const feeEl = document.getElementById('summaryPaymentFee');
		if (feeRow && feeEl) {
			if (paymentFee > 0) {
				feeRow.style.display = 'flex';
				feeEl.textContent = formatRupiah(paymentFee);
			} else {
				feeRow.style.display = 'none';
			}
		}

		// Update Grand Total
		const grandTotal = subtotal + shippingCost + paymentFee;
		const totalEl = document.getElementById('summaryTotal');
		if (totalEl) {
			totalEl.textContent = formatRupiah(grandTotal);
		}

		// Bank transfer accounts visibility
		const bankSection = document.getElementById('bankAccountsSection');
		if (bankSection) {
			if (paymentType === 'bank_transfer') {
				bankSection.style.display = 'block';
			} else {
				bankSection.style.display = 'none';
			}
		}

		// Payment instructions toggle
		document.querySelectorAll('.payment-instruction-box').forEach(function (el) {
			el.style.display = 'none';
		});
		if (selectedPayment) {
			const activeInstruction = document.getElementById('instruction_' + selectedPayment.value);
			if (activeInstruction) {
				activeInstruction.style.display = 'block';
			}
		}
	}

	// Shipping radio listener
	document.querySelectorAll('input[name="shipping_method_id"]').forEach(function (radio) {
		radio.addEventListener('change', function () {
			document.querySelectorAll('.shipping-method-card').forEach(function (card) {
				card.classList.remove('border-primary', 'shadow-sm', 'bg-light');
			});
			const card = this.closest('.shipping-method-card');
			if (card) {
				card.classList.add('border-primary', 'shadow-sm', 'bg-light');
			}
			updateTotalSummary();
		});
	});

	// Payment radio listener
	document.querySelectorAll('input[name="payment_method_id"]').forEach(function (radio) {
		radio.addEventListener('change', function () {
			document.querySelectorAll('.payment-method-card').forEach(function (card) {
				card.classList.remove('border-primary', 'shadow-sm', 'bg-light');
			});
			const card = this.closest('.payment-method-card');
			if (card) {
				card.classList.add('border-primary', 'shadow-sm', 'bg-light');
			}
			updateTotalSummary();
		});
	});

	// Bank radio listener
	document.querySelectorAll('input[name="payment_bank_id"]').forEach(function (radio) {
		radio.addEventListener('change', function () {
			document.querySelectorAll('.bank-option-card').forEach(function (card) {
				card.classList.remove('border-primary');
			});
			const card = this.closest('.bank-option-card');
			if (card) {
				card.classList.add('border-primary');
			}
		});
	});

	// Copy account button listener
	document.querySelectorAll('.btn-copy-acc').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
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
			this.innerHTML = '<i class="ri-check-line text-success"></i> ' + @json(__('Copied!'));
			this.classList.add('btn-success', 'text-white');
			this.classList.remove('btn-outline-secondary');
			const self = this;
			setTimeout(function () {
				self.innerHTML = originalHtml;
				self.classList.remove('btn-success', 'text-white');
				self.classList.add('btn-outline-secondary');
			}, 1800);
		});
	});

	// Initial run
	updateTotalSummary();
});

function handlePlaceOrder() {
	const terms = document.getElementById('termsAgree');
	if (terms && !terms.checked) {
		alert(@json(__('Please agree to the Terms & Conditions first.')));
		return;
	}

	const addressId = document.getElementById('selectedAddressId');
	if (!addressId || !addressId.value) {
		alert(@json(__('Shipping address is not available yet. Please add a shipping address first.')));
		window.location.href = "{{ route('ecommerce.address-list') }}";
		return;
	}

	const selectedShipping = document.querySelector('input[name="shipping_method_id"]:checked');
	const selectedPayment = document.querySelector('input[name="payment_method_id"]:checked');

	if (!selectedShipping) {
		alert(@json(__('Please select a shipping method first.')));
		return;
	}

	if (!selectedPayment) {
		alert(@json(__('Please select a payment method first.')));
		return;
	}

	const submitBtn = document.getElementById('btnPlaceOrder');
	if (submitBtn) {
		submitBtn.disabled = true;
		submitBtn.innerHTML = '<span><i class="ri-loader-4-line ri-spin me-1"></i> ' + @json(__('Processing Order...')) + '</span>';
	}

	document.getElementById('checkoutForm').submit();
}
</script>
@endsection
