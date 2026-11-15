@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Compare - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Compare Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Compare Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Compare section -->
			<section class="mn-compare-list p-b-15">
				<h2 class="d-none">Compare</h2>
				<div class="row">
					<div class="col-md-12">
						<div class="mn-compare-box">
							@if (($compares ?? collect())->isEmpty())
								<div class="p-4 text-center w-100">
									<p class="mn-compare-page-msg mb-3">Your Compare list is empty!</p>
									<a href="{{ route('ecommerce.products.index') }}" class="mn-btn-1"><span>Browse Products</span></a>
								</div>
							@else
								<div class="mn-compare-col title-col">
									<div class="mn-compare-cell">
										<div class="title">
											<h5>Product Image</h5>
										</div>
									</div>
									<div class="mn-compare-cell">
										<h5>Name</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>Category</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>Ratings</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>Availability</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>location</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>Brand</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>SKU</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>Quantity</h5>
									</div>
									<div class="mn-compare-cell">
										<h5>Size</h5>
									</div>
									<div class="mn-compare-cell">
										<div class="desc">
											<h5>Description</h5>
										</div>
									</div>
								</div>
								@foreach ($compares as $compare)
									@php
										$product = $compare->product;
										$avgRating = round($product->average_rating ?? 0);
										$reviewCount = $product->approved_reviews_count ?? 0;
									@endphp
									<div class="mn-compare-col product-col" data-product-id="{{ $product->id }}">
										<a href="javascript:void(0)" class="remove-compare-product" data-product-id="{{ $product->id }}"><i
												class="ri-close-large-line"></i></a>
										<div class="mn-compare-cell">
											<div class="list" style="overflow: hidden; border-radius: 15px;">
												<a href="{{ route('ecommerce.products.detail', $product->slug) }}" class="d-block w-100">
													<img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" style="width: 100%; height: 200px; object-fit: cover; display: block;">
												</a>
												<div class="mn-action">
													<ul>
														<li>
															<a href="javascript:void(0);" class="mn-btn-group wishlist mn-wishlist {{ in_array($product->id, $userWishlistIds ?? []) ? 'active' : '' }}" data-product-id="{{ $product->id }}" title="Wishlist"><i
																	class="ri-heart-line"></i></a>
														</li>
														<li>
															<a href="javascript:void(0)" title="Add To Cart"
																class="mn-btn-group add-to-cart mn-add-cart" data-product-id="{{ $product->id }}"><i
																	class="ri-shopping-cart-line"></i></a>
														</li>
													</ul>
												</div>
											</div>
										</div>
										<div class="mn-compare-cell">
											<p><a href="{{ route('ecommerce.products.detail', $product->slug) }}" class="text-reset">{{ $product->title }}</a></p>
										</div>
										<div class="mn-compare-cell">
											<p>{{ $product->category?->name ?? '-' }}</p>
										</div>
										<div class="mn-compare-cell">
											<span class="mn-pro-rating">
												@for ($star = 1; $star <= 5; $star++)
													<i class="ri-star-fill {{ $star <= $avgRating ? '' : 'grey' }}"></i>
												@endfor
											</span>
											<p class="rating-info">({{ $reviewCount }} Review)</p>
										</div>
										<div class="mn-compare-cell">
											@if ($product->stock > 0)
												<p class="i-stock">In Stock</p>
											@else
												<p class="o-stock">Out Of Stock</p>
											@endif
										</div>
										<div class="mn-compare-cell">
											<p>{{ $compare->location ?: 'In Store, Online' }}</p>
										</div>
										<div class="mn-compare-cell">
											<p>{{ $product->brand?->name ?? ($product->vendor?->name ?? '-') }}</p>
										</div>
										<div class="mn-compare-cell">
											<p>{{ $product->sku ?? '-' }}</p>
										</div>
										<div class="mn-compare-cell">
											<p>{{ $compare->quantity }}</p>
										</div>
										<div class="mn-compare-cell">
											<p>{{ $product->size?->name ?? '-' }}</p>
										</div>
										<div class="mn-compare-cell">
											<div class="desc">
												<p>{{ \Illuminate\Support\Str::limit(strip_tags($product->short_description ?: $product->description_detail), 140) }}</p>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
