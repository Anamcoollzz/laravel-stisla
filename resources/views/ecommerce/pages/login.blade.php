@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Login - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">Login Page</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Login Page</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Login section -->
			<section class="mn-login p-b-15">
				<div class="mn-title d-none">
					<h2>Login<span></span></h2>
					<p>Get access to your Orders, Wishlist and Recommendations.</p>
				</div>
				<div class="mn-login-content">
					<div class="mn-login-box">
						<div class="mn-login-wrapper">
							<div class="mn-login-container">
								<div class="mn-login-form">
									<form action="{{ route('ecommerce.login.submit') }}" method="POST">
										@csrf

										@if (session('status'))
											<div class="alert alert-info py-2 px-3 mb-3" style="font-size: 14px;">
												{{ session('status') }}
											</div>
										@endif

										@if (session('success'))
											<div class="alert alert-success py-2 px-3 mb-3" style="font-size: 14px;">
												{{ session('success') }}
											</div>
										@endif

										@if ($errors->any())
											<div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 14px;">
												{{ $errors->first() }}
											</div>
										@endif

										<span class="mn-login-wrap">
											<label>Email Address*</label>
											<input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address..."
												required autofocus>
										</span>
										<span class="mn-login-wrap">
											<label>Password*</label>
											<input type="password" name="password" placeholder="Enter your password"
												required>
										</span>
										<span class="mn-login-wrap mn-login-fp">
											<span class="mn-remember">
												<input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
												Remember
												<span class="checked"></span>
											</span>
											<label><a href="{{ route('password.request') }}">Forgot Password?</a></label>
										</span>
										<span class="mn-login-wrap mn-login-btn">
											<span><a href="{{ route('ecommerce.register') }}" class="">Create Account?</a></span>
											<button class="mn-btn-1 btn" type="submit"><span>Login</span></button>
										</span>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="mn-login-box d-n-991">
						<div class="mn-login-img">
							<img src="{{ asset('mantu/assets/img/common/about-3.png') }}" alt="login">
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
