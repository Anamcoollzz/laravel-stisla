@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Track Order - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Track Order</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Track Order</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Track Order section -->
			<section class="mn-track p-b-15">
				<div class="mn-title d-none">
					<h2>Track<span> Order</span></h2>
					<p>We delivering happiness and needs, Faster than you can think.</p>
				</div>
				<div class="row">
					<div class="container">
						<div class="mn-track-box">
							<!-- Details-->
							<div class="row m-b-m-30">
								<div class="col-lg-3 col-sm-6 col-12 m-b-30">
									<div class="mn-track-card"><span
											class="mn-track-title">order</span><span>#5874</span>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12 m-b-30">
									<div class="mn-track-card"><span class="mn-track-title">Men's
											T-shirt</span><span>m975mn</span></div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12 m-b-30">
									<div class="mn-track-card"><span class="mn-track-title">Expected
											date</span><span>June
											15, 2026</span></div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12 m-b-30">
									<div class="mn-track-card"><span class="mn-track-title">Progress</span><span>
											Quality Check
										</span></div>
								</div>
							</div>
							<!-- Progress-->
							<div class="mn-steps">
								<div class="mn-steps-body">
									<div class="mn-step mn-step-completed">
										<span class="mn-step-indicator">
											<i class="ri-check-line"></i>
										</span>
										<span class="mn-step-icon">
											<i class="ri-shield-check-line"></i>
										</span>Order<br> confirmed
									</div>

									<div class="mn-step mn-step-completed">
										<span class="mn-step-indicator">
											<i class="ri-check-line"></i>
										</span>
										<span class="mn-step-icon">
											<i class="ri-settings-5-line"></i>
										</span>Processing<br> order
									</div>
									<div class="mn-step mn-step-active">
										<span class="mn-step-icon">
											<i class="ri-gift-line"></i>
										</span>Quality<br> check
									</div>
									<div class="mn-step">
										<span class="mn-step-icon">
											<i class="ri-truck-line"></i>
										</span>Product<br> dispatched
									</div>
									<div class="mn-step">
										<span class="mn-step-icon">
											<i class="ri-home-4-line"></i>
										</span>Product<br> delivered
									</div>
								</div>
								<div class="mn-steps-header">
									<div class="progress">
										<div class="progress-bar" role="progressbar" style="width: 50%"
											aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
