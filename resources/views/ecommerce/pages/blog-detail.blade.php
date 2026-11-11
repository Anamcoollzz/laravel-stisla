@php
  $blog = $blog ?? \App\Models\Blog::query()->active()->firstOrFail();
  $recentBlogs = $recentBlogs ?? \App\Models\Blog::query()->active()->where('id', '!=', $blog->id)->orderBy('order')->latest('published_at')->latest('id')->take(5)->get();
  $categories = $categories ?? \App\Models\Blog::query()->active()->whereNotNull('category')->where('category', '!=', '')->select('category')->selectRaw('count(*) as count')->groupBy('category')->orderBy('category')->get();
@endphp
@extends('ecommerce.layouts.app-ecommerce')

@section('title', $blog->title . ' - Blog')

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
						<h2 class="mn-breadcrumb-title">{{ $blog->title }}</h2>
					</div>
					<div class="col-md-6 col-sm-12">
						<!-- mn-breadcrumb-list start -->
						<ul class="mn-breadcrumb-list">
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
							<li class="mn-breadcrumb-item"><a href="javascript:void(0)">Blog</a></li>
							<li class="mn-breadcrumb-item active">{{ Str::limit($blog->title, 25) }}</li>
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
			<div class="mn-blogs-content">
				<div class="mn-blogs-inner">
					<div class="mn-single-blog-item">
						<div class="single-blog-info">
							<figure class="blog-img">
								<a href="javascript:void(0)"><img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="img-fluid rounded" style="width: 100%; max-height: 480px; object-fit: cover;"></a>
							</figure>
							<div class="single-blog-detail">
								<label>
									{{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }} - 
									<span class="text-primary font-weight-bold">{{ $blog->category ?? 'General' }}</span>
									@if ($blog->author_name)
										• By <span class="text-dark">{{ $blog->author_name }}</span>
									@endif
								</label>
								<h3>{{ $blog->title }}</h3>

								@if ($blog->short_description)
									<p class="mn-text-highlight">{{ $blog->short_description }}</p>
								@endif

								<div class="blog-body-content">
									{!! $blog->content !!}
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Comments Start -->
				<div class="mn-blog-comments m-t-30">
					<div class="mn-blog-cmt-preview">
						<div class="mn-blog-comment-wrapper">
							<h4 class="mn-blog-dmn-title">Comments : 02</h4>
							<div class="mn-single-comment-wrapper mt-35">
								<div class="mn-blog-user-img">
									<img src="{{ asset('mantu/assets/img/user/1.jpg') }}" alt="user image">
								</div>
								<div class="mn-blog-comment-content">
									<h5>John Deo</h5>
									<span>October 14, 2025</span>
									<p>Artikel yang sangat informatif dan bermanfaat! Tips strategi pemasarannya sangat relevan dan mudah diaplikasikan.</p>
									<div class="mn-blog-details-btn">
										<a href="javascript:void(0)">Reply</a>
									</div>
								</div>
							</div>
							<div class="mn-single-comment-wrapper sub-cmt">
								<div class="mn-blog-user-img">
									<img src="{{ asset('mantu/assets/img/user/2.jpg') }}" alt="user image">
								</div>
								<div class="mn-blog-comment-content">
									<h5>Jenifer Lowes</h5>
									<span>October 15, 2025</span>
									<p>Setuju sekali dengan poin di atas, terutama mengenai pentingnya data analytics untuk loyalitas pelanggan.</p>
									<div class="mn-blog-details-btn">
										<a href="javascript:void(0)">Reply</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="mn-blog-cmt-form">
						<div class="mn-blog-reply-wrapper mt-50">
							<h4 class="mn-blog-dmn-title">Leave A Reply</h4>
							<form class="mn-blog-form" action="javascript:void(0)">
								<div class="row">
									<div class="col-md-6">
										<div class="mn-leave-form">
											<input type="text" placeholder="Full Name" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mn-leave-form">
											<input type="email" placeholder="Email Address" required>
										</div>
									</div>
									<div class="col-md-12">
										<div class="mn-text-leave">
											<textarea placeholder="Message" required></textarea>
											<button type="submit" class="mn-btn-2 border-0"><span>Submit</span></button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- Comments End -->
			</div>
		</div>

		<!-- Sidebar Area Start -->
		<div class="mn-blogs-sidebar mn-blogs-leftside col-lg-4 col-md-12 m-t-991">
			<div class="mn-blog-search">
				<form class="mn-blog-search-form" action="javascript:void(0)">
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
						@forelse ($recentBlogs as $recent)
							<div class="mn-sidebar-block-item">
								<div class="mn-sidebar-block-img">
									<a href="{{ route('ecommerce.blogs.detail', $recent->slug) }}">
										<img src="{{ $recent->image_url }}" alt="{{ $recent->title }}" style="width: 70px; height: 60px; object-fit: cover; border-radius: 4px;">
									</a>
								</div>
								<div class="mn-sidebar-block-detial">
									<h5 class="mn-blog-title">
										<a href="{{ route('ecommerce.blogs.detail', $recent->slug) }}">{{ Str::limit($recent->title, 40) }}</a>
									</h5>
									<div class="mn-blog-date">{{ $recent->published_at ? $recent->published_at->format('F d, Y') : $recent->created_at->format('F d, Y') }}</div>
									<span class="text-primary small">- {{ $recent->category ?? 'General' }}</span>
								</div>
							</div>
						@empty
							<p class="text-muted small">No other articles available.</p>
						@endforelse
					</div>
				</div>
				<!-- Sidebar Recent Blog Block -->

				<!-- Sidebar Category Block -->
				@if ($categories->isNotEmpty())
					<div class="mn-sidebar-block">
						<div class="mn-sb-title">
							<h3 class="mn-sidebar-title">Categories</h3>
						</div>
						<div class="mn-blog-block-content mn-sidebar-dropdown">
							<ul>
								@foreach ($categories as $cat)
									<li>
										<div class="mn-sidebar-block-item d-flex justify-content-between align-items-center py-1">
											<span class="text-dark font-weight-bold">{{ $cat->category }}</span>
											<span class="badge bg-light text-muted border">{{ $cat->count }}</span>
										</div>
									</li>
								@endforeach
							</ul>
						</div>
					</div>
				@endif
				<!-- Sidebar Category Block -->
			</div>
		</div>
	</div>
</div>
@endsection

