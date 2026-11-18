@extends('news.layouts.app-news')

@section('title', $news->title . ' - News')

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
                  <div class="d-sm-flex justify-content-between text-center">
                     <p class="like-info"><span class="align-middle"><i class="fa fa-heart"></i></span> Share this article</p>
                     <div class="col-sm-4 text-center my-2 my-sm-0"></div>
                     <ul class="social-icons">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-dribbble"></i></a></li>
                        <li><a href="#"><i class="fab fa-behance"></i></a></li>
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
               <div class="comment-form">
                  <h4>Leave a Reply</h4>
                  <form class="form-contact comment_form" action="#" id="commentForm">
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group">
                              <textarea class="form-control w-100" name="comment" id="comment" cols="30" rows="9"
                                 placeholder="Write Comment"></textarea>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <input class="form-control" name="name" id="name" type="text" placeholder="Name">
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <input class="form-control" name="email" id="email" type="email" placeholder="Email">
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group">
                              <input class="form-control" name="website" id="website" type="text" placeholder="Website">
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
