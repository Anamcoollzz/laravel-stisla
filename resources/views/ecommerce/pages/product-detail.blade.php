@php
  $product = $product ?? \App\Models\Product::with(['category', 'brand', 'vendor', 'size', 'color', 'images'])->withAvg('approvedReviews', 'rating')->withCount('approvedReviews')->first();
  $reviews = $reviews ?? ($product ? $product->approvedReviews()->with('user')->get() : collect());
  $randomProducts = $randomProducts ?? \App\Models\Product::query()->active()->when($product, fn($q) => $q->where('id', '!=', $product->id))->withAvg('approvedReviews', 'rating')->inRandomOrder()->take(6)->get();
  $relatedProducts = $relatedProducts ?? ($product ? \App\Models\Product::query()->active()->where('id', '!=', $product->id)->with(['category', 'brand', 'size', 'color'])->take(8)->get() : collect());
  $avgRating = $product ? ($product->average_rating ?: 0) : 0;
  $reviewCount = $reviews->count();
  $sidebarCategories = $sidebarCategories ?? \App\Models\ProductCategory::query()->whereNull('parent_id')->where('is_active', true)->withCount('products')->get()->unique('name')->values();
  $sidebarBrands = $sidebarBrands ?? \App\Models\ProductBrand::query()->orderBy('name')->get()->unique('name')->values();
  $sidebarSizes = $sidebarSizes ?? \App\Models\ProductSize::query()->orderBy('id')->get()->unique('name')->values();
  $sidebarColors = $sidebarColors ?? \App\Models\ProductColor::query()->orderBy('name')->get()->unique('name')->values();
  $minPrice = $minPrice ?? (int) floor((\App\Models\Product::active()->min('price') ?? 0) / 10000) * 10000;
  $maxPrice = $maxPrice ?? (int) ceil((\App\Models\Product::active()->max('price') ?? 10000000) / 10000) * 10000;
  if ($minPrice >= $maxPrice) {
    $maxPrice = $minPrice + 100000;
  }
@endphp
@extends('ecommerce.layouts.app-ecommerce')

@section('title', ($product?->title ? $product->title . ' - ' : '') . 'Ecommerce')

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
                  <h2 class="mn-breadcrumb-title">{{ $product?->title ?? 'Product Detail' }}</h2>
                </div>
                <div class="col-md-6 col-sm-12">
                  <!-- mn-breadcrumb-list start -->
                  <ul class="mn-breadcrumb-list">
                    <li class="mn-breadcrumb-item">
                      <a href="{{ route('ecommerce.index.html') }}">Home</a>
                    </li>
                    @if ($product?->category)
                      <li class="mn-breadcrumb-item">
                        <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">{{ $product->category->name }}</a>
                      </li>
                    @endif
                    <li class="mn-breadcrumb-item active">{{ $product?->title ?? 'Product Detail' }}</li>
                  </ul>
                  <!-- mn-breadcrumb-list end -->
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xxl-12">
            <section class="mn-single-product">
              <div class="row">
                <div
                  class="mn-pro-rightside mn-common-rightside col-lg-9 col-md-12 m-b-15"
                >
                  <!-- Single product content Start -->
                  <div class="single-pro-block">
                    <div class="single-pro-inner">
                      <div class="row">
                        <div class="single-pro-img">
                          <div class="single-product-scroll">
                            @php
                              $detailImages = collect();
                              if (! empty($product?->thumbnail_url)) {
                                  $detailImages->push($product->thumbnail_url);
                              }
                              if ($product && $product->images->isNotEmpty()) {
                                  foreach ($product->images as $img) {
                                      $detailImages->push($img->image_url);
                                  }
                              }
                              $detailImages = $detailImages->unique();
                              if ($detailImages->isEmpty()) {
                                  $detailImages = collect([
                                      asset('mantu/assets/img/product/27.jpg'),
                                      asset('mantu/assets/img/product/28.jpg'),
                                      asset('mantu/assets/img/product/29.jpg'),
                                  ]);
                              } elseif ($detailImages->count() < 3) {
                                  $fallbackSeed = $detailImages->first();
                                  while ($detailImages->count() < 3) {
                                      $detailImages->push($fallbackSeed);
                                  }
                              }
                            @endphp

                            <div class="single-product-cover">
                              @foreach ($detailImages as $imgUrl)
                                <div class="single-slide zoom-image-hover">
                                  <img
                                    class="img-responsive"
                                    src="{{ $imgUrl }}"
                                    alt="{{ $product?->title ?? 'Product' }}"
                                  />
                                </div>
                              @endforeach
                            </div>
                            <div class="single-nav-thumb">
                              @foreach ($detailImages as $imgUrl)
                                <div class="single-slide">
                                  <img
                                    class="img-responsive"
                                    src="{{ $imgUrl }}"
                                    alt="{{ $product?->title ?? 'Product' }}"
                                  />
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                        <div class="single-pro-desc m-t-991">
                          <div class="single-pro-content">
                            <h5 class="mn-single-title">
                              {{ $product?->title ?? 'Product Title' }}
                            </h5>
                            <div class="mn-single-rating-wrap">
                              <div class="mn-single-rating mn-pro-rating">
                                @php $roundedAvg = round($avgRating); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                  <i class="ri-star-fill {{ $i <= $roundedAvg ? '' : 'grey' }}"></i>
                                @endfor
                              </div>
                              <span class="mn-read-review">
                                |&nbsp;&nbsp;<a href="#mn-spt-nav-review" onclick="$('#review-tab').tab('show');">
                                  {{ $avgRating > 0 ? number_format($avgRating, 1) . ' / 5.0 Rating' : __('No ratings yet') }}
                                  ({{ $reviewCount }} {{ __('reviews') }})
                                </a>
                              </span>
                            </div>

                            <div class="mn-single-price-stoke">
                              <div class="mn-single-price">
                                <div class="final-price">
                                  Rp {{ number_format($product?->final_price ?? 0, 0, ',', '.') }}
                                  @if (($product?->discount_percent ?? 0) > 0)
                                    <span class="price-des">-{{ (int) $product->discount_percent }}%</span>
                                  @endif
                                </div>
                                @if (($product?->discount_percent ?? 0) > 0)
                                  <div class="mrp">
                                    M.R.P. : <span>Rp {{ number_format($product?->price ?? 0, 0, ',', '.') }}</span>
                                  </div>
                                @endif
                              </div>
                              <div class="mn-single-stoke">
                                <span class="mn-single-sku">SKU#: {{ $product?->sku ?? 'N/A' }}</span>
                                <span class="mn-single-ps-title {{ ($product?->stock ?? 0) > 0 ? '' : 'text-danger' }}">
                                  {{ ($product?->stock ?? 0) > 0 ? 'IN STOCK (' . $product->stock . ')' : 'OUT OF STOCK' }}
                                </span>
                              </div>
                            </div>
                            <div class="mn-single-sales">
                              <div class="mn-single-sales-inner">
                                <div class="mn-single-sales-visitor">
                                  Real time <span>28</span> visitor right now!
                                </div>
                                <div class="mn-single-sales-countdown">
                                  <div class="mn-single-countdown">
                                    <div
                                      class="timer1 timer dealend-timer"
                                      data-date="September 30, 2026 19:15:10 PDT"
                                    ></div>
                                    <div class="mn-single-count-desc">
                                      Special Promo Offer!
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="mn-single-desc">
                              {!! $product?->short_description ?? 'No short description available.' !!}
                            </div>

                            <div class="mn-single-list">
                              <ul>
                                @if ($product?->brand)
                                  <li><strong>Brand :</strong> {{ $product->brand->name }}</li>
                                @endif
                                @if ($product?->category)
                                  <li><strong>Category :</strong> {{ $product->category->name }}</li>
                                @endif
                                @if ($product?->vendor)
                                  <li><strong>Vendor :</strong> {{ $product->vendor->name }}</li>
                                @endif
                                @if ($product?->size)
                                  <li><strong>Size :</strong> {{ $product->size->name }} ({{ $product->size->code }})</li>
                                @endif
                                @if ($product?->color)
                                  <li><strong>Color :</strong> {{ $product->color->name }}</li>
                                @endif
                              </ul>
                            </div>

                            <div class="mn-pro-variation">
                              @if ($product?->size)
                                <div
                                  class="mn-pro-variation-inner mn-pro-variation-size m-b-24"
                                >
                                  <span>Size</span>
                                  <div class="mn-pro-variation-content">
                                    <ul>
                                      <li class="active"><span>{{ $product->size->name }}</span></li>
                                    </ul>
                                  </div>
                                </div>
                              @endif
                              @if ($product?->color)
                                <div
                                  class="mn-pro-variation-inner mn-pro-variation-color"
                                >
                                  <span>Colors</span>
                                  <div class="mn-pro-variation-content">
                                    <ul>
                                      <li class="active" title="{{ $product->color->name }}">
                                        <span
                                          style="background-color: {{ $product->color->color_code ?: '#000000' }}"
                                        ></span>
                                      </li>
                                    </ul>
                                  </div>
                                </div>
                              @endif
                            </div>
                            <div class="mn-single-qty">
                              <div class="qty-plus-minus">
                                <input
                                  class="qty-input"
                                  type="text"
                                  name="ms_qtybtn"
                                  value="1"
                                />
                              </div>
                              <div class="mn-btns">
                                <div class="mn-single-cart">
                                  <button
                                    type="button"
                                    class="btn btn-primary mn-btn-2 mn-add-cart"
                                    data-product-id="{{ $product->id }}"
                                  >
                                    <span>Add To Cart</span>
                                  </button>
                                </div>
                                <div class="mn-single-wishlist">
                                  <a
                                    href="javascript:void(0)"
                                    class="mn-btn-group wishlist mn-wishlist {{ in_array($product->id, $userWishlistIds ?? []) ? 'active' : '' }}"
                                    data-product-id="{{ $product->id }}"
                                    title="Wishlist"
                                  >
                                    <i class="ri-heart-line"></i>
                                  </a>
                                </div>
                                <div class="mn-single-mn-compare">
                                  <a
                                    href="javascript:void(0)"
                                    class="mn-btn-group mn-compare"
                                    title="Quick view"
                                  >
                                    <i class="ri-repeat-line"></i>
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--Single product content End -->
                  <!-- Add More and get discount content Start -->
                  <div class="single-add-more m-tb-30">
                    <div class="mn-add-more-slider owl-carousel">
                      @foreach ($randomProducts as $rndProduct)
                        @php
                          $rndRating = round((float) ($rndProduct->approved_reviews_avg_rating ?? 0));
                        @endphp
                        <div class="add-more-item">
                          <a href="javascript:void(0)" class="mn-btn-2 mn-add-cart" data-product-id="{{ $rndProduct->id }}" title="Add To Cart">
                            <span>+</span>
                          </a>
                          <div class="add-more-img">
                            <a href="{{ route('ecommerce.products.detail', $rndProduct->slug) }}">
                              <img src="{{ $rndProduct->thumbnail_url }}" alt="{{ $rndProduct->title }}" style="object-fit: cover;" />
                            </a>
                          </div>
                          <div class="add-more-info">
                            <h5>
                              <a href="{{ route('ecommerce.products.detail', $rndProduct->slug) }}" class="text-dark">
                                {{ Str::limit($rndProduct->title, 26) }}
                              </a>
                            </h5>
                            <span class="mn-pro-rating">
                              @for ($i = 1; $i <= 5; $i++)
                                <i class="ri-star-fill {{ $i <= $rndRating ? '' : 'grey' }}"></i>
                              @endfor
                            </span>
                            <span class="mn-price">
                              <span class="new-price">Rp {{ number_format($rndProduct->final_price, 0, ',', '.') }}</span>
                              @if ($rndProduct->discount_percent > 0)
                                <span class="old-price">Rp {{ number_format($rndProduct->price, 0, ',', '.') }}</span>
                              @endif
                            </span>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <!-- Single product tab start -->
                  <div class="mn-single-pro-tab">
                    <div class="mn-single-pro-tab-wrapper">
                      <div class="mn-single-pro-tab-nav">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item" role="presentation">
                            <button
                              class="nav-link active"
                              id="details-tab"
                              data-bs-toggle="tab"
                              data-bs-target="#mn-spt-nav-details"
                              type="button"
                              role="tab"
                              aria-controls="mn-spt-nav-details"
                              aria-selected="true"
                            >
                              Detail
                            </button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button
                              class="nav-link"
                              id="info-tab"
                              data-bs-toggle="tab"
                              data-bs-target="#mn-spt-nav-info"
                              type="button"
                              role="tab"
                              aria-controls="mn-spt-nav-info"
                              aria-selected="false"
                            >
                              Specifications
                            </button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button
                              class="nav-link"
                              id="vendor-tab"
                              data-bs-toggle="tab"
                              data-bs-target="#mn-spt-nav-vendor"
                              type="button"
                              role="tab"
                              aria-controls="mn-spt-nav-vendor"
                              aria-selected="false"
                            >
                              Vendor
                            </button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button
                              class="nav-link"
                              id="review-tab"
                              data-bs-toggle="tab"
                              data-bs-target="#mn-spt-nav-review"
                              type="button"
                              role="tab"
                              aria-controls="mn-spt-nav-review"
                              aria-selected="false"
                            >
                              Reviews ({{ $reviewCount }})
                            </button>
                          </li>
                        </ul>
                      </div>
                      <div class="tab-content mn-single-pro-tab-content">
                        <div
                          id="mn-spt-nav-details"
                          class="tab-pane fade show active"
                        >
                          <div class="mn-single-pro-tab-desc">
                            {!! $product?->description_detail ?? $product?->short_description ?? '<p>No detail description available.</p>' !!}
                          </div>
                        </div>
                        <div id="mn-spt-nav-info" class="tab-pane fade">
                          <div class="mn-single-pro-tab-moreinfo">
                            <ul>
                              <li><span>SKU</span> {{ $product?->sku ?? '-' }}</li>
                              @if ($product?->brand)
                                <li><span>Brand</span> {{ $product->brand->name }}</li>
                              @endif
                              @if ($product?->category)
                                <li><span>Category</span> {{ $product->category->name }}</li>
                              @endif
                              @if ($product?->size)
                                <li><span>Size</span> {{ $product->size->name }} ({{ $product->size->code }})</li>
                              @endif
                              @if ($product?->color)
                                <li><span>Color</span> {{ $product->color->name }}</li>
                              @endif
                              @if (is_array($product?->spesifications))
                                @foreach ($product->spesifications as $k => $spec)
                                  @php
                                    $specKey = is_array($spec) ? ($spec['key'] ?? '') : $k;
                                    $specVal = is_array($spec) ? ($spec['value'] ?? '') : $spec;
                                  @endphp
                                  @if ($specKey)
                                    <li><span>{{ $specKey }}</span> {{ $specVal }}</li>
                                  @endif
                                @endforeach
                              @endif
                            </ul>
                          </div>
                        </div>
                        <div id="mn-spt-nav-vendor" class="tab-pane fade">
                          <div class="mn-single-pro-tab-moreinfo">
                            @if ($product?->vendor)
                              <div class="mn-product-vendor">
                                <div class="mn-vendor-info">
                                  <span>
                                    <img
                                      src="{{ asset('mantu/assets/img/vendor/1.jpg') }}"
                                      alt="{{ $product->vendor->name }}"
                                    />
                                  </span>
                                  <div>
                                    <h5>{{ $product->vendor->name }}</h5>
                                    @if ($product->vendor->email)
                                      <p>{{ $product->vendor->email }}</p>
                                    @endif
                                  </div>
                                </div>
                                <div class="mn-detail">
                                  <ul>
                                    <li>
                                      <span>Phone No. :</span> {{ $product->vendor->phone ?: '-' }}
                                    </li>
                                    <li>
                                      <span>Email. :</span> {{ $product->vendor->email ?: '-' }}
                                    </li>
                                    <li>
                                      <span>Address. :</span> {{ $product->vendor->address ?: '-' }}
                                    </li>
                                  </ul>
                                  @if ($product->vendor->description)
                                    <p class="mb-0">
                                      {{ $product->vendor->description }}
                                    </p>
                                  @endif
                                </div>
                              </div>
                            @else
                              <p class="text-muted">Vendor information not available.</p>
                            @endif
                          </div>
                        </div>
                        <div id="mn-spt-nav-review" class="tab-pane fade">
                          <div class="row">
                            @if (session('review_success'))
                              <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('review_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                            @endif

                            @if ($errors->any())
                              <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <ul class="mb-0 ps-3">
                                  @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                  @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                            @endif

                            <div class="mn-t-review-wrapper mt-0">
                              @forelse ($reviews as $review)
                                <div class="mn-t-review-item">
                                  <div class="mn-t-review-avtar">
                                    <img src="{{ $review->avatar_url }}" alt="{{ $review->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;" />
                                  </div>
                                  <div class="mn-t-review-content">
                                    <div class="mn-t-review-top">
                                      <div class="mn-t-review-name">
                                        {{ $review->name }}
                                        <span class="text-muted ms-2" style="font-weight: normal; font-size: 12px;">
                                          {{ $review->created_at?->format('d M Y') }}
                                        </span>
                                      </div>
                                      <div class="mn-t-review-rating mn-pro-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                          <i class="ri-star-fill {{ $i <= $review->rating ? '' : 'grey' }}"></i>
                                        @endfor
                                      </div>
                                    </div>
                                    <div class="mn-t-review-bottom">
                                      <p>{{ $review->comment }}</p>
                                    </div>
                                  </div>
                                </div>
                              @empty
                                <div class="text-center py-4 text-muted">
                                  <p class="mb-0">{{ __('Belum ada ulasan untuk produk ini. Jadilah yang pertama mengulas!') }}</p>
                                </div>
                              @endforelse
                            </div>

                            <div class="mn-ratting-content mt-4">
                              <h3>Add a Review</h3>
                              <div class="mn-ratting-form">
                                <form action="{{ $product ? route('ecommerce.products.review.store', $product->slug) : '#' }}" method="POST">
                                  @csrf
                                  <input type="hidden" name="rating" id="review-rating-input" value="{{ old('rating', 5) }}">
                                  <div class="mn-ratting-star">
                                    <span>Your rating:</span>
                                    <div class="mn-t-review-rating mn-pro-rating" id="interactive-rating-stars" style="cursor: pointer;">
                                      @for ($i = 1; $i <= 5; $i++)
                                        <i class="ri-star-fill rating-star-item {{ $i <= old('rating', 5) ? '' : 'grey' }}" data-value="{{ $i }}"></i>
                                      @endfor
                                    </div>
                                  </div>
                                  <div class="mn-ratting-input">
                                    <input
                                      name="name"
                                      placeholder="Name*"
                                      type="text"
                                      value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}"
                                      required
                                    />
                                  </div>
                                  <div class="mn-ratting-input">
                                    <input
                                      name="email"
                                      placeholder="Email*"
                                      type="email"
                                      value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                                      required
                                    />
                                  </div>
                                  <div class="mn-ratting-input form-submit">
                                    <textarea
                                      name="comment"
                                      placeholder="Enter Your Comment*"
                                      required
                                    >{{ old('comment') }}</textarea>
                                    <button
                                      class="mn-btn-2"
                                      type="submit"
                                      value="Submit"
                                    >
                                      <span>Submit</span>
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Related Section -->
                  <section class="mn-related-product m-t-30">
                    <div class="mn-title">
                      <h2>Related <span>Products</span></h2>
                    </div>
                    <div class="mn-related owl-carousel">
                      @forelse ($relatedProducts as $relProduct)
                        <div class="mn-product-card">
                          <div class="mn-product-img">
                            @if ($relProduct->is_trending)
                              <div class="lbl">
                                <span class="trending">trending</span>
                              </div>
                            @elseif ($relProduct->is_new)
                              <div class="lbl">
                                <span class="new">new</span>
                              </div>
                            @elseif ($relProduct->is_sale)
                              <div class="lbl">
                                <span class="sale">sale</span>
                              </div>
                            @elseif ($relProduct->discount_percent > 0)
                              <div class="lbl">
                                <span class="discount">{{ (int) $relProduct->discount_percent }}% off</span>
                              </div>
                            @endif
                            <div class="mn-img">
                              <a href="{{ route('ecommerce.products.detail', $relProduct->slug) }}" class="image">
                                <img
                                  class="main-img"
                                  src="{{ $relProduct->thumbnail_url }}"
                                  alt="{{ $relProduct->title }}"
                                />
                                <img
                                  class="hover-img"
                                  src="{{ $relProduct->thumbnail_url }}"
                                  alt="{{ $relProduct->title }}"
                                />
                              </a>
                              <div class="mn-pro-loader"></div>
                              <div class="mn-options">
                                <ul>
                                  <li>
                                    <a
                                      href="{{ route('ecommerce.products.detail', $relProduct->slug) }}"
                                      data-tooltip
                                      title="Quick View"
                                    ><i class="ri-eye-line"></i
                                    ></a>
                                  </li>
                                  <li>
                                    <a
                                      href="javascript:void(0)"
                                      data-tooltip
                                      title="Add To Cart"
                                      class="mn-add-cart"
                                      data-product-id="{{ $relProduct->id }}"
                                    ><i class="ri-shopping-cart-line"></i
                                    ></a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                          <div class="mn-product-detail">
                            <div class="cat">
                              <a href="{{ route('ecommerce.shop-right-sidebar.html') }}">{{ $relProduct->category?->name ?? 'Product' }}</a>
                              @if ($relProduct->size)
                                <ul>
                                  <li>{{ strtolower($relProduct->size->code ?: $relProduct->size->name) }}</li>
                                </ul>
                              @endif
                            </div>
                            <h5><a href="{{ route('ecommerce.products.detail', $relProduct->slug) }}">{{ $relProduct->title }}</a></h5>
                            <div class="mn-price">
                              <div class="mn-price-new">Rp {{ number_format($relProduct->final_price, 0, ',', '.') }}</div>
                              @if ($relProduct->discount_percent > 0)
                                <div class="mn-price-old">Rp {{ number_format($relProduct->price, 0, ',', '.') }}</div>
                              @endif
                            </div>
                            @if ($relProduct->color)
                              <div class="mn-pro-option">
                                <div class="mn-pro-color">
                                  <ul class="mn-opt-swatch mn-change-img">
                                    <li class="active">
                                      <a
                                        href="{{ route('ecommerce.products.detail', $relProduct->slug) }}"
                                        class="mn-opt-clr-img active"
                                        data-tooltip="{{ $relProduct->color->name }}"
                                      ><span
                                        style="background-color: {{ $relProduct->color->color_code ?: '#000000' }}"
                                      ></span></a>
                                    </li>
                                  </ul>
                                </div>
                                <a
                                  href="javascript:void(0);"
                                  class="mn-wishlist"
                                  data-tooltip
                                  title="Wishlist"
                                >
                                  <i class="ri-heart-line"></i>
                                </a>
                              </div>
                            @endif
                          </div>
                        </div>
                      @empty
                        <p class="text-muted p-3">No related products found.</p>
                      @endforelse
                    </div>
                  </section>
                </div>
                <!-- Sidebar Area Start -->
                <div class="mn-shop-sidebar col-lg-3 col-md-12 m-t-991">
                  <div id="shop_sidebar">
                    <div class="mn-sidebar-wrap">
                      <!-- Sidebar Filters Block -->
                      <div class="mn-sidebar-block drop">
                        <div class="mn-sb-title">
                          <h3 class="mn-sidebar-title">Categories</h3>
                        </div>
                        <div class="mn-sb-block-content p-t-15">
                          <ul>
                            @foreach ($sidebarCategories as $sbCat)
                              <li>
                                <a
                                  href="{{ route('ecommerce.shop-right-sidebar.html') }}"
                                  class="mn-sidebar-block-item main"
                                >{{ $sbCat->name }} <span>-{{ $sbCat->products_count ?? $sbCat->products()->count() }}</span></a>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                      <!-- Sidebar Brand Block -->
                      <div class="mn-sidebar-block">
                        <div class="mn-sb-title">
                          <h3 class="mn-sidebar-title">Brand</h3>
                        </div>
                        <div class="mn-sb-block-content">
                          <ul>
                            @foreach ($sidebarBrands as $sbBrand)
                              <li>
                                <div class="mn-sidebar-block-item">
                                  <input type="checkbox" {{ ($product?->brand_id == $sbBrand->id) ? 'checked' : '' }} />
                                  <a href="javascript:void(0)">
                                    <span>{{ $sbBrand->name }}</span>
                                  </a>
                                  <span class="checked"></span>
                                </div>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                      <!-- Sidebar Size Block -->
                      <div class="mn-sidebar-block">
                        <div class="mn-sb-title">
                          <h3 class="mn-sidebar-title">Size</h3>
                        </div>
                        <div class="mn-sb-block-content">
                          <ul>
                            @foreach ($sidebarSizes as $sbSize)
                              <li>
                                <div class="mn-sidebar-block-item">
                                  <input type="checkbox" value="" {{ ($product?->size_id == $sbSize->id) ? 'checked' : '' }} />
                                  <a href="#">{{ $sbSize->name }} ({{ $sbSize->code }})</a>
                                  <span class="checked"></span>
                                </div>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                      <!-- Sidebar Color item -->
                      <div
                        class="mn-sidebar-block color-block mn-sidebar-block-clr"
                      >
                        <div class="mn-sb-title">
                          <h3 class="mn-sidebar-title">Color</h3>
                        </div>
                        <div class="mn-sb-block-content">
                          <ul>
                            @foreach ($sidebarColors as $sbColor)
                              <li class="{{ ($product?->color_id == $sbColor->id) ? 'active' : '' }}" title="{{ $sbColor->name }}">
                                <div class="mn-sidebar-block-item">
                                  <input type="checkbox" value="" {{ ($product?->color_id == $sbColor->id) ? 'checked' : '' }} />
                                  <span
                                    class="mn-clr-block"
                                    style="background-color: {{ $sbColor->color_code ?: '#000000' }}"
                                  ></span>
                                  <span class="checked"></span>
                                </div>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                      <!-- Sidebar Price Block -->
                      <div class="mn-sidebar-block">
                        <div class="mn-sb-title">
                          <h3 class="mn-sidebar-title">Price</h3>
                        </div>
                        <div
                          class="mn-sb-block-content mn-price-range-slider es-price-slider"
                        >
                          <div class="mn-price-filter">
                            <div class="mn-price-input">
                              <label class="filter__label">
                                From<input type="text" class="filter__input" />
                              </label>
                              <span class="mn-price-divider"></span>
                              <label class="filter__label">
                                To<input type="text" class="filter__input" />
                              </label>
                            </div>
                            <div
                              id="mn-sliderPrice"
                              class="filter__slider-price"
                              data-min="{{ $minPrice }}"
                              data-max="{{ $maxPrice }}"
                              data-step="10000"
                              data-start-min="{{ request('min_price', $minPrice) }}"
                              data-start-max="{{ request('max_price', $maxPrice) }}"
                              data-redirect-url="{{ route('ecommerce.products.index') }}"
                            ></div>
                          </div>
                        </div>
                      </div>
                      <!-- Sidebar tags -->
                      <div class="mn-sidebar-block">
                        <div class="mn-sb-title">
                          <h3 class="mn-sidebar-title">Tags</h3>
                        </div>
                        <div class="mn-tag-block mn-sb-block-content">
                          @if (! empty($product?->tags) && is_array($product->tags))
                            @foreach ($product->tags as $tag)
                              <a href="{{ route('ecommerce.products.index', ['q' => $tag]) }}"><span>{{ $tag }}</span></a>
                            @endforeach
                          @else
                            <a href="{{ route('ecommerce.products.index', ['q' => 'Clothes']) }}"><span>Clothes</span></a>
                            <a href="{{ route('ecommerce.products.index', ['q' => 'Shoes']) }}"><span>Shoes</span></a>
                            <a href="{{ route('ecommerce.products.index', ['q' => 'Bags']) }}"><span>Bags</span></a>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
@endsection

@push('scripts')
<script>
  (function () {
    const slider = document.getElementById('mn-sliderPrice');
    if (!slider) return;

    function handlePriceRedirect(minVal, maxVal) {
      const redirectBase = slider.dataset.redirectUrl || '{{ route('ecommerce.products.index') }}';
      const redirectUrl = new URL(redirectBase, window.location.origin);
      redirectUrl.searchParams.set('min_price', Math.round(minVal));
      redirectUrl.searchParams.set('max_price', Math.round(maxVal));
      window.location.href = redirectUrl.toString();
    }

    if (slider.noUiSlider) {
      slider.noUiSlider.on('change', function (values) {
        handlePriceRedirect(values[0], values[1]);
      });
    }

    const priceInputs = document.querySelectorAll('.mn-price-filter input.filter__input');
    priceInputs.forEach(function (input) {
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          if (slider.noUiSlider) {
            const vals = slider.noUiSlider.get();
            handlePriceRedirect(vals[0], vals[1]);
          }
        }
      });
    });

    // Interactive Rating Stars
    const starContainer = document.getElementById('interactive-rating-stars');
    const ratingInput = document.getElementById('review-rating-input');
    if (starContainer && ratingInput) {
      const stars = starContainer.querySelectorAll('.rating-star-item');
      stars.forEach(function (star) {
        star.addEventListener('click', function () {
          const val = parseInt(this.dataset.value, 10);
          ratingInput.value = val;
          stars.forEach(function (s) {
            const sVal = parseInt(s.dataset.value, 10);
            s.classList.toggle('grey', sVal > val);
          });
        });
      });
    }

    @if (session('review_success') || $errors->any())
      const reviewTabTrigger = document.getElementById('review-tab');
      if (reviewTabTrigger && typeof bootstrap !== 'undefined') {
        const tab = new bootstrap.Tab(reviewTabTrigger);
        tab.show();
        reviewTabTrigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    @endif
  })();
</script>
@endpush
