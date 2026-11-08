@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Blog Right Sidebar - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Blog Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Blog Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-30">
				<div class="mn-blogs-rightside col-lg-8 col-md-12">
					<!-- Blog content Start -->
					<div class="mn-blogs-content mn-blog">
						<div class="mn-blogs-inner">
							<div class="row">
								<div class="col-sm-6 col-12 mn-blog-block m-b-24">
									<div class="mn-blog-card">
										<div class="blog-info">
											<figure class="blog-img"><a href="#"><img src="{{ asset('mantu/assets/img/blog/1.jpg') }}"
														alt="news imag"></a>
											</figure>
											<div class="detail">
												<label>June 30, 2025 - <a href="#">Fashion</a></label>
												<h3><a href="#">Marketing Guide: 5 Steps to Success to
														way.</a></h3>
												<div class="more-info">
													<a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Read More<i
															class="ri-arrow-right-double-line"></i></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 col-12 mn-blog-block m-b-24">
									<div class="mn-blog-card">
										<div class="blog-info">
											<figure class="blog-img"><a href="#"><img src="{{ asset('mantu/assets/img/blog/2.jpg') }}"
														alt="news imag"></a>
											</figure>
											<div class="detail">
												<label>April 02, 2025 - <a href="#">Cosmetics</a></label>
												<h3><a href="#">Best way to solve business issue in
														market.</a>
												</h3>
												<div class="more-info">
													<a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Read More<i
															class="ri-arrow-right-double-line"></i></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 col-12 mn-blog-block m-b-24">
									<div class="mn-blog-card">
										<div class="blog-info">
											<figure class="blog-img"><a href="#"><img src="{{ asset('mantu/assets/img/blog/3.jpg') }}"
														alt="news imag"></a>
											</figure>
											<div class="detail">
												<label>Mar 09, 2025 - <a href="#">Bags</a></label>
												<h3><a href="#">31 grocery customer service stats know in
														2025.</a></h3>
												<div class="more-info">
													<a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Read More<i
															class="ri-arrow-right-double-line"></i></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 col-12 mn-blog-block m-b-24">
									<div class="mn-blog-card">
										<div class="blog-info">
											<figure class="blog-img"><a href="#"><img src="{{ asset('mantu/assets/img/blog/4.jpg') }}"
														alt="news imag"></a>
											</figure>
											<div class="detail">
												<label>January 25, 2025 - <a href="#">Shoes</a></label>
												<h3><a href="#">Business ideas to grow your business
														traffic.</a></h3>
												<div class="more-info">
													<a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Read More<i
															class="ri-arrow-right-double-line"></i></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 col-12 mn-blog-block m-b-24">
									<div class="mn-blog-card">
										<div class="blog-info">
											<figure class="blog-img"><a href="#"><img src="{{ asset('mantu/assets/img/blog/5.jpg') }}"
														alt="news imag"></a>
											</figure>
											<div class="detail">
												<label>December 10, 2026 - <a href="#">Perfumes</a></label>
												<h3><a href="#">Marketing Guide: 5 Steps way to Success.</a>
												</h3>
												<div class="more-info">
													<a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Read More<i
															class="ri-arrow-right-double-line"></i></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 col-12 mn-blog-block m-b-24">
									<div class="mn-blog-card">
										<div class="blog-info">
											<figure class="blog-img"><a href="#"><img src="{{ asset('mantu/assets/img/blog/6.jpg') }}"
														alt="news imag"></a>
											</figure>
											<div class="detail">
												<label>August 08, 2027 - <a href="#">Bags</a></label>
												<h3><a href="#">15 customer service stats idea know in
														2026.</a></h3>
												<div class="more-info">
													<a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Read More<i
															class="ri-arrow-right-double-line"></i></a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Pagination Start -->
						<div class="mn-pro-pagination">
							<span>Showing 1-6 of 20 items</span>
							<ul class="mn-pro-pagination-inner">
								<li><a class="active" href="#">1</a></li>
								<li><a href="#">2</a></li>
								<li><a href="#">3</a></li>
								<li><span>...</span></li>
								<li><a href="#">8</a></li>
								<li><a class="next" href="#">Next <i class="ri-arrow-right-double-line"></i></a>
								</li>
							</ul>
						</div>
						<!-- Pagination End -->
					</div>
					<!--Blog content End -->
				</div>

				<!-- Sidebar Area Start -->
				<div class="mn-blogs-sidebar mn-blogs-leftside col-lg-4 col-md-12 m-t-991">
					<div class="mn-blog-search">
						<form class="mn-blog-search-form" action="#">
							<input class="form-control" placeholder="Search Our Blog" type="text">
							<button class="submit" type="submit"><i class="ri-search-line"></i></button>
						</form>
					</div>
					<div class="mn-blog-sidebar-wrap">
						<!-- Sidebar Recent Blog Block -->
						<div class="mn-sidebar-block mn-sidebar-recent-blog">
							<div class="mn-sb-title">
								<h3 class="mn-sidebar-title">Recent Articles</h3>
							</div>
							<div class="mn-blog-block-content mn-sidebar-dropdown">
								<div class="mn-sidebar-block-item">
									<div class="mn-sidebar-block-img">
										<img src="{{ asset('mantu/assets/img/blog/6.jpg') }}" alt="blog imag">
									</div>
									<div class="mn-sidebar-block-detial">
										<h5 class="mn-blog-title"><a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">The best
												fashion influencers.</a></h5>
										<div class="mn-blog-date">February 10, 2025-2026</div>
										<a href="{{ route('ecommerce.blog-right-sidebar.html') }}">- Organic</a>
									</div>
								</div>
								<div class="mn-sidebar-block-item">
									<div class="mn-sidebar-block-img">
										<img src="{{ asset('mantu/assets/img/blog/5.jpg') }}" alt="blog imag">
									</div>
									<div class="mn-sidebar-block-detial">
										<h5 class="mn-blog-title"><a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Vogue
												Shopping
												Weekend.</a></h5>
										<div class="mn-blog-date">March 14, 2025-2026</div>
										<a href="{{ route('ecommerce.blog-right-sidebar.html') }}">- Fruits</a>
									</div>
								</div>
								<div class="mn-sidebar-block-item">
									<div class="mn-sidebar-block-img">
										<img src="{{ asset('mantu/assets/img/blog/4.jpg') }}" alt="blog imag">
									</div>
									<div class="mn-sidebar-block-detial">
										<h5 class="mn-blog-title"><a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Fashion
												Market
												Reveals Her Jacket.</a></h5>
										<div class="mn-blog-date">June 09, 2025-2026</div>
										<a href="{{ route('ecommerce.blog-right-sidebar.html') }}">- Vegetables</a>
									</div>
								</div>
								<div class="mn-sidebar-block-item">
									<div class="mn-sidebar-block-img">
										<img src="{{ asset('mantu/assets/img/blog/3.jpg') }}" alt="blog imag">
									</div>
									<div class="mn-sidebar-block-detial">
										<h5 class="mn-blog-title"><a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Summer
												Trending Fashion Market.</a></h5>
										<div class="mn-blog-date">July 17, 2025-2026</div>
										<a href="{{ route('ecommerce.blog-right-sidebar.html') }}">- Fastfood</a>
									</div>
								</div>
								<div class="mn-sidebar-block-item">
									<div class="mn-sidebar-block-img">
										<img src="{{ asset('mantu/assets/img/blog/2.jpg') }}" alt="blog imag">
									</div>
									<div class="mn-sidebar-block-detial">
										<h5 class="mn-blog-title"><a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Winter 2025
												Trending Fashion Market.</a></h5>
										<div class="mn-blog-date">August 02, 2025-2026</div>
										<a href="{{ route('ecommerce.blog-right-sidebar.html') }}">- Vegetables</a>
									</div>
								</div>
							</div>
						</div>
						<!-- Sidebar Recent Blog Block -->
						<!-- Sidebar Category Block -->
						<div class="mn-sidebar-block">
							<div class="mn-sb-title">
								<h3 class="mn-sidebar-title">Categories</h3>
							</div>
							<div class="mn-blog-block-content mn-sidebar-dropdown">
								<ul>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox" checked> <a href="javascript:void(0)">Dairy &
												Milk<span title="Products">- 68</span></a><span class="checked"></span>
										</div>
									</li>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox"> <a href="javascript:void(0)">Seafood<span
													title="Products">-
													58</span></a><span class="checked"></span>
										</div>
									</li>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox"> <a href="javascript:void(0)">Bakery<span
													title="Products">- 84</span></a><span class="checked"></span>
										</div>
									</li>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox"> <a href="javascript:void(0)">cosmetics<span
													title="Products">-
													63</span></a><span class="checked"></span>
										</div>
									</li>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox"> <a href="javascript:void(0)">electrics<span
													title="Products">-
													75</span></a><span class="checked"></span>
										</div>
									</li>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox"> <a href="javascript:void(0)">phones<span
													title="Products">- 26</span></a><span class="checked"></span>
										</div>
									</li>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox"> <a href="javascript:void(0)">Clothes<span
													title="Products">-
													39</span></a><span class="checked"></span>
										</div>
									</li>
									<li>
										<div class="mn-sidebar-block-item">
											<input type="checkbox"> <a href="javascript:void(0)">Watch<span
													title="Products">- 48</span></a><span class="checked"></span>
										</div>
									</li>

								</ul>
							</div>
						</div>
						<!-- Sidebar Category Block -->
					</div>
				</div>
			</div>
		</div>
@endsection
