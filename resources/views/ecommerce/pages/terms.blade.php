@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Terms - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Terms Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Terms Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Terms section -->
			<section class="mn-terms-conditions p-b-15">
				<div class="mn-title d-none">
					<h2><span>Terms</span> & Condition</h2>
					<p>Browse The Collection of Top Products</p>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="mn-common-wrapper">
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">Welcome to Mantu Store.</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged. <b>Lorem Ipsum is simply dutmmy text.</b></p>
								</div>
							</div>
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">Mantu Websites</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged. <b>Lorem Ipsum is simply dutmmy text.</b></p>
								</div>
							</div>
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">How browsing and vendor works?</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged. <b>Lorem Ipsum is simply dutmmy text.</b></p>
								</div>
							</div>
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">Becoming an vendor</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged. <b>Lorem Ipsum is simply dutmmy text.</b></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 m-t-991">
						<div class="mn-common-wrapper">
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">How browsing and vendor works?</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged. <b>Lorem Ipsum is simply dutmmy text.</b></p>
								</div>
							</div>
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">Becoming an vendor</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged.</p>
								</div>
							</div>
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">How browsing and vendor works?</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged. <b>Lorem Ipsum is simply dutmmy text.</b></p>
								</div>
							</div>
							<div class="col-sm-12 mn-cmn-block">
								<div class="mn-cmn-block-inner">
									<h5 class="mn-cmn-block-title">Becoming an vendor</h5>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
										<b>Lorem
											Ipsum is simply dutmmy text</b> ever since the 1500s, when an unknown
										printer
										took a galley of type and scrambled it to make a type specimen book. It has
										survived
										not only five centuries, but also the leap into electronic typesetting,
										remaining
										essentially unchanged.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
