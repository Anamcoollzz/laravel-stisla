@extends('ecommerce.layouts.app-ecommerce')

@section('title', __('Register - Ecommerce'))

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
								<h2 class="mn-breadcrumb-title">{{ __('Register Page') }}</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">{{ __('Home') }}</a></li>
									<li class="mn-breadcrumb-item active">{{ __('Register Page') }}</li>
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Register section -->
			<section class="mn-register p-b-15">
				<div class="mn-title d-none">
					<h2>{{ __('Register') }}<span></span></h2>
					<p>{{ __('Best place to buy and sell digital products.') }}</p>
				</div>
				<div class="row">
					<div class="mn-register-wrapper">
						<div class="mn-register-container">
							<div class="mn-register-form">
								<form action="{{ route('ecommerce.register.submit') }}" method="POST">
									@csrf

									@if ($errors->any())
										<div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 14px;">
											<ul class="mb-0 ps-3">
												@foreach ($errors->all() as $error)
													<li>{{ $error }}</li>
												@endforeach
											</ul>
										</div>
									@endif

									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('First Name') }}*</label>
										<input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('Enter your first name') }}"
											required autofocus>
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('Last Name') }}</label>
										<input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('Enter your last name') }}">
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('Email') }}*</label>
										<input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter your email address...') }}" required>
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('Phone Number') }}</label>
										<input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('Enter your phone number') }}">
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('Password') }}*</label>
										<input type="password" name="password" placeholder="{{ __('Enter password (min 8 characters)') }}" required>
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('Confirm Password') }}*</label>
										<input type="password" name="password_confirmation" placeholder="{{ __('Confirm your password') }}" required>
									</span>
									<span class="mn-register-wrap">
										<label>{{ __('Address') }}</label>
										<input type="text" name="address" value="{{ old('address') }}" placeholder="{{ __('Address Line 1') }}">
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('City') }}</label>
										<span class="mn-rg-select-inner">
											<select name="city" id="mn-select-city"
												class="mn-register-select">
												<option value="" selected disabled>{{ __('Select City') }}</option>
												<option value="Jakarta" {{ old('city') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
												<option value="Bandung" {{ old('city') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
												<option value="Surabaya" {{ old('city') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
												<option value="Semarang" {{ old('city') == 'Semarang' ? 'selected' : '' }}>Semarang</option>
												<option value="Yogyakarta" {{ old('city') == 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
											</select>
										</span>
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('Region State') }}</label>
										<span class="mn-rg-select-inner">
											<select name="state" id="mn-select-state"
												class="mn-register-select">
												<option value="" selected disabled>{{ __('Region/State') }}</option>
												<option value="DKI Jakarta" {{ old('state') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
												<option value="Jawa Barat" {{ old('state') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
												<option value="Jawa Tengah" {{ old('state') == 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
												<option value="Jawa Timur" {{ old('state') == 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
												<option value="DI Yogyakarta" {{ old('state') == 'DI Yogyakarta' ? 'selected' : '' }}>DI Yogyakarta</option>
											</select>
										</span>
									</span>
									<span class="mn-register-wrap mn-register-half">
										<label>{{ __('Post Code') }}</label>
										<input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="{{ __('Post Code') }}">
									</span>
									<span class="mn-register-wrap mn-register-btn">
										<span>{{ __('Have an account?') }} <a href="{{ route('ecommerce.login') }}">{{ __('Login') }}</a></span>
										<button class="mn-btn-1" type="submit"><span>{{ __('Register') }}</span></button>
									</span>
								</form>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
@endsection
