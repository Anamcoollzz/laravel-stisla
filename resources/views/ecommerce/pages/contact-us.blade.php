@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Contact Us - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Contact us Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Contact us Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			@php
				$siteContact = $siteContact ?? \App\Models\Setting::getContactData();
				$openTicket = $openTicket ?? (auth()->check()
					? \App\Models\Ticket::query()->where('user_id', auth()->id())->where('status', '!=', 'closed')->latest('id')->first()
					: null);
			@endphp

			<!-- Contact us section -->
			<section class="mn-contact p-b-15">
				<div class="mn-title d-none">
					<h2>Get in <span>Touch</span></h2>
					<p>Please select a topic below related to you inquiry. If you don't fint what you need, fill out
						our
						contact form.</p>
				</div>
				<div class="row p-t-80">
					<div class="col-md-6 mn-contact-detail m-b-m-30">
						<div class="mn-box m-b-30">
							<div class="detail">
								<div class="icon"><i class="ri-mail-send-line"></i></div>
								<div class="info">
									<h3 class="title">Contact Mail</h3>
									<p>{{ $siteContact['email'] ?? 'hairulanam21@gmail.com' }}</p>
								</div>
							</div>
						</div>
						<div class="mn-box m-b-30">
							<div class="detail">
								<div class="icon"><i class="ri-customer-service-2-line"></i></div>
								<div class="info">
									<h3 class="title">Contact Phone</h3>
									<p>{{ $siteContact['phone'] ?? '6285322778935' }}</p>
								</div>
							</div>
						</div>
						<div class="mn-box m-b-30">
							<div class="detail">
								<div class="icon"><i class="ri-map-pin-line"></i></div>
								<div class="info">
									<h3 class="title">Address</h3>
									<p>{{ $siteContact['address'] ?? 'Jl PB Sudirman Dusun Krajan Tanggul, Jember' }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 m-t-767">
						@if (session('success'))
							<div class="alert alert-success mb-3">{{ session('success') }}</div>
						@endif
						@if ($errors->any())
							<div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
						@endif
						@if (!auth()->check())
							<div class="alert alert-warning mb-3 d-flex align-items-center justify-content-between gap-2">
								<span>Silakan login terlebih dahulu untuk mengirim pesan / membuat tiket.</span>
								<a href="{{ route('ecommerce.login') }}" class="btn btn-sm btn-primary text-white text-nowrap">Login</a>
							</div>
						@elseif ($openTicket)
							<div class="alert alert-info mb-3 d-flex align-items-center justify-content-between gap-2">
								<span>Anda masih memiliki tiket aktif <strong>#{{ $openTicket->id }}</strong>. Tutup tiket tersebut terlebih dahulu sebelum membuat tiket baru.</span>
								<a href="{{ route('ecommerce.ticket-list', ['ticket' => $openTicket->id]) }}" class="btn btn-sm btn-primary text-white text-nowrap">Lihat Tiket</a>
							</div>
						@endif
						<form action="{{ route('ecommerce.contact-us.submit') }}" method="POST">
							@csrf
							<div class="form-group">
								<input type="text" class="form-control" id="fname" placeholder="Full Name" value="{{ auth()->user()?->name }}" style="background-color: #f8f9fa; cursor: not-allowed;" disabled>
							</div>
							<div class="form-group">
								<input type="email" class="form-control" id="umail" placeholder="Email" value="{{ auth()->user()?->email }}" style="background-color: #f8f9fa; cursor: not-allowed;" disabled>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="phone" placeholder="Phone" value="{{ auth()->user()?->phone }}" style="background-color: #f8f9fa; cursor: not-allowed;" disabled>
							</div>
							<div class="form-group">
								<textarea name="message" class="form-control" id="message" rows="5"
									placeholder="Message" {{ $openTicket ? 'disabled style=background-color:#f8f9fa;cursor:not-allowed;' : (auth()->check() ? 'required' : '') }}>{{ old('message') }}</textarea>
							</div>
							<button type="submit" class="mn-btn-2" {{ $openTicket ? 'disabled style=opacity:0.6;cursor:not-allowed;' : '' }}><span>Submit</span></button>
						</form>
					</div>
				</div>
			</section>
		</div>
@endsection
