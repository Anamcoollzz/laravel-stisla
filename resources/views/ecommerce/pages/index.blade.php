@php
  $heroBanners = $heroBanners ?? \App\Models\ProductBanner::query()->active()->hero()->with('product')->orderBy('order')->latest('id')->get();
  $featuredCategories = $featuredCategories ?? \App\Models\ProductCategory::query()
      ->where('is_active', true)
      ->whereHas('products', fn ($q) => $q->active())
      ->with('parent')
      ->withCount(['products' => fn ($q) => $q->active()])
      ->withMax(['products' => fn ($q) => $q->active()], 'discount_percent')
      ->with(['products' => fn ($q) => $q->active()->latest('id')])
      ->orderByDesc('products_count')
      ->take(8)
      ->get()
      ->each(fn ($cat) => $cat->setRelation('products', $cat->products->take(3)));
  $blogs = $blogs ?? \App\Models\Blog::query()->active()->orderBy('order')->latest('published_at')->latest('id')->get();
  $testimonials = $testimonials ?? \App\Models\Testimonial::query()->active()->orderBy('order')->latest('id')->get();
  $dealProducts = $dealProducts ?? \App\Models\Product::query()->active()->deal()->with(['category', 'brand', 'size', 'color'])->latest('id')->take(12)->get();
  $storeServices = $storeServices ?? \App\Models\StoreService::query()->active()->orderBy('order')->latest('id')->get();
  $instaProducts = $instaProducts ?? \App\Models\Product::query()->active()->inRandomOrder()->take(8)->get();
@endphp
@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Ecommerce')

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
  <div class="mn-main-content">
    <div class="row">
      <div class="col-xxl-12">
        <!-- Hero Section -->
        <section class="mn-hero swiper-container m-b-15">
          <div class="mn-hero-slider owl-carousel">
            @forelse ($heroBanners as $banner)
              <div class="mn-hero-slide swiper-slide slide-{{ ($loop->index % 4) + 1 }}" style="background-image: linear-gradient(90deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.15) 60%, transparent 100%), url('{{ $banner->image_url }}'); background-size: cover; background-position: center;">
                <div class="mn-hero-detail">
                  @if ($banner->badge_label)
                    <p class="label"><span>{!! nl2br(e(preg_replace('/\s+/', "\n", trim($banner->badge_label), 1))) !!}</span></p>
                  @endif
                  @if ($loop->first)
                    <h1>{!! $banner->title !!}</h1>
                  @else
                    <h2>{!! $banner->title !!}</h2>
                  @endif
                  @if ($banner->subtitle)
                    <p>{{ $banner->subtitle }}</p>
                  @endif
                  <a href="{{ $banner->target_url }}" class="mn-btn-2"><span>{{ $banner->button_text ?: 'Shop Now' }}</span></a>
                </div>
              </div>
            @empty
              <div class="mn-hero-slide swiper-slide slide-1">
                <div class="mn-hero-detail">
                  <p class="label"><span>50%<br>Off</span></p>
                  <h1>Fashion sale <br>for women's</h1>
                  <p>Elevate your every day. Style that speaks volumes.</p>
                  <a href="{{ route('ecommerce.products.index') }}" class="mn-btn-2"><span>Shop Now</span></a>
                </div>
              </div>
            @endforelse
          </div>
        </section>

        <!-- Category Section -->
        <section class="mn-category p-tb-15">
          <div class="mn-cat owl-carousel">
            @forelse ($featuredCategories as $category)
              @php
                $maxDiscount = (int) ($category->products_max_discount_percent ?? 0);
                $badgeText = $maxDiscount > 0 ? $maxDiscount.'%' : 'Hot';
              @endphp
              <div class="mn-cat-card cat-card-{{ ($loop->index % 6) + 1 }}">
                <p class="lbl"><span>{{ $badgeText }}</span></p>
                <span class="bg">{{ $badgeText }}</span>
                <h4>{{ $category->parent?->name ?? 'Collection' }}</h4>
                <h3>
                  <a href="{{ route('ecommerce.products.index', ['category' => $category->slug]) }}" style="color: inherit;">
                    {{ $category->name }}
                  </a>
                </h3>
                <p>Items ({{ $category->products_count }})</p>
                <ul>
                  @foreach ($category->products->take(3) as $catProduct)
                    <li>
                      <a href="{{ route('ecommerce.products.detail', $catProduct->slug) }}" title="{{ $catProduct->title }}">
                        <img src="{{ $catProduct->thumbnail_url }}" alt="{{ $catProduct->title }}" style="aspect-ratio: 1 / 1; object-fit: cover;">
                      </a>
                    </li>
                  @endforeach
                </ul>
              </div>
            @empty
              <div class="mn-cat-card cat-card-1">
                <p class="lbl"><span>Hot</span></p>
                <span class="bg">Hot</span>
                <h4>Collection</h4>
                <h3>Products</h3>
                <p>Items (0)</p>
              </div>
            @endforelse
          </div>
        </section>

        <!-- New Section -->
        <section class="mn-new-product p-tb-15">
          <div class="mn-title">
            <h2>New <span>Arrivals</span></h2>
          </div>
          <div class="mn-product owl-carousel">
            @if (isset($newArrivalProducts) && $newArrivalProducts->isNotEmpty())
              @foreach ($newArrivalProducts as $product)
                <div class="mn-product-card">
                  <div class="mn-product-img">
                    @if ($product->is_trending)
                      <div class="lbl">
                        <span class="trending">trending</span>
                      </div>
                    @elseif ($product->is_new)
                      <div class="lbl">
                        <span class="new">new</span>
                      </div>
                    @elseif ($product->is_sale)
                      <div class="lbl">
                        <span class="sale">sale</span>
                      </div>
                    @elseif ($product->discount_percent > 0)
                      <div class="lbl">
                        <span class="discount">{{ (int) $product->discount_percent }}% off</span>
                      </div>
                    @endif
                    <div class="mn-img">
                      <a href="{{ route('ecommerce.products.detail', $product->slug) }}" class="image">
                        <img class="main-img" src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}">
                        <img class="hover-img" src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}">
                      </a>
                      <div class="mn-pro-loader"></div>
                      <div class="mn-options">
                        <ul>
                          <li><a href="{{ route('ecommerce.products.detail', $product->slug) }}" data-tooltip title="Quick View">
                              <i class="ri-eye-line"></i></a></li>
                          <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                          <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart" data-product-id="{{ $product->id }}"><i class="ri-shopping-cart-line"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="mn-product-detail">
                    <div class="cat">
                      <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">{{ $product->category?->name ?? 'Product' }}</a>
                      @if ($product->size)
                        <ul>
                          <li>{{ strtolower($product->size->code ?: $product->size->name) }}</li>
                        </ul>
                      @endif
                    </div>
                    <h5><a href="{{ route('ecommerce.products.detail', $product->slug) }}">{{ $product->title }}</a></h5>
                    <div class="mn-price">
                      <div class="mn-price-new">Rp {{ number_format($product->final_price, 0, ',', '.') }}</div>
                      @if ($product->discount_percent > 0)
                        <div class="mn-price-old">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                      @endif
                    </div>
                    <div class="mn-pro-option">
                      <div class="mn-pro-color">
                        @if ($product->color)
                          <ul class="mn-opt-swatch mn-change-img">
                            <li class="active">
                              <a href="#" class="mn-opt-clr-img active" data-src="{{ $product->thumbnail_url }}" data-src-hover="{{ $product->thumbnail_url }}" data-tooltip="{{ $product->color->name }}">
                                <span style="background-color: {{ $product->color->color_code ?: '#000000' }};"></span>
                              </a>
                            </li>
                          </ul>
                        @endif
                      </div>
                      <a href="javascript:void(0);" class="mn-wishlist {{ in_array($product->id, $userWishlistIds ?? []) ? 'active' : '' }}" data-product-id="{{ $product->id }}" data-tooltip title="Wishlist">
                        <i class="ri-heart-{{ in_array($product->id, $userWishlistIds ?? []) ? 'fill' : 'line' }}"></i>
                      </a>
                    </div>
                  </div>
                </div>
              @endforeach
            @else
              <div class="mn-product-card">
              <div class="mn-product-img">
                <div class="lbl">
                  <span class="trending">trending</span>
                </div>
                <div class="mn-img">
                  <a href="{{ route('ecommerce.product-detail.html') }}" class="image">
                    <img class="main-img" src="{{ asset('mantu/assets/img/product/5.jpg') }}" alt="product">
                    <img class="hover-img" src="{{ asset('mantu/assets/img/product/6.jpg') }}" alt="product">
                  </a>
                  <div class="mn-pro-loader"></div>
                  <div class="mn-options">
                    <ul>
                      <li><a href="javascript:void(0)" data-tooltip title="Quick View" data-link-action="quickview" data-bs-toggle="modal" data-bs-target="#quickview_modal">
                          <i class="ri-eye-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart"><i class="ri-shopping-cart-line"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="mn-product-detail">
                <div class="cat">
                  <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">T-shirt</a>
                  <ul>
                    <li>s</li>
                    <li>m</li>
                    <li>xl</li>
                  </ul>
                </div>
                <h5><a href="">Cotton fabric T-shirt</a></h5>
                <div class="mn-price">
                  <div class="mn-price-new">$120</div>
                  <div class="mn-price-old">$130</div>
                </div>
                <div class="mn-pro-option">
                  <div class="mn-pro-color">
                    <ul class="mn-opt-swatch mn-change-img">
                      <li class="active"><a href="#" class="mn-opt-clr-img active" data-src="{{ asset('mantu/assets/img/product/5.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/5.jpg') }}" data-tooltip="Gray"><span
                            style="background-image:url('{{ asset('mantu/assets/img/product/bg/5.jpg') }}');"></span></a>
                      </li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/6.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/6.jpg') }}" data-tooltip="Orange"><span
                            style="background-image:url('{{ asset('mantu/assets/img/product/bg/6.jpg') }}');"></span></a>
                      </li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/7.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/7.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#e97e70;"></span></a></li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/8.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/8.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#70e98a;"></span></a></li>
                    </ul>
                  </div>
                  <a href="javascript:void(0);" class="mn-wishlist" data-tooltip title="Wishlist">
                    <i class="ri-heart-line"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="mn-product-card">
              <div class="mn-product-img">
                <div class="mn-img">
                  <a href="{{ route('ecommerce.product-detail.html') }}" class="image">
                    <img class="main-img" src="{{ asset('mantu/assets/img/product/9.jpg') }}" alt="product">
                    <img class="hover-img" src="{{ asset('mantu/assets/img/product/10.jpg') }}" alt="product">
                  </a>
                  <div class="mn-pro-loader"></div>
                  <div class="mn-options">
                    <ul>
                      <li><a href="javascript:void(0)" data-tooltip title="Quick View" data-link-action="quickview" data-bs-toggle="modal" data-bs-target="#quickview_modal"><i class="ri-eye-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart"><i class="ri-shopping-cart-line"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="mn-product-detail">
                <div class="cat">
                  <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">Shoes</a>
                  <ul>
                    <li>7</li>
                    <li>8</li>
                    <li>10</li>
                  </ul>
                </div>
                <h5><a href="">Special sport shoes</a></h5>
                <div class="mn-price">
                  <div class="mn-price-new">$55</div>
                </div>
                <div class="mn-pro-option">
                  <div class="mn-pro-color">
                    <ul class="mn-opt-swatch mn-change-img">
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/9.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/9.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#0e0e0e;"></span></a></li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/10.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/10.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#c54367;"></span></a></li>
                    </ul>
                  </div>
                  <a href="javascript:void(0);" class="mn-wishlist active" data-tooltip title="Wishlist">
                    <i class="ri-heart-line"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="mn-product-card">
              <div class="mn-product-img">
                <div class="lbl">
                  <span class="new">new</span>
                </div>
                <div class="mn-img">
                  <a href="{{ route('ecommerce.product-detail.html') }}" class="image">
                    <img class="main-img" src="{{ asset('mantu/assets/img/product/1.jpg') }}" alt="product">
                    <img class="hover-img" src="{{ asset('mantu/assets/img/product/3.jpg') }}" alt="product">
                  </a>
                  <div class="mn-pro-loader"></div>
                  <div class="mn-options">
                    <ul>
                      <li><a href="javascript:void(0)" data-tooltip title="Quick View" data-link-action="quickview" data-bs-toggle="modal" data-bs-target="#quickview_modal"><i class="ri-eye-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart"><i class="ri-shopping-cart-line"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="mn-product-detail">
                <div class="cat">
                  <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">Top</a>
                  <ul>
                    <li>s</li>
                    <li>m</li>
                  </ul>
                </div>
                <h5><a href="">Cotton fabric Top</a></h5>
                <div class="mn-price">
                  <div class="mn-price-new">$120</div>
                  <div class="mn-price-old">$130</div>
                </div>
                <div class="mn-pro-option">
                  <div class="mn-pro-color">
                    <ul class="mn-opt-swatch mn-change-img">
                      <li class="active"><a href="#" class="mn-opt-clr-img active" data-src="{{ asset('mantu/assets/img/product/1.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/3.jpg') }}" data-tooltip="Gray"><span
                            style="background-color:#f3f3f3;"></span></a></li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/2.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/4.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#e8c2ff;"></span></a></li>
                    </ul>
                  </div>
                  <a href="javascript:void(0);" class="mn-wishlist" data-tooltip title="Wishlist">
                    <i class="ri-heart-line"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="mn-product-card">
              <div class="mn-product-img">
                <div class="lbl">
                  <span class="sale">sale</span>
                </div>
                <div class="mn-img">
                  <a href="{{ route('ecommerce.product-detail.html') }}" class="image">
                    <img class="main-img" src="{{ asset('mantu/assets/img/product/11.jpg') }}" alt="product">
                    <img class="hover-img" src="{{ asset('mantu/assets/img/product/12.jpg') }}" alt="product">
                  </a>
                  <div class="mn-pro-loader"></div>
                  <div class="mn-options">
                    <ul>
                      <li><a href="javascript:void(0)" data-tooltip title="Quick View" data-link-action="quickview" data-bs-toggle="modal" data-bs-target="#quickview_modal"><i class="ri-eye-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart"><i class="ri-shopping-cart-line"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="mn-product-detail">
                <div class="cat">
                  <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">watches</a>
                </div>
                <h5><a href="">Mantu smart watch</a></h5>
                <div class="mn-price">
                  <div class="mn-price-new">$955</div>
                  <div class="mn-price-old">$999</div>
                </div>
                <div class="mn-pro-option">
                  <div class="mn-pro-color">
                    <ul class="mn-opt-swatch mn-change-img">
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/11.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/12.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#f3f3f3;"></span></a></li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/12.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/11.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#242424;"></span></a></li>
                    </ul>
                  </div>
                  <a href="javascript:void(0);" class="mn-wishlist" data-tooltip title="Wishlist">
                    <i class="ri-heart-line"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="mn-product-card">
              <div class="mn-product-img">
                <div class="lbl">
                  <span class="discount">20% off</span>
                </div>
                <div class="mn-img">
                  <a href="{{ route('ecommerce.product-detail.html') }}" class="image">
                    <img class="main-img" src="{{ asset('mantu/assets/img/product/13.jpg') }}" alt="product">
                    <img class="hover-img" src="{{ asset('mantu/assets/img/product/14.jpg') }}" alt="product">
                  </a>
                  <div class="mn-pro-loader"></div>
                  <div class="mn-options">
                    <ul>
                      <li><a href="javascript:void(0)" data-tooltip title="Quick View" data-link-action="quickview" data-bs-toggle="modal" data-bs-target="#quickview_modal"><i class="ri-eye-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart"><i class="ri-shopping-cart-line"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="mn-product-detail">
                <div class="cat">
                  <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">belt</a>
                </div>
                <h5><a href="">Mantu leather belt</a></h5>
                <div class="mn-price">
                  <div class="mn-price-new">$10</div>
                  <div class="mn-price-old">$12</div>
                </div>
                <div class="mn-pro-option">
                  <div class="mn-pro-color">
                    <ul class="mn-opt-swatch mn-change-img">
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/13.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/14.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#d48a5b;"></span></a></li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/14.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/13.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#242424;"></span></a></li>
                    </ul>
                  </div>
                  <a href="javascript:void(0);" class="mn-wishlist" data-tooltip title="Wishlist">
                    <i class="ri-heart-line"></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="mn-product-card">
              <div class="mn-product-img">
                <div class="mn-img">
                  <a href="{{ route('ecommerce.product-detail.html') }}" class="image">
                    <img class="main-img" src="{{ asset('mantu/assets/img/product/15.jpg') }}" alt="product">
                    <img class="hover-img" src="{{ asset('mantu/assets/img/product/16.jpg') }}" alt="product">
                  </a>
                  <div class="mn-pro-loader"></div>
                  <div class="mn-options">
                    <ul>
                      <li><a href="javascript:void(0)" data-tooltip title="Quick View" data-link-action="quickview" data-bs-toggle="modal" data-bs-target="#quickview_modal"><i class="ri-eye-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                      <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart"><i class="ri-shopping-cart-line"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="mn-product-detail">
                <div class="cat">
                  <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">Bag</a>
                  <ul>
                    <li>m</li>
                    <li>l</li>
                  </ul>
                </div>
                <h5><a href="">Leather bag</a></h5>
                <div class="mn-price">
                  <div class="mn-price-new">$66</div>
                </div>
                <div class="mn-pro-option">
                  <div class="mn-pro-color">
                    <ul class="mn-opt-swatch mn-change-img">
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/15.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/16.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#b75956;"></span></a></li>
                      <li><a href="#" class="mn-opt-clr-img" data-src="{{ asset('mantu/assets/img/product/16.jpg') }}" data-src-hover="{{ asset('mantu/assets/img/product/15.jpg') }}" data-tooltip="Orange"><span
                            style="background-color:#0e0e0e;"></span></a></li>
                    </ul>
                  </div>
                  <a href="javascript:void(0);" class="mn-wishlist" data-tooltip title="Wishlist">
                    <i class="ri-heart-line"></i>
                  </a>
                </div>
              </div>
            </div>
            @endif
          </div>
        </section>

        <!-- Service Section -->
        @if ($storeServices->isNotEmpty())
          <section class="mn-service p-tb-15">
            <div class="row m-b-m-24">
              @foreach ($storeServices as $service)
                <div class="mn-ser-content mn-ser-content-{{ ($loop->index % 4) + 1 }} col-sm-6 col-xl-3 m-b-24" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="{{ (($loop->index % 4) + 1) * 200 }}">
                  <div class="mn-ser-box">
                    <div class="mn-ser-inner">
                      <div class="mn-service-image">
                        @if ($service->icon_image_url)
                          <img src="{{ $service->icon_image_url }}" alt="{{ $service->title }}">
                        @elseif ($service->icon_svg)
                          {!! $service->icon_svg !!}
                        @endif
                      </div>
                      <div class="mn-service-desc">
                        <h3>{{ $service->title }}</h3>
                        <p>{{ $service->description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @endif

        <!-- Banner slider -->
        <section class="mn-banner p-tb-15">
          <div class="row">
            <div class="col-12" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
              <div class="mn-modern-banner owl-carousel">
                <div class="modern-banner">
                  <div class="mn-banner-img img-1"></div>
                  <div class="mn-banner-contact banner-animation">
                    <div class="inner-banner">
                      <h3>WOMEN'S</h3>
                      <h4>Fashion COLLECTION</h4>
                    </div>
                    <div class="inner-text">
                      <span class="bnr-text">Summer</span>
                      <p>New Stylish Shirts, Pants & Accessries.</p>
                    </div>
                    <div class="banner-btn">
                      <a href="{{ route('ecommerce.shop-right-sidebar.html') }}" class="mn-btn-1"><span>Book
                          Now</span></a>
                    </div>
                  </div>
                </div>
                <div class="modern-banner">
                  <div class="mn-banner-img img-2"></div>
                  <div class="mn-banner-contact banner-animation">
                    <div class="inner-banner">
                      <h3>WOMEN'S</h3>
                      <h4>goggles COLLECTION</h4>
                    </div>
                    <div class="inner-text">
                      <span class="bnr-text">Summer</span>
                      <p>New Stylish Shirts, Pants & Accessries.</p>
                    </div>
                    <div class="banner-btn">
                      <a href="{{ route('ecommerce.shop-right-sidebar.html') }}" class="mn-btn-1"><span>Book
                          Now</span></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Deal Section -->
        <section class="mn-new-product p-tb-15">
          <div class="mn-title mn-title-2">
            <div class="mn-timer">
              <h2>Day of the <span>Deals</span></h2>
              <div class="timer1 timer dealend-timer" data-date="September 30, 2026 19:15:10 PDT">
              </div>
            </div>
          </div>
          <div class="mn-product owl-carousel">
            @forelse ($dealProducts as $deal)
              <div class="mn-product-card">
                <div class="mn-product-img">
                  @if ($deal->discount_percent > 0)
                    <div class="lbl">
                      <span class="discount">{{ (int) $deal->discount_percent }}% off</span>
                    </div>
                  @elseif ($deal->is_deal)
                    <div class="lbl">
                      <span class="sale">deal</span>
                    </div>
                  @elseif ($deal->is_trending)
                    <div class="lbl">
                      <span class="trending">trending</span>
                    </div>
                  @elseif ($deal->is_new)
                    <div class="lbl">
                      <span class="new">new</span>
                    </div>
                  @elseif ($deal->is_sale)
                    <div class="lbl">
                      <span class="sale">sale</span>
                    </div>
                  @endif
                  <div class="mn-img">
                    <a href="{{ route('ecommerce.products.detail', $deal->slug) }}" class="image">
                      <img class="main-img" src="{{ $deal->thumbnail_url }}" alt="{{ $deal->title }}">
                      <img class="hover-img" src="{{ $deal->thumbnail_url }}" alt="{{ $deal->title }}">
                    </a>
                    <div class="mn-pro-loader"></div>
                    <div class="mn-options">
                      <ul>
                        <li><a href="{{ route('ecommerce.products.detail', $deal->slug) }}" data-tooltip title="Quick View"><i class="ri-eye-line"></i></a></li>
                        <li><a href="javascript:void(0)" data-tooltip title="Compare" class="mn-compare"><i class="ri-repeat-line"></i></a></li>
                        <li><a href="javascript:void(0)" data-tooltip title="Add To Cart" class="mn-add-cart" data-product-id="{{ $deal->id }}"><i class="ri-shopping-cart-line"></i></a></li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="mn-product-detail">
                  <div class="cat">
                    <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">{{ $deal->category?->name ?? 'Product' }}</a>
                    @if ($deal->size)
                      <ul>
                        <li>{{ strtolower($deal->size->code ?: $deal->size->name) }}</li>
                      </ul>
                    @endif
                  </div>
                  <h5><a href="{{ route('ecommerce.products.detail', $deal->slug) }}">{{ $deal->title }}</a></h5>
                  <div class="mn-price">
                    <div class="mn-price-new">Rp {{ number_format($deal->final_price, 0, ',', '.') }}</div>
                    @if ($deal->discount_percent > 0)
                      <div class="mn-price-old">Rp {{ number_format($deal->price, 0, ',', '.') }}</div>
                    @endif
                  </div>
                  <div class="mn-pro-option">
                    <div class="mn-pro-color">
                      @if ($deal->color)
                        <ul class="mn-opt-swatch mn-change-img">
                          <li class="active">
                            <a href="#" class="mn-opt-clr-img active" data-src="{{ $deal->thumbnail_url }}" data-src-hover="{{ $deal->thumbnail_url }}" data-tooltip="{{ $deal->color->name }}">
                              <span style="background-color: {{ $deal->color->color_code ?: '#000000' }};"></span>
                            </a>
                          </li>
                        </ul>
                      @endif
                    </div>
                    <a href="javascript:void(0);" class="mn-wishlist {{ in_array($deal->id, $userWishlistIds ?? []) ? 'active' : '' }}" data-product-id="{{ $deal->id }}" data-tooltip title="Wishlist">
                      <i class="ri-heart-{{ in_array($deal->id, $userWishlistIds ?? []) ? 'fill' : 'line' }}"></i>
                    </a>
                  </div>
                </div>
              </div>
            @empty
              <div class="mn-product-card text-center py-4">
                <p class="text-muted">{{ __('No deal products available.') }}</p>
              </div>
            @endforelse
          </div>
        </section>

        <!-- Testimonials Section -->
        <section class="mn-testimonials p-tb-15">
          <div class="row">
            <div class="col-md-12">
              <div class="testim-bg">
                <div class="section-title d-none">
                  <h2>Customers <span>Review</span></h2>
                </div>
                <div class="mn-test-outer mn-testimonials">
                  <ul class="mn-testimonial-slider owl-carousel">
                    @forelse ($testimonials as $testimonial)
                      <li class="mn-test-item">
                        <img src="{{ asset('mantu/assets/img/icons/top-quotes.svg') }}" class="svg_img test_svg top" alt="quotes">
                        <div class="mn-test-inner">
                          <div class="mn-test-img" style="width: 100px; height: 100px; overflow: hidden; border-radius: 50%;">
                            <img alt="{{ $testimonial->name }}" title="{{ $testimonial->name }}" src="{{ $testimonial->image_url }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; display: block;">
                          </div>
                          <div class="mn-test-content">
                            <div class="mn-test-desc">{{ $testimonial->content }}</div>
                            <div class="mn-test-name">{{ $testimonial->name }}</div>
                            <div class="mn-test-designation">({{ $testimonial->designation ?: 'Customer' }})</div>
                          </div>
                        </div>
                        <img src="{{ asset('mantu/assets/img/icons/bottom-quotes.svg') }}" class="svg_img test_svg bottom" alt="quotes">
                      </li>
                    @empty
                      <li class="mn-test-item">
                        <div class="mn-test-inner text-center py-4">
                          <p class="text-muted">{{ __('No customer reviews yet.') }}</p>
                        </div>
                      </li>
                    @endforelse
                  </ul>
                </div>
                <span class="mn-testi-shape-2"></span>
              </div>
            </div>
          </div>
        </section>

        <!-- Blog Section Start -->
        <section class="mn-blog p-tb-15">
          <div class="mn-title">
            <h2>Our <span>Blogs</span></h2>
          </div>
          <div class="mn-blog-carousel owl-carousel">
            @forelse ($blogs as $blog)
              <div class="mn-blog-card">
                <div class="blog-info">
                  <figure class="blog-img">
                    <a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}"><img src="{{ $blog->image_url }}" alt="{{ $blog->title }}"></a>
                  </figure>
                  <div class="detail">
                    <label>{{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }} - <a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}">{{ $blog->category ?? 'General' }}</a></label>
                    <h3><a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}">{{ $blog->title }}</a></h3>
                    <div class="more-info">
                      <a href="{{ route('ecommerce.blogs.detail', $blog->slug) }}">Read More<i class="ri-arrow-right-double-line"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="mn-blog-card">
                <div class="blog-info text-center py-4 text-muted">
                  <p>{{ __('No blog posts available at the moment.') }}</p>
                </div>
              </div>
            @endforelse
          </div>
        </section>

        <!-- Instagram Start -->
        <section class="mn-instagram module p-tb-15" id="insta">
          <h2 class="d-none">insta</h2>
          <div class="mn-insta-wrapper">
            <div class="mn-insta-outer">
              <div class="insta-auto">
                @forelse ($instaProducts as $instaProduct)
                  <!-- instagram item -->
                  <div class="mn-insta-item">
                    <div class="mn-insta-inner">
                      <a href="{{ route('ecommerce.products.detail', $instaProduct->slug) }}" title="{{ $instaProduct->name }}">
                        <img src="{{ $instaProduct->thumbnail_url }}" alt="{{ $instaProduct->name }}" style="aspect-ratio: 1 / 1; object-fit: cover;">
                      </a>
                    </div>
                  </div>
                @empty
                  @for ($i = 1; $i <= 7; $i++)
                    <div class="mn-insta-item">
                      <div class="mn-insta-inner">
                        <a href="{{ route('ecommerce.products.index') }}"><img src="{{ asset("mantu/assets/img/instagram/{$i}.jpg") }}" alt="insta"></a>
                      </div>
                    </div>
                  @endfor
                @endforelse
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
@endsection
