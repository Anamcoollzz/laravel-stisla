@extends('ecommerce.layouts.app-ecommerce')

@section('title', __('Order List - Ecommerce'))

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
								<h2 class="mn-breadcrumb-title">{{ __('Order List') }}</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">{{ __('Home') }}</a></li>
									<li class="mn-breadcrumb-item active">{{ __('Order List') }}</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Order list section -->
			<section class="mn-wishlist-page p-b-15">
				<div class="row">
					<div class="col-md-12">
						<div class="mn-vendor-dashboard-card">
							<div class="mn-vendor-card-header">
								<h5>{{ __('My Orders') }}</h5>
								<div class="mn-header-btn d-flex align-items-center gap-2">
									<a class="mn-btn-2" href="{{ route('ecommerce.products.index') }}"><span>{{ __('Shop Now') }}</span></a>
								</div>
							</div>
							<div class="mn-vendor-card-body">
								<div class="mn-vendor-card-table">
									<table class="mn-table">
										<thead>
											<tr>
												<th scope="col">{{ __('Order #') }}</th>
												<th scope="col">{{ __('Date') }}</th>
												<th scope="col">{{ __('Items') }}</th>
												<th scope="col">{{ __('Total') }}</th>
												<th scope="col">{{ __('Status') }}</th>
												<th scope="col">{{ __('Payment') }}</th>
											</tr>
										</thead>
										<tbody>
											@forelse ($orders as $order)
												@php
													$firstItem = $order->items->first();
													$totalQty = $order->items->sum('quantity');
													$statusClass = match ($order->status) {
														'completed', 'delivered' => 'avl',
														'cancelled', 'failed' => 'out',
														default => 'text-warning fw-semibold',
													};
													$paymentClass = match ($order->payment_status) {
														'paid' => 'avl',
														'failed', 'expired' => 'out',
														default => 'text-warning fw-semibold',
													};
												@endphp
												<tr>
													<td>
														<a href="{{ route('ecommerce.checkout.detail', $order->order_number) }}" class="fw-semibold text-primary">
															{{ $order->order_number }}
														</a>
													</td>
													<td><span>{{ $order->created_at?->format('d M Y H:i') }}</span></td>
													<td data-label="Items">
														@if ($firstItem && $firstItem->product)
															<a href="{{ route('ecommerce.checkout.detail', $order->order_number) }}" class="mn-item">
																<img class="prod-img" src="{{ $firstItem->product->thumbnail_url }}" alt="{{ $firstItem->product_title ?? $firstItem->product->title }}">
																<span>
																	{{ $firstItem->product_title ?? $firstItem->product->title }}
																	@if ($order->items->count() > 1)
																		<small class="text-muted">(+{{ $order->items->count() - 1 }} {{ __('other products') }}, {{ $totalQty }} {{ __('pcs') }})</small>
																	@else
																		<small class="text-muted">({{ $totalQty }} {{ __('pcs') }})</small>
																	@endif
																</span>
															</a>
														@else
															<span>{{ $totalQty }} {{ __('item') }}</span>
														@endif
													</td>
													<td><span class="fw-semibold">{{ $order->formatted_grand_total }}</span></td>
													<td>
														<span class="{{ $statusClass }}">{{ __(ucfirst($order->status)) }}</span>
													</td>
													<td>
														<span class="{{ $paymentClass }}">{{ __(ucfirst($order->payment_status)) }}</span>
													</td>
												</tr>
											@empty
												<tr>
													<td colspan="7" class="text-center p-5">
														<div class="m-b-15">
															<i class="ri-shopping-bag-3-line" style="font-size: 48px; color: #adb5bd;"></i>
														</div>
														<h4>{{ __('No orders yet') }}</h4>
														<p class="text-muted">{{ __('You do not have any order history yet. Start shopping now.') }}</p>
														<div class="m-t-15">
															<a href="{{ route('ecommerce.products.index') }}" class="mn-btn-2"><span>{{ __('Shop Now') }}</span></a>
														</div>
													</td>
												</tr>
											@endforelse
										</tbody>
									</table>
								</div>

								@if ($orders->hasPages())
									<div class="mt-4 d-flex justify-content-center">
										{{ $orders->links('pagination::bootstrap-5') }}
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
