@php
  $blogs = $blogs ?? \App\Models\Blog::query()->active()->orderBy('order')->latest('published_at')->latest('id')->paginate(9);
  $categories = $categories ?? \App\Models\Blog::query()->active()->whereNotNull('category')->where('category', '!=', '')->select('category')->selectRaw('count(*) as count')->groupBy('category')->orderBy('category')->get();
@endphp
@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Our Blogs - Ecommerce')

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
						<h2 class="mn-breadcrumb-title">Our Blogs</h2>
					</div>
					<div class="col-md-6 col-sm-12">
						<!-- mn-breadcrumb-list start -->
						<ul class="mn-breadcrumb-list">
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
							<li class="mn-breadcrumb-item active">Blogs</li>
						</ul>
						<!-- mn-breadcrumb-list end -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Search & Filter Bar -->
	<div class="row m-b-30">
		<div class="col-12">
			<div class="card border-0 shadow-sm" style="border-radius: 12px; background: #fafafa;">
				<div class="card-body p-3 p-md-4">
					<form action="{{ route('ecommerce.blogs.index') }}" method="GET">
						<div class="row g-2 align-items-center">
							<div class="col-md-6 col-lg-7">
								<div class="input-group">
									<input type="text" name="search" class="form-control" placeholder="Search blog articles, topics, keywords..." value="{{ request('search') }}" style="border-radius: 8px 0 0 8px; height: 46px;">
									<button class="btn btn-primary px-4" type="submit" style="border-radius: 0 8px 8px 0; height: 46px;">
										<i class="ri-search-line"></i> Search
									</button>
								</div>
							</div>
							<div class="col-md-4 col-lg-3">
								<select name="category" class="form-select form-control" onchange="this.form.submit()" style="border-radius: 8px; height: 46px;">
									<option value="">All Categories</option>
									@foreach ($categories as $cat)
										<option value="{{ $cat->category }}" {{ request('category') === $cat->category ? 'selected' : '' }}>
											{{ $cat->category }} ({{ $cat->count }})
										</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2 col-lg-2 text-md-end">
								@if (request('search') || request('category'))
									<a href="{{ route('ecommerce.blogs.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: 8px; height: 46px; display: inline-flex; align-items: center; justify-content: center;">
										<i class="ri-close-line me-1"></i> Reset
									</a>
								@endif
							</div>
						</div>
					</form>

					@if ($categories->isNotEmpty())
						<div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top align-items-center">
							<small class="text-muted me-2 font-weight-bold">Popular Topics:</small>
							<a href="{{ route('ecommerce.blogs.index', array_filter(['search' => request('search')])) }}" class="badge {{ !request('category') ? 'bg-primary text-white' : 'bg-white text-dark border' }} text-decoration-none px-3 py-2" style="border-radius: 20px;">
								All
							</a>
							@foreach ($categories as $cat)
								<a href="{{ route('ecommerce.blogs.index', array_filter(['category' => $cat->category, 'search' => request('search')])) }}" class="badge {{ request('category') === $cat->category ? 'bg-primary text-white' : 'bg-white text-dark border' }} text-decoration-none px-3 py-2" style="border-radius: 20px;">
									{{ $cat->category }} <span class="opacity-75">({{ $cat->count }})</span>
								</a>
							@endforeach
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Active Search Indicator -->
	@if (request('search') || request('category'))
		<div class="row m-b-20">
			<div class="col-12">
				<div class="alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 mb-0" style="border-radius: 8px;">
					<span>
						Showing results for
						@if (request('search'))
							keyword: <strong>"{{ request('search') }}"</strong>
						@endif
						@if (request('search') && request('category'))
							in
						@endif
						@if (request('category'))
							category: <strong>"{{ request('category') }}"</strong>
						@endif
						({{ $blogs->total() }} articles found)
					</span>
					<a href="{{ route('ecommerce.blogs.index') }}" class="text-danger small font-weight-bold text-decoration-none">
						<i class="ri-close-circle-line"></i> Clear filters
					</a>
				</div>
			</div>
		</div>
	@endif

	<div class="row m-b-30">
		<div class="mn-blogs-rightside col-md-12">
			<!-- Blog content Start -->
			<div class="mn-blogs-content mn-blog">
				<div class="mn-blogs-inner">
					<div class="row">
						@forelse ($blogs as $blog)
							<div class="col-lg-4 col-md-6 col-12 mn-blog-block m-b-24">
								<div class="mn-blog-card h-100 d-flex flex-column" style="border-radius: 10px; overflow: hidden; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
									<div class="blog-info d-flex flex-column flex-grow-1">
										<figure class="blog-img m-0">
											<a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}">
												<img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" style="width: 100%; height: 230px; object-fit: cover;">
											</a>
										</figure>
										<div class="detail p-3 d-flex flex-column flex-grow-1">
											<label class="text-muted small mb-2">
												{{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }} - 
												<a href="{{ route('ecommerce.blogs.index', ['category' => $blog->category]) }}" class="text-primary font-weight-bold">{{ $blog->category ?? 'General' }}</a>
											</label>
											<h3 class="mb-2" style="font-size: 1.15rem; line-height: 1.4;">
												<a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}">{{ $blog->title }}</a>
											</h3>
											@if ($blog->short_description)
												<p class="text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
													{{ $blog->short_description }}
												</p>
											@endif
											<div class="more-info mt-auto pt-2 border-top">
												<a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}">Read More<i class="ri-arrow-right-double-line"></i></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						@empty
							<div class="col-12 py-5 text-center">
								<div class="py-4">
									<i class="ri-article-line" style="font-size: 3.5rem; color: #ccc;"></i>
									<h4 class="mt-3 text-dark">No Articles Found</h4>
									<p class="text-muted">We couldn't find any blog posts matching your search criteria.</p>
									<a href="{{ route('ecommerce.blogs.index') }}" class="btn btn-primary px-4 mt-2" style="border-radius: 8px;">
										View All Articles
									</a>
								</div>
							</div>
						@endforelse
					</div>
				</div>

				<!-- Pagination Start -->
				@if ($blogs->total() > 0)
					<div class="mn-pro-pagination">
						<span>Showing {{ $blogs->firstItem() ?? 0 }}-{{ $blogs->lastItem() ?? 0 }} of {{ $blogs->total() }} items</span>
						@if ($blogs->hasPages())
							<ul class="mn-pro-pagination-inner">
								@if ($blogs->onFirstPage())
									<li class="disabled"><span><i class="ri-arrow-left-double-line"></i></span></li>
								@else
									<li><a href="{{ $blogs->previousPageUrl() }}"><i class="ri-arrow-left-double-line"></i></a></li>
								@endif

								@foreach ($blogs->getUrlRange(max(1, $blogs->currentPage() - 2), min($blogs->lastPage(), $blogs->currentPage() + 2)) as $page => $url)
									@if ($page == $blogs->currentPage())
										<li><a class="active" href="javascript:void(0)">{{ $page }}</a></li>
									@else
										<li><a href="{{ $url }}">{{ $page }}</a></li>
									@endif
								@endforeach

								@if ($blogs->hasMorePages())
									<li><a class="next" href="{{ $blogs->nextPageUrl() }}">Next <i class="ri-arrow-right-double-line"></i></a></li>
								@else
									<li class="disabled"><span>Next <i class="ri-arrow-right-double-line"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				@endif
				<!-- Pagination End -->
			</div>
			<!--Blog content End -->
		</div>
	</div>
</div>
@endsection
