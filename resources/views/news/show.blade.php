@extends('news.layouts.app-news')

@php
    $seoDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $news->content))), 160);
    $seoKeywords = collect([$news->category?->name])->merge($news->tags->pluck('name'))->filter()->implode(', ');
    $articleUrl = route('news.show', $news->slug);
    $encodedUrl = urlencode($articleUrl);
    $encodedTitle = urlencode($news->title);
@endphp

@section('title', $news->title . ' - ' . config('app.name', 'News'))
@section('meta_description', $seoDescription)
@section('meta_keywords', $seoKeywords ?: 'news, berita, artikel')
@section('meta_author', $news->publisher?->name ?? config('app.name', 'Admin'))
@section('canonical_url', $articleUrl)
@section('og_type', 'article')
@section('og_image', $news->thumbnail_url)

@push('meta')
    @if ($news->published_at ?? $news->created_at)
        <meta property="article:published_time" content="{{ ($news->published_at ?? $news->created_at)->toIso8601String() }}">
    @endif
    @if ($news->category)
        <meta property="article:section" content="{{ $news->category->name }}">
    @endif
    @foreach ($news->tags as $metaTag)
        <meta property="article:tag" content="{{ $metaTag->name }}">
    @endforeach
@endpush

@section('content')
<!--================Blog Area =================-->
   <section class="blog_area single-post-area section-padding">
      <div class="container">
         <div class="row">
            <div class="col-lg-8 posts-list">
               <div class="single-post">
                  <div class="feature-img">
                     <img class="img-fluid w-100" src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}">
                  </div>
                  <div class="blog_details">
                     <h2>{{ $news->title }}</h2>
                     <ul class="blog-info-link mt-3 mb-4">
                        @if ($news->category)
                           <li><a href="{{ route('news.all', ['category' => $news->category->id]) }}"><i class="fa fa-tag"></i> {{ $news->category->name }}</a></li>
                        @endif
                        @foreach ($news->tags as $tag)
                           <li><a href="{{ route('news.all', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a></li>
                        @endforeach
                        <li><a href="#"><i class="fa fa-user"></i> {{ $news->publisher?->name ?? 'Admin' }}</a></li>
                        <li><a href="#"><i class="fa fa-calendar"></i> {{ ($news->published_at ?? $news->created_at)?->format('d M Y') }}</a></li>
                     </ul>
                     <div class="news-article-content" style="line-height: 1.8;">
                        {!! nl2br(e((string) $news->content)) !!}
                     </div>
                  </div>
               </div>
               <div class="navigation-top">
                  <div class="d-sm-flex justify-content-between text-center align-items-center">
                     <p class="like-info mb-0"><span class="align-middle"><i class="fa fa-share-alt"></i></span> Share this article</p>
                     <div class="col-sm-4 text-center my-2 my-sm-0"></div>
                     <ul class="social-icons">
                        <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" title="Share on X / Twitter"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="https://wa.me/?text={{ $encodedTitle }}%20{{ $encodedUrl }}" target="_blank" rel="noopener noreferrer" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a></li>
                        <li><a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a></li>
                        <li><a href="javascript:void(0)" class="btn-copy-news-link" data-url="{{ $articleUrl }}" title="Copy Link"><i class="fa fa-link"></i></a></li>
                     </ul>
                  </div>
                  <div class="navigation-area">
                     <div class="row">
                        <div class="col-lg-6 col-md-6 col-12 nav-left flex-row d-flex justify-content-start align-items-center">
                           @if ($prevPost)
                              <div class="thumb">
                                 <a href="{{ route('news.show', $prevPost->slug) }}">
                                    <img class="img-fluid" src="{{ $prevPost->thumbnail_mini_url }}" alt="{{ $prevPost->title }}" style="width: 60px; height: 60px; object-fit: cover;">
                                 </a>
                              </div>
                              <div class="arrow">
                                 <a href="{{ route('news.show', $prevPost->slug) }}">
                                    <span class="lnr text-white ti-arrow-left"></span>
                                 </a>
                              </div>
                              <div class="detials">
                                 <p>Prev Post</p>
                                 <a href="{{ route('news.show', $prevPost->slug) }}">
                                    <h4>{{ \Illuminate\Support\Str::limit($prevPost->title, 35) }}</h4>
                                 </a>
                              </div>
                           @endif
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 nav-right flex-row d-flex justify-content-end align-items-center">
                           @if ($nextPost)
                              <div class="detials">
                                 <p>Next Post</p>
                                 <a href="{{ route('news.show', $nextPost->slug) }}">
                                    <h4>{{ \Illuminate\Support\Str::limit($nextPost->title, 35) }}</h4>
                                 </a>
                              </div>
                              <div class="arrow">
                                 <a href="{{ route('news.show', $nextPost->slug) }}">
                                    <span class="lnr text-white ti-arrow-right"></span>
                                 </a>
                              </div>
                              <div class="thumb">
                                 <a href="{{ route('news.show', $nextPost->slug) }}">
                                    <img class="img-fluid" src="{{ $nextPost->thumbnail_mini_url }}" alt="{{ $nextPost->title }}" style="width: 60px; height: 60px; object-fit: cover;">
                                 </a>
                              </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="blog-author">
                  <div class="media align-items-center">
                     <img src="{{ $news->publisher?->avatar_url ?? asset('news-assets/img/blog/author.png') }}" alt="{{ $news->publisher?->name ?? 'Admin' }}" style="width: 90px; height: 90px; object-fit: cover; border-radius: 50%;">
                     <div class="media-body">
                        <a href="#">
                           <h4>{{ $news->publisher?->name ?? 'Admin' }}</h4>
                        </a>
                        <p>{{ $news->category?->description ?? 'Penulis dan kontributor berita terkini.' }}</p>
                     </div>
                  </div>
               </div>
               <div class="comments-area" id="commentsArea">
                  <h4>{{ $news->approvedTopLevelComments->count() }} Comments</h4>
                  @forelse ($news->approvedTopLevelComments as $comment)
                     <div class="comment-list">
                        <div class="single-comment justify-content-between d-flex">
                           <div class="user justify-content-between d-flex w-100">
                              <div class="thumb">
                                 <img src="{{ $comment->user?->avatar_url ?? asset('news-assets/img/comment/comment_1.png') }}" alt="{{ $comment->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;">
                              </div>
                              <div class="desc w-100">
                                 <p class="comment">{{ $comment->comment }}</p>
                                 <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                       <h5><a href="{{ $comment->website ?: '#' }}">{{ $comment->name }}</a></h5>
                                       <p class="date">{{ $comment->created_at?->format('F d, Y \a\t g:i a') }}</p>
                                    </div>
                                    <div class="reply-btn">
                                       <a href="#commentForm" class="btn-reply text-uppercase btn-reply-comment" data-id="{{ $comment->id }}" data-name="{{ $comment->name }}">reply</a>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @foreach ($comment->replies as $reply)
                           <div class="single-comment justify-content-between d-flex mt-3 ml-5 pl-3 border-left">
                              <div class="user justify-content-between d-flex w-100">
                                 <div class="thumb">
                                    <img src="{{ $reply->user?->avatar_url ?? asset('news-assets/img/comment/comment_2.png') }}" alt="{{ $reply->name }}" style="width: 55px; height: 55px; object-fit: cover; border-radius: 50%;">
                                 </div>
                                 <div class="desc w-100">
                                    <p class="comment">{{ $reply->comment }}</p>
                                    <div class="d-flex justify-content-between">
                                       <div class="d-flex align-items-center">
                                          <h5><a href="#">{{ $reply->name }}</a></h5>
                                          <p class="date">{{ $reply->created_at?->format('F d, Y \a\t g:i a') }}</p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        @endforeach
                     </div>
                  @empty
                     <p class="text-muted">{{ __('Belum ada komentar. Jadilah yang pertama berkomentar!') }}</p>
                  @endforelse
               </div>
               <div class="comment-form">
                  <h4>Leave a Reply</h4>
                  @if (session('comment_success'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('comment_success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                     </div>
                  @endif
                  @if ($errors->any())
                     <div class="alert alert-danger">
                        <ul class="mb-0 pl-3">
                           @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                  @endif
                  <div id="replyingToBanner" class="alert alert-info py-2 px-3 mb-3 justify-content-between align-items-center" style="display: none;">
                     <span>Membalas komentar: <strong id="replyingToName"></strong></span>
                     <button type="button" class="btn btn-sm btn-link text-danger p-0" id="btnCancelReply">Batal</button>
                  </div>
                  <form class="form-contact comment_form" action="{{ route('news.comment.store', $news->slug) }}" method="POST" id="commentForm">
                     @csrf
                     <input type="hidden" name="parent_id" id="commentParentId" value="{{ old('parent_id') }}">
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group">
                              <textarea class="form-control w-100 @error('comment') is-invalid @enderror" name="comment" id="comment" cols="30" rows="9"
                                 placeholder="Write Comment" required>{{ old('comment') }}</textarea>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <input class="form-control @error('name') is-invalid @enderror" name="name" id="name" type="text" placeholder="Name" value="{{ old('name', auth()->user()?->name) }}" required>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <input class="form-control @error('email') is-invalid @enderror" name="email" id="email" type="email" placeholder="Email" value="{{ old('email', auth()->user()?->email) }}" required>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group">
                              <input class="form-control @error('website') is-invalid @enderror" name="website" id="website" type="text" placeholder="Website (Optional)" value="{{ old('website') }}">
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group">
                              <div class="d-flex align-items-center mb-2">
                                 <span class="captcha-img mr-2">{!! captcha_img('flat') !!}</span>
                                 <button type="button" class="btn btn-outline-secondary btn-sm btn-refresh-captcha" title="Refresh Captcha">
                                    <i class="fa fa-sync-alt"></i>
                                 </button>
                              </div>
                              <input class="form-control @error('captcha') is-invalid @enderror" name="captcha" id="captcha" type="text" placeholder="Masukkan kode captcha" required>
                           </div>
                        </div>
                     </div>
                     <div class="form-group">
                        <button type="submit" class="button button-contactForm btn_1 boxed-btn">Send Message</button>
                     </div>
                  </form>
               </div>
            </div>
            <div class="col-lg-4">
               <div class="blog_right_sidebar">
                  <aside class="single_sidebar_widget search_widget">
                     <form action="{{ route('news.all') }}" method="GET">
                        <div class="form-group">
                           <div class="input-group mb-3">
                              <input type="text" name="search" class="form-control" placeholder='Search Keyword'
                                 onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search Keyword'">
                              <div class="input-group-append">
                                 <button class="btns" type="submit"><i class="ti-search"></i></button>
                              </div>
                           </div>
                        </div>
                        <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn"
                           type="submit">Search</button>
                     </form>
                  </aside>
                  <aside class="single_sidebar_widget post_category_widget">
                     <h4 class="widget_title">Category</h4>
                     <ul class="list cat-list">
                        <li>
                           <a href="{{ route('news.all') }}" class="d-flex">
                              <p>All Categories</p>
                           </a>
                        </li>
                        @foreach ($categories as $category)
                           <li>
                              <a href="{{ route('news.all', ['category' => $category->id]) }}" class="d-flex">
                                 <p>{{ $category->name }}</p>
                                 <p>({{ $category->news_count }})</p>
                              </a>
                           </li>
                        @endforeach
                     </ul>
                  </aside>
                  <aside class="single_sidebar_widget popular_post_widget">
                     <h3 class="widget_title">Recent Post</h3>
                     @forelse ($recentPosts as $recent)
                        <div class="media post_item">
                           <img src="{{ $recent->thumbnail_mini_url }}" alt="{{ $recent->title }}" style="width: 80px; height: 80px; object-fit: cover;">
                           <div class="media-body">
                              <a href="{{ route('news.show', $recent->slug) }}">
                                 <h3>{{ \Illuminate\Support\Str::limit($recent->title, 42) }}</h3>
                              </a>
                              <p>{{ ($recent->published_at ?? $recent->created_at)?->format('F d, Y') }}</p>
                           </div>
                        </div>
                     @empty
                        <p class="text-muted small mb-0">{{ __('Belum ada berita terbaru.') }}</p>
                     @endforelse
                  </aside>
                  <aside class="single_sidebar_widget tag_cloud_widget">
                     <h4 class="widget_title">Tag Clouds</h4>
                     <ul class="list">
                        @forelse ($tags as $tag)
                           <li>
                              <a href="{{ route('news.all', ['tag' => $tag->slug]) }}">{{ $tag->name }}</a>
                           </li>
                        @empty
                           <li><a href="#">News</a></li>
                        @endforelse
                     </ul>
                  </aside>
                  <aside class="single_sidebar_widget instagram_feeds">
                     <h4 class="widget_title">Instagram Feeds</h4>
                     <ul class="instagram_row flex-wrap">
                        <li><a href="#"><img class="img-fluid" src="{{ asset('news-assets/img/post/post_5.png') }}" alt=""></a></li>
                        <li><a href="#"><img class="img-fluid" src="{{ asset('news-assets/img/post/post_6.png') }}" alt=""></a></li>
                        <li><a href="#"><img class="img-fluid" src="{{ asset('news-assets/img/post/post_7.png') }}" alt=""></a></li>
                        <li><a href="#"><img class="img-fluid" src="{{ asset('news-assets/img/post/post_8.png') }}" alt=""></a></li>
                        <li><a href="#"><img class="img-fluid" src="{{ asset('news-assets/img/post/post_9.png') }}" alt=""></a></li>
                        <li><a href="#"><img class="img-fluid" src="{{ asset('news-assets/img/post/post_10.png') }}" alt=""></a></li>
                     </ul>
                  </aside>
                  <aside class="single_sidebar_widget newsletter_widget">
                     <h4 class="widget_title">Newsletter</h4>
                     <form action="#">
                        <div class="form-group">
                           <input type="email" class="form-control" onfocus="this.placeholder = ''"
                              onblur="this.placeholder = 'Enter email'" placeholder='Enter email' required>
                        </div>
                        <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn"
                           type="submit">Subscribe</button>
                     </form>
                  </aside>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!--================ Blog Area end =================-->
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-copy-news-link').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var url = this.dataset.url;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url);
            } else {
                var temp = document.createElement('input');
                temp.value = url;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
            }
            var icon = this.querySelector('i');
            if (icon) {
                icon.className = 'fa fa-check text-success';
                setTimeout(function () {
                    icon.className = 'fa fa-link';
                }, 1800);
            }
        });
    });

    document.querySelectorAll('.btn-refresh-captcha').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var img = this.closest('.form-group').querySelector('.captcha-img img');
            if (img) {
                img.src = img.src.split('?')[0] + '?' + Date.now();
            }
        });
    });

    document.querySelectorAll('.btn-reply-comment').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var parentInput = document.getElementById('commentParentId');
            var banner = document.getElementById('replyingToBanner');
            var nameEl = document.getElementById('replyingToName');
            if (parentInput && banner && nameEl) {
                parentInput.value = this.dataset.id;
                nameEl.textContent = this.dataset.name;
                banner.style.display = 'flex';
            }
        });
    });

    var cancelReply = document.getElementById('btnCancelReply');
    if (cancelReply) {
        cancelReply.addEventListener('click', function () {
            document.getElementById('commentParentId').value = '';
            document.getElementById('replyingToBanner').style.display = 'none';
        });
    }
});
</script>
@endpush
