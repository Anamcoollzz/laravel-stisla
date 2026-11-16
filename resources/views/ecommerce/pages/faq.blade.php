@extends('ecommerce.layouts.app-ecommerce')

@section('title', __('FAQ - Ecommerce'))

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
								<h2 class="mn-breadcrumb-title">{{ __('FAQ Page') }}</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">{{ __('Home') }}</a></li>
									<li class="mn-breadcrumb-item active">{{ __('FAQ Page') }}</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			@php
			  $faqs = $faqs ?? \App\Models\EcommerceFaq::query()->active()->orderBy('order')->latest('id')->get();
			  $leftFaqs = $leftFaqs ?? $faqs->where('column', 'left')->values();
			  $rightFaqs = $rightFaqs ?? $faqs->where('column', 'right')->values();
			@endphp

			<!-- Faq section -->
			<section class="mn-faq p-b-15">
				<div class="mn-title d-none">
					<h2>{{ __('frequently asked') }} <span>{{ __('questions') }}</span></h2>
					<p>{{ __('Customer service management.') }}</p>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="mn-accordion style-1">
							@forelse ($leftFaqs as $faq)
								<div class="mn-accordion-item">
									<h4 class="mn-accordion-header">
										{{ $faq->question }}
									</h4>
									<div class="mn-accordion-body {{ $loop->first ? 'show' : '' }}">
										{!! nl2br(e($faq->answer)) !!}
									</div>
								</div>
							@empty
								<div class="mn-accordion-item">
									<h4 class="mn-accordion-header">{{ __('No FAQs available yet.') }}</h4>
									<div class="mn-accordion-body show">{{ __('Please check back later.') }}</div>
								</div>
							@endforelse
						</div>
					</div>
					<div class="col-lg-6 m-t-991">
						<div class="mn-accordion style-1">
							@foreach ($rightFaqs as $faq)
								<div class="mn-accordion-item">
									<h4 class="mn-accordion-header">
										{{ $faq->question }}
									</h4>
									<div class="mn-accordion-body {{ $loop->first ? 'show' : '' }}">
										{!! nl2br(e($faq->answer)) !!}
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
