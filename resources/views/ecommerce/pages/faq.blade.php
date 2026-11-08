@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Faq - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">FAQ Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">FAQ Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Faq section -->
			<section class="mn-faq p-b-15">
				<div class="mn-title d-none">
					<h2>frequently asked <span>questions</span></h2>
					<p>Customer service management.</p>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="mn-accordion style-1">
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									What is the multi vendor services?
								</h4>
								<div class="mn-accordion-body show">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s,
									when an
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									How to buy many products at a time?
								</h4>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever.
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									Refund policy for customer.
								</h4>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									Exchange policy for customer.
								</h4>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s,
									when an Ipsum has been the industry's
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h2 class="mn-accordion-header">
									Give a way products available.
								</h2>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s,
									when an
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s.
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 m-t-991">
						<div class="mn-accordion style-1">
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									Refund policy for customer.
								</h4>
								<div class="mn-accordion-body show">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s,
									when an
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									Exchange policy for customer.
								</h4>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s,
									when an Ipsum has been the industry's
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									How to buy many products at a time?
								</h4>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever.
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h2 class="mn-accordion-header">
									Give a way products available.
								</h2>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s,
									when an
									Ipsum has been the industry's standard <code>dummy text</code> ever since the 1500s.
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
							<div class="mn-accordion-item">
								<h4 class="mn-accordion-header">
									What is the multi vendor services?
								</h4>
								<div class="mn-accordion-body">Lorem Ipsum is simply dummy text of the printing and
									typesetting industry. Lorem
									unknown printer took a galley of type and scrambled it to make a type specimen book.
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
