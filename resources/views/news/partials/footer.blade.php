<footer>
    <!-- Footer Start-->
    <div class="footer-main footer-bg">
        <div class="footer-area footer-padding">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-8">
                        <div class="single-footer-caption mb-50">
                            <div class="single-footer-caption mb-30">
                                <!-- logo -->
                                <div class="footer-logo">
                                    <a href="{{ route('news.index') }}"><img src="{{ asset('news-assets/img/logo/logo2_footer.png') }}" alt=""></a>
                                </div>
                                <div class="footer-tittle">
                                    <div class="footer-pera">
                                        <p class="info1">Lorem ipsum dolor sit amet, nsectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
                                        <p class="info2">198 West 21th Street, Suite 721 New York,NY 10010</p>
                                        <p class="info2">Phone: +95 (0) 123 456 789 Cell: +95 (0) 123 456 789</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-5 col-sm-7">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Popular post</h4>
                            </div>
                            @forelse ($popularNewsPosts ?? collect() as $popularPost)
                                <!-- Popular post -->
                                <div class="whats-right-single mb-20">
                                    <div class="whats-right-img">
                                        <a href="{{ route('news.show', $popularPost->slug) }}">
                                            <img src="{{ $popularPost->thumbnail_mini_url }}" alt="{{ $popularPost->title }}" style="width: 86px; height: 70px; object-fit: cover; border-radius: 4px;">
                                        </a>
                                    </div>
                                    <div class="whats-right-cap">
                                        <h4><a href="{{ route('news.show', $popularPost->slug) }}">{{ \Illuminate\Support\Str::limit($popularPost->title, 50) }}</a></h4>
                                        <p>{{ $popularPost->publisher?->name ?? 'Admin' }}  |  {{ ($popularPost->published_at ?? $popularPost->created_at)?->diffForHumans() ?? '-' }}  |  {{ number_format($popularPost->views_count) }} {{ __('views') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="info1">{{ __('Belum ada berita populer.') }}</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-7">
                        <div class="single-footer-caption mb-50">
                            <div class="banner">
                                <img src="{{ asset('news-assets/img/gallery/body_card4.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer-bottom aera -->
        <div class="footer-bottom-area footer-bg">
            <div class="container">
                <div class="footer-border">
                    <div class="row d-flex align-items-center">
                        <div class="col-xl-12">
                            <div class="footer-copy-right text-center">
                                <p>
                                    Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End-->
</footer>
