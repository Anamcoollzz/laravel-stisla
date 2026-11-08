@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Wishlist - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Wishlist Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Wishlist Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Wishlist section -->
			<section class="mn-wishlist-page p-b-15">
				<div class="mn-title d-none">
					<h2>Product <span>Wishlist</span></h2>
					<p>Your product wish is our first priority.</p>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="mn-vendor-dashboard-card">
							<div class="mn-vendor-card-header">
								<h5>Wishlist</h5>
								<div class="mn-header-btn">
									<a class="mn-btn-2" href="#"><span>Shop Now</span></a>
								</div>
							</div>
							<div class="mn-vendor-card-body">
								<div class="mn-vendor-card-table">
									<table class="mn-table">
										<thead>
											<tr>
												<th scope="col">ID</th>
												<th scope="col">Product</th>
												<th scope="col">Date</th>
												<th scope="col">Price</th>
												<th scope="col">Status</th>
												<th scope="col">Actions</th>
											</tr>
										</thead>
										<tbody class="wish-empt">
											<tr class="pro-gl-content">
												<td><span>548</span></td>
												<td data-label="Product" class="mn-cart-pro-name">
													<a href="{{ route('ecommerce.product-detail.html') }}" class="mn-item">
														<img class="prod-img"
															src="{{ asset('mantu/assets/img/product/1.jpg') }}" alt="product image">T-shirt for womens
													</a>
												</td>
												<td><span>13 Aug 2026</span></td>
												<td><span>$25</span></td>
												<td><span class="out">Out Of Stock</span></td>
												<td>
													<span class="tbl-btn">
														<a class="mn-btn-2 add-to-cart mn-add-cart" href="javascript:void(0)"
															title="Add To Cart">
															<span><i class="ri-shopping-cart-line"></i></span>
														</a>
														<a class="mn-btn-1 mn-remove-wish btn"
															href="javascript:void(0)" title="Remove From List">
															<span><i class="ri-close-line"></i></span>
														</a>
													</span>
												</td>
											</tr>
											<tr class="pro-gl-content">
												<td><span>684</span></td>
												<td data-label="Product" class="mn-cart-pro-name">
													<a href="{{ route('ecommerce.product-detail.html') }}" class="mn-item">
														<img class="prod-img"
															src="{{ asset('mantu/assets/img/product/23.jpg') }}" alt="product image">Women's winter coat
													</a>
												</td>
												<td><span>20 Jul 2027</span></td>
												<td><span>$360</span></td>
												<td><span class="avl">Available</span></td>
												<td>
													<span class="tbl-btn">
														<a class="mn-btn-2 add-to-cart mn-add-cart" href="javascript:void(0)"
															title="Add To Cart">
															<span><i class="ri-shopping-cart-line"></i></span>
														</a>
														<a class="mn-btn-1 mn-remove-wish btn"
															href="javascript:void(0)" title="Remove From List">
															<span><i class="ri-close-line"></i></span>
														</a>
													</span>
												</td>
											</tr>
											<tr class="pro-gl-content">
												<td><span>548</span></td>
												<td data-label="Product" class="mn-cart-pro-name">
													<a href="{{ route('ecommerce.product-detail.html') }}" class="mn-item">
														<img class="prod-img"
															src="{{ asset('mantu/assets/img/product/11.jpg') }}" alt="product image">Mantu smart
														watch
													</a>
												</td>
												<td><span>28 Mar 2024</span></td>
												<td><span>$74</span></td>
												<td><span class="dis">Disabled</span></td>
												<td>
													<span class="tbl-btn">
														<a class="mn-btn-2 add-to-cart mn-add-cart" href="javascript:void(0)"
															title="Add To Cart">
															<span><i class="ri-shopping-cart-line"></i></span>
														</a>
														<a class="mn-btn-1 mn-remove-wish btn"
															href="javascript:void(0)" title="Remove From List">
															<span><i class="ri-close-line"></i></span>
														</a>
													</span>
												</td>
											</tr>
											<tr class="pro-gl-content">
												<td><span>684</span></td>
												<td data-label="Product" class="mn-cart-pro-name">
													<a href="{{ route('ecommerce.product-detail.html') }}" class="mn-item">
														<img class="prod-img"
															src="{{ asset('mantu/assets/img/product/15.jpg') }}" alt="product image">Leather textured bag
													</a>
												</td>
												<td><span>16 Apr 2026</span></td>
												<td><span>$245</span></td>
												<td><span class="avl">Available</span></td>
												<td>
													<span class="tbl-btn">
														<a class="mn-btn-2 add-to-cart mn-add-cart" href="javascript:void(0)"
															title="Add To Cart">
															<span><i class="ri-shopping-cart-line"></i></span>
														</a>
														<a class="mn-btn-1 mn-remove-wish btn"
															href="javascript:void(0)" title="Remove From List">
															<span><i class="ri-close-line"></i></span>
														</a>
													</span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
