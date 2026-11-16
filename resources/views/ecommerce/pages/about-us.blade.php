@php
  $ourTeams = $ourTeams ?? \App\Models\OurTeam::query()->active()->orderBy('order')->latest('id')->get();
  $testimonials = $testimonials ?? \App\Models\Testimonial::query()->active()->orderBy('order')->latest('id')->get();
  $about = $about ?? \App\Models\Setting::getAboutData();
  $facts = $facts ?? [
      'vendors' => \App\Models\ProductVendor::count(),
      'brands' => \App\Models\ProductBrand::count(),
      'stock' => (int) \App\Models\Product::where('is_active', true)->sum('stock'),
      'products' => \App\Models\Product::where('is_active', true)->count(),
  ];
@endphp
@extends('ecommerce.layouts.app-ecommerce')

@section('title', __('About Us - Ecommerce'))

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
								<h2 class="mn-breadcrumb-title">{{ __('About us Page') }}</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">{{ __('Home') }}</a></li>
									<li class="mn-breadcrumb-item active">{{ __('About us Page') }}</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- About section -->
			<section class="mn-about p-b-15">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<div class="mn-about-img">
							<img src="{{ $about['image_1'] }}" class="v-img" alt="about 1">
							<img src="{{ $about['image_2'] }}" class="h-img" alt="about 2">
							<img src="{{ $about['image_3'] }}" class="h-img" alt="about 3">
							<img src="{{ $about['image_4'] }}" class="h-img" alt="about 4">
						</div>
					</div>
					<div class="col-lg-6 col-md-12">
						<div class="mn-about-detail">
							<div class="section-title m-t-991">
								<h2>{!! $about['title'] !!}</h2>
								<p>{{ $about['subtitle'] }}</p>
							</div>
							{!! $about['content'] !!}
						</div>
					</div>
				</div>
			</section>
			<!-- About section End -->

			@php
				$storeServices = $storeServices ?? \App\Models\StoreService::query()->active()->orderBy('order')->latest('id')->get();
			@endphp

			<!-- Service Section -->
			@if ($storeServices->isNotEmpty())
				<section class="mn-service p-tb-15">
					<div class="row m-b-m-24">
						@foreach ($storeServices as $service)
							<div class="mn-ser-content mn-ser-content-{{ ($loop->index % 4) + 1 }} col-sm-6 col-xl-3 m-b-24" data-aos="fade-up"
								data-aos-duration="1000" data-aos-delay="{{ (($loop->index % 4) + 1) * 200 }}">
								<div class="mn-ser-box">
									<div class="mn-ser-inner">
										<div class="mn-service-image">
											@if ($service->icon_image_url)
												<img src="{{ $service->icon_image_url }}" alt="{{ $service->title }}">
											@elseif ($service->icon_svg)
												{!! $service->icon_svg !!}
											@endif
										</div>
										<div class="mn-service-desc">
											<h3>{{ $service->title }}</h3>
											<p>{{ $service->description }}</p>
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</section>
			@endif

			<!-- Testimonials Section -->
			<section class="mn-testimonials p-tb-15">
				<div class="row">
					<div class="col-md-12">
						<div class="testim-bg">
							<div class="section-title d-none">
								<h2>{{ __('Customers') }} <span>{{ __('Review') }}</span></h2>
							</div>
							<div class="mn-test-outer mn-testimonials">
								<ul class="mn-testimonial-slider owl-carousel">
									@forelse ($testimonials as $testimonial)
										<li class="mn-test-item">
											<img src="{{ asset('mantu/assets/img/icons/top-quotes.svg') }}" class="svg_img test_svg top" alt="quotes">
											<div class="mn-test-inner">
												<div class="mn-test-img" style="width: 100px; height: 100px; overflow: hidden; border-radius: 50%;">
													<img alt="{{ $testimonial->name }}" title="{{ $testimonial->name }}" src="{{ $testimonial->image_url }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; display: block;">
												</div>
												<div class="mn-test-content">
													<div class="mn-test-desc">{{ $testimonial->content }}</div>
													<div class="mn-test-name">{{ $testimonial->name }}</div>
													<div class="mn-test-designation">({{ $testimonial->designation ?: __('Customer') }})</div>
												</div>
											</div>
											<img src="{{ asset('mantu/assets/img/icons/bottom-quotes.svg') }}" class="svg_img test_svg bottom" alt="quotes">
										</li>
									@empty
										<li class="mn-test-item">
											<div class="mn-test-inner text-center py-4">
												<p class="text-muted">{{ __('No customer reviews yet.') }}</p>
											</div>
										</li>
									@endforelse
								</ul>
							</div>
							<span class="mn-testi-shape-2"></span>
						</div>
					</div>
				</div>
			</section>

			<!-- Facts Section -->
			<section class="mn-facts-section p-tb-15">
				<div class="row m-b-m-24">
					<div class="mn-facts-content col-sm-6 col-xl-3 m-b-24">
						<div class="mn-facts-inner">
							<div class="mn-count">
								<span class="counter">{{ number_format($facts['vendors'] ?? 0) }}{{ ($facts['vendors'] ?? 0) > 0 ? '+' : '' }}</span>
							</div>
							<div class="mn-facts-desc">
								<h4>{{ __('Vendors') }}</h4>
								<p>{{ __('Verified and trusted merchant partners providing premium catalog goods.') }}</p>
							</div>
						</div>
					</div>
					<div class="mn-facts-content col-sm-6 col-xl-3 m-b-24">
						<div class="mn-facts-inner">
							<div class="mn-count">
								<span class="counter">{{ number_format($facts['brands'] ?? 0) }}{{ ($facts['brands'] ?? 0) > 0 ? '+' : '' }}</span>
							</div>
							<div class="mn-facts-desc">
								<h4>{{ __('Brands') }}</h4>
								<p>{{ __('Recognized and curated domestic and international brands.') }}</p>
							</div>
						</div>
					</div>
					<div class="mn-facts-content col-sm-6 col-xl-3 m-b-24">
						<div class="mn-facts-inner">
							<div class="mn-count">
								<span class="counter">{{ number_format($facts['stock'] ?? 0, 0, ',', '.') }}{{ ($facts['stock'] ?? 0) > 0 ? '+' : '' }}</span>
							</div>
							<div class="mn-facts-desc">
								<h4>{{ __('Items in Stock') }}</h4>
								<p>{{ __('Ready to ship units stored across active fulfillment warehouses.') }}</p>
							</div>
						</div>
					</div>
					<div class="mn-facts-content col-sm-6 col-xl-3 m-b-24">
						<div class="mn-facts-inner">
							<div class="mn-count">
								<span class="counter">{{ number_format($facts['products'] ?? 0) }}{{ ($facts['products'] ?? 0) > 0 ? '+' : '' }}</span>
							</div>
							<div class="mn-facts-desc">
								<h4>{{ __('Products') }}</h4>
								<p>{{ __('Diverse range of verified and quality guaranteed items.') }}</p>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Facts Section End -->

			<!-- Team Section -->
			<section class="mn-team-section p-tb-15">
				<div class="mn-title">
					<h2>{{ __('Our') }} <span>{{ __('Team') }}</span></h2>
				</div>
				<div class="mn-team owl-carousel">
					@forelse ($ourTeams as $member)
						<div class="mn-team-box">
							<div class="mn-team-img">
								<img src="{{ $member->image_url }}" alt="{{ $member->name }}" style="aspect-ratio: 1 / 1; object-fit: cover;">
								<div class="mn-team-socials">
									<ul class="align-itemn-center">
										@if ($member->twitter_url)
											<li class="mn-social-link">
												<a href="{{ $member->twitter_url }}" target="_blank"><i class="ri-twitter-line"></i></a>
											</li>
										@endif
										@if ($member->facebook_url)
											<li class="mn-social-link">
												<a href="{{ $member->facebook_url }}" target="_blank"><i class="ri-facebook-line"></i></a>
											</li>
										@endif
										@if ($member->linkedin_url)
											<li class="mn-social-link">
												<a href="{{ $member->linkedin_url }}" target="_blank"><i class="ri-linkedin-line"></i></a>
											</li>
										@endif
									</ul>
								</div>
							</div>
							<div class="mn-team-info">
								<h5>{{ $member->name }}</h5>
								<p>{{ $member->position }}</p>
							</div>
						</div>
					@empty
						<div class="mn-team-box">
							<div class="mn-team-img">
								<img src="{{ asset('mantu/assets/img/user/1.jpg') }}" alt="user">
							</div>
							<div class="mn-team-info">
								<h5>Olivia Smith</h5>
								<p>Founder</p>
							</div>
						</div>
					@endforelse
				</div>
			</section>
			<!-- Facts Section End -->
		</div>
@endsection
