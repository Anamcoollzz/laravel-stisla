@php
  $blog = $blog ?? \App\Models\Blog::query()->active()->firstOrFail();
  $recentBlogs = $recentBlogs ?? \App\Models\Blog::query()->active()->where('id', '!=', $blog->id)->orderBy('order')->latest('published_at')->latest('id')->take(5)->get();
  $categories = $categories ?? \App\Models\Blog::query()->active()->whereNotNull('category')->where('category', '!=', '')->select('category')->selectRaw('count(*) as count')->groupBy('category')->orderBy('category')->get();
  $comments = $comments ?? $blog->topLevelComments()->get();
  $totalComments = $totalComments ?? $blog->approvedComments()->count();
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
				<div class="mn-blog-comments m-t-30" id="comments">
					@if (session('comment_success'))
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<strong><i class="ri-checkbox-circle-line me-1"></i> {{ session('comment_success') }}</strong>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					@endif

					<div class="mn-blog-cmt-preview">
						<div class="mn-blog-comment-wrapper">
							<h4 class="mn-blog-dmn-title">Comments : {{ str_pad($totalComments, 2, '0', STR_PAD_LEFT) }}</h4>
							@forelse ($comments as $comment)
								<div class="mn-single-comment-wrapper mt-35" id="comment-{{ $comment->id }}">
									<div class="mn-blog-user-img">
										<img src="{{ $comment->avatar_url }}" alt="{{ $comment->name }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
									</div>
									<div class="mn-blog-comment-content">
										<h5>{{ $comment->name }}</h5>
										<span>{{ $comment->created_at->format('F d, Y') }}</span>
										<p>{{ $comment->comment }}</p>
										<div class="mn-blog-details-btn">
											<a href="#comment-form" onclick="setReply({{ $comment->id }}, '{{ addslashes($comment->name) }}')">Reply</a>
										</div>
									</div>
								</div>

								@foreach ($comment->replies as $reply)
									<div class="mn-single-comment-wrapper sub-cmt" id="comment-{{ $reply->id }}">
										<div class="mn-blog-user-img">
											<img src="{{ $reply->avatar_url }}" alt="{{ $reply->name }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
										</div>
										<div class="mn-blog-comment-content">
											<h5>{{ $reply->name }} <span class="badge bg-light text-muted small border ms-1">Reply</span></h5>
											<span>{{ $reply->created_at->format('F d, Y') }}</span>
											<p>{{ $reply->comment }}</p>
											<div class="mn-blog-details-btn">
												<a href="#comment-form" onclick="setReply({{ $comment->id }}, '{{ addslashes($reply->name) }}')">Reply</a>
											</div>
										</div>
									</div>
								@endforeach
							@empty
								<p class="text-muted py-3">Belum ada komentar untuk artikel ini. Jadilah yang pertama berkomentar!</p>
							@endforelse
						</div>
					</div>

					<div class="mn-blog-cmt-form" id="comment-form">
						<div class="mn-blog-reply-wrapper mt-50">
							<h4 class="mn-blog-dmn-title" id="reply-title">Leave A Reply</h4>
							<div id="reply-alert" class="alert alert-info py-2 px-3 d-none align-items-center justify-content-between mb-3" style="border-radius: 6px;">
								<span>Membalas komentar <strong id="reply-to-name"></strong></span>
								<button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="cancelReply()">Batal Balas</button>
							</div>

							<form class="mn-blog-form" action="{{ route('ecommerce.blogs.comment.store', $blog->slug) }}" method="POST">
								@csrf
								<input type="hidden" name="parent_id" id="comment-parent-id" value="{{ old('parent_id') }}">

								<div class="row">
									<div class="col-md-6">
										<div class="mn-leave-form">
											<input type="text" name="name" placeholder="Full Name" value="{{ old('name', auth()->user()?->name) }}" required>
										</div>
										@error('name')
											<small class="text-danger d-block mt-1">{{ $message }}</small>
										@enderror
									</div>
									<div class="col-md-6">
										<div class="mn-leave-form">
											<input type="email" name="email" placeholder="Email Address" value="{{ old('email', auth()->user()?->email) }}" required>
										</div>
										@error('email')
											<small class="text-danger d-block mt-1">{{ $message }}</small>
										@enderror
									</div>
									<div class="col-md-12">
										<div class="mn-text-leave">
											<textarea name="comment" placeholder="Your Message / Comment" required>{{ old('comment') }}</textarea>
											<button type="submit" class="mn-btn-2 border-0"><span>Submit Comment</span></button>
										</div>
										@error('comment')
											<small class="text-danger d-block mt-1">{{ $message }}</small>
										@enderror
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
				<form class="mn-blog-search-form" action="{{ route('ecommerce.blogs.index') }}" method="GET">
					<input class="form-control" name="search" placeholder="Search Our Blog" type="text" value="{{ request('search') }}">
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
											<a href="{{ route('ecommerce.blogs.index', ['category' => $cat->category]) }}" class="text-dark font-weight-bold">{{ $cat->category }}</a>
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
<script>
function setReply(parentId, authorName) {
    document.getElementById('comment-parent-id').value = parentId;
    document.getElementById('reply-to-name').innerText = authorName;
    document.getElementById('reply-alert').classList.remove('d-none');
    document.getElementById('reply-alert').classList.add('d-flex');
    document.getElementById('reply-title').innerText = 'Reply to ' + authorName;
    const form = document.getElementById('comment-form');
    if (form) form.scrollIntoView({ behavior: 'smooth' });
}

function cancelReply() {
    document.getElementById('comment-parent-id').value = '';
    document.getElementById('reply-alert').classList.add('d-none');
    document.getElementById('reply-alert').classList.remove('d-flex');
    document.getElementById('reply-title').innerText = 'Leave A Reply';
}
</script>
@endsection

