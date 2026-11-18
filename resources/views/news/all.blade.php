@extends('news.layouts.app-news')

@section('title', 'All News - News HTML-5 Template')

@section('content')
<!--================Blog Area =================-->
    <section class="blog_area section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-5 mb-lg-0">
                    <div class="blog_left_sidebar">
                        @forelse ($newsList as $item)
                            @php
                                $date = $item->published_at ?? $item->created_at;
                            @endphp
                            <article class="blog_item">
                                <div class="blog_item_img">
                                    <img class="card-img rounded-0" src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}">
                                    @if ($date)
                                        <a href="{{ route('news.blog_details') }}" class="blog_item_date">
                                            <h3>{{ $date->format('d') }}</h3>
                                            <p>{{ $date->format('M') }}</p>
                                        </a>
                                    @endif
                                </div>

                                <div class="blog_details">
                                    <a class="d-inline-block" href="{{ route('news.blog_details') }}">
                                        <h2>{{ $item->title }}</h2>
                                    </a>
                                    <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 180) }}</p>
                                    <ul class="blog-info-link">
                                        @if ($item->category)
                                            <li><a href="{{ route('news.all', ['category' => $item->category->id]) }}"><i class="fa fa-tag"></i> {{ $item->category->name }}</a></li>
                                        @endif
                                        @foreach ($item->tags as $tag)
                                            <li><a href="{{ route('news.all', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a></li>
                                        @endforeach
                                        <li><a href="#"><i class="fa fa-user"></i> {{ $item->publisher?->name ?? 'Admin' }}</a></li>
                                    </ul>
                                </div>
                            </article>
                        @empty
                            <div class="alert alert-light border p-4 text-center">
                                <h4 class="mb-2">{{ __('Tidak ada berita yang ditemukan.') }}</h4>
                                <p class="text-muted mb-3">{{ __('Coba ubah kata kunci pencarian atau pilih kategori lain.') }}</p>
                                <a href="{{ route('news.all') }}" class="button rounded-0 primary-bg text-white btn_1 boxed-btn">{{ __('Lihat Semua Berita') }}</a>
                            </div>
                        @endforelse

                        @if ($newsList->hasPages())
                            <nav class="blog-pagination justify-content-center d-flex">
                                {{ $newsList->links('pagination::bootstrap-4') }}
                            </nav>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog_right_sidebar">
                        <aside class="single_sidebar_widget search_widget">
                            <form action="{{ route('news.all') }}" method="GET">
                                @if ($selectedCategory !== '')
                                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                                @endif
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <input type="text" name="search" class="form-control" placeholder='Search Keyword'
                                            value="{{ $search }}"
                                            onfocus="this.placeholder = ''"
                                            onblur="this.placeholder = 'Search Keyword'">
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
                                    <a href="{{ route('news.all', array_filter(['search' => $search])) }}" class="d-flex {{ $selectedCategory === '' ? 'font-weight-bold text-danger' : '' }}">
                                        <p>All Categories</p>
                                    </a>
                                </li>
                                @foreach ($categories as $category)
                                    <li>
                                        <a href="{{ route('news.all', array_filter(['category' => $category->id, 'search' => $search])) }}" class="d-flex {{ (string) $selectedCategory === (string) $category->id ? 'font-weight-bold text-danger' : '' }}">
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
                                        <a href="{{ route('news.blog_details') }}">
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
                                        <a href="{{ route('news.all', array_filter(['tag' => $tag->slug, 'category' => $selectedCategory, 'search' => $search])) }}"
                                           class="{{ $selectedTag === $tag->slug ? 'bg-danger text-white border-danger' : '' }}">
                                            {{ $tag->name }}
                                        </a>
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
    <!--================Blog Area =================-->
@endsection
