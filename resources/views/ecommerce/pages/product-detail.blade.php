@php
  $product = $product ?? \App\Models\Product::with(['category', 'brand', 'vendor', 'size', 'color'])->first();
  $relatedProducts = $relatedProducts ?? ($product ? \App\Models\Product::query()->active()->where('id', '!=', $product->id)->with(['category', 'brand', 'size', 'color'])->take(8)->get() : collect());
  $sidebarCategories = $sidebarCategories ?? \App\Models\ProductCategory::query()->whereNull('parent_id')->where('is_active', true)->withCount('products')->get();
  $sidebarBrands = $sidebarBrands ?? \App\Models\ProductBrand::query()->orderBy('name')->get();
  $sidebarSizes = $sidebarSizes ?? \App\Models\ProductSize::query()->orderBy('id')->get();
  $sidebarColors = $sidebarColors ?? \App\Models\ProductColor::query()->orderBy('name')->get();
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
                            <div class="single-product-cover">
                              @php
                                $thumbUrl = $product?->thumbnail_url ?? asset('mantu/assets/img/product/27.jpg');
                              @endphp
                              <div class="single-slide zoom-image-hover">
                                <img
                                  class="img-responsive"
                                  src="{{ $thumbUrl }}"
                                  alt="{{ $product?->title ?? 'Product' }}"
                                />
                              </div>
                              <div class="single-slide zoom-image-hover">
                                <img
                                  class="img-responsive"
                                  src="{{ $thumbUrl }}"
                                  alt="{{ $product?->title ?? 'Product' }}"
                                />
                              </div>
                              <div class="single-slide zoom-image-hover">
                                <img
                                  class="img-responsive"
                                  src="{{ $thumbUrl }}"
                                  alt="{{ $product?->title ?? 'Product' }}"
                                />
                              </div>
                            </div>
                            <div class="single-nav-thumb">
                              <div class="single-slide">
                                <img
                                  class="img-responsive"
                                  src="{{ $thumbUrl }}"
                                  alt="{{ $product?->title ?? 'Product' }}"
                                />
                              </div>
                              <div class="single-slide">
                                <img
                                  class="img-responsive"
                                  src="{{ $thumbUrl }}"
                                  alt="{{ $product?->title ?? 'Product' }}"
                                />
                              </div>
                              <div class="single-slide">
                                <img
                                  class="img-responsive"
                                  src="{{ $thumbUrl }}"
                                  alt="{{ $product?->title ?? 'Product' }}"
                                />
                              </div>
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
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill grey"></i>
                              </div>
                              <span class="mn-read-review">
                                |&nbsp;&nbsp;<a href="#mn-spt-nav-review"
                                  >4.8 / 5.0 Rating</a
                                >
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
                                    class="btn btn-primary mn-btn-2 mn-add-cart"
                                  >
                                    <span>Add To Cart</span>
                                  </button>
                                </div>
                                <div class="mn-single-wishlist">
                                  <a
                                    href="javascript:void(0)"
                                    class="mn-btn-group wishlist mn-wishlist"
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
                      <div class="add-more-item">
                        <a href="javascript:void(0)" class="mn-btn-2"
                          ><span>+</span></a
                        >
                        <div class="add-more-img">
                          <img src="{{ asset('mantu/assets/img/product/1.jpg') }}" alt="product" />
                        </div>
                        <div class="add-more-info">
                          <h5>Honey Spiced Nuts</h5>
                          <span class="mn-pro-rating">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill grey"></i>
                            <i class="ri-star-fill grey"></i>
                          </span>
                          <span class="mn-price">
                            <span class="new-price">$32.00</span>
                            <span class="old-price">$45.00</span>
                          </span>
                        </div>
                      </div>
                      <div class="add-more-item">
                        <a href="javascript:void(0)" class="mn-btn-2"
                          ><span>+</span></a
                        >
                        <div class="add-more-img">
                          <img src="{{ asset('mantu/assets/img/product/31.jpg') }}" alt="product" />
                        </div>
                        <div class="add-more-info">
                          <h5>Dates Value Pouch</h5>
                          <span class="mn-pro-rating">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                          </span>
                          <span class="mn-price">
                            <span class="new-price">$56.00</span>
                            <span class="old-price">$60.00</span>
                          </span>
                        </div>
                      </div>
                      <div class="add-more-item">
                        <a href="javascript:void(0)" class="mn-btn-2"
                          ><span>+</span></a
                        >
                        <div class="add-more-img">
                          <img src="{{ asset('mantu/assets/img/product/17.jpg') }}" alt="product" />
                        </div>
                        <div class="add-more-info">
                          <h5>Graps Mix Snack</h5>
                          <span class="mn-pro-rating">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill grey"></i>
                            <i class="ri-star-fill grey"></i>
                            <i class="ri-star-fill grey"></i>
                          </span>
                          <span class="mn-price">
                            <span class="new-price">$28.00</span>
                            <span class="old-price">$35.00</span>
                          </span>
                        </div>
                      </div>
                      <div class="add-more-item">
                        <a href="javascript:void(0)" class="mn-btn-2"
                          ><span>+</span></a
                        >
                        <div class="add-more-img">
                          <img src="{{ asset('mantu/assets/img/product/35.jpg') }}" alt="product" />
                        </div>
                        <div class="add-more-info">
                          <h5>Roasted Almonds Pack</h5>
                          <span class="mn-pro-rating">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                          </span>
                          <span class="mn-price">
                            <span class="new-price">$16.00</span>
                            <span class="old-price">$23.00</span>
                          </span>
                        </div>
                      </div>
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
                              Reviews
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
                            <div class="mn-t-review-wrapper mt-0">
                              <div class="mn-t-review-item">
                                <div class="mn-t-review-avtar">
                                  <img src="{{ asset('mantu/assets/img/user/1.jpg') }}" alt="user" />
                                </div>
                                <div class="mn-t-review-content">
                                  <div class="mn-t-review-top">
                                    <div class="mn-t-review-name">
                                      Mariya Lykra
                                    </div>
                                    <div
                                      class="mn-t-review-rating mn-pro-rating"
                                    >
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill grey"></i>
                                    </div>
                                  </div>
                                  <div class="mn-t-review-bottom">
                                    <p>
                                      Lorem Ipsum is simply dummy text of the
                                      printing and typesetting industry. Lorem
                                      Ipsum has been the industry's standard
                                      dummy text ever since the 1500s, when an
                                      unknown printer took a galley of type and
                                      scrambled it to make a type specimen.
                                    </p>
                                  </div>
                                </div>
                              </div>
                              <div class="mn-t-review-item">
                                <div class="mn-t-review-avtar">
                                  <img src="{{ asset('mantu/assets/img/user/2.jpg') }}" alt="user" />
                                </div>
                                <div class="mn-t-review-content">
                                  <div class="mn-t-review-top">
                                    <div class="mn-t-review-name">
                                      Moris Willson
                                    </div>
                                    <div
                                      class="mn-t-review-rating mn-pro-rating"
                                    >
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill grey"></i>
                                      <i class="ri-star-fill grey"></i>
                                    </div>
                                  </div>
                                  <div class="mn-t-review-bottom">
                                    <p>
                                      Lorem Ipsum has been the industry's
                                      standard dummy text ever since the 1500s,
                                      when an unknown printer took a galley of
                                      type and scrambled it to make a type
                                      specimen.
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="mn-ratting-content">
                              <h3>Add a Review</h3>
                              <div class="mn-ratting-form">
                                <form action="#">
                                  <div class="mn-ratting-star">
                                    <span>Your rating:</span>
                                    <div
                                      class="mn-t-review-rating mn-pro-rating"
                                    >
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill grey"></i>
                                      <i class="ri-star-fill grey"></i>
                                      <i class="ri-star-fill grey"></i>
                                    </div>
                                  </div>
                                  <div class="mn-ratting-input">
                                    <input
                                      name="your-name"
                                      placeholder="Name"
                                      type="text"
                                    />
                                  </div>
                                  <div class="mn-ratting-input">
                                    <input
                                      name="your-email"
                                      placeholder="Email*"
                                      type="email"
                                      required
                                    />
                                  </div>
                                  <div class="mn-ratting-input form-submit">
                                    <textarea
                                      name="your-commemt"
                                      placeholder="Enter Your Comment"
                                    ></textarea>
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
                              data-min="0"
                              data-max="250"
                              data-step="10"
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
                              <a href="{{ route('ecommerce.shop-right-sidebar.html') }}"><span>{{ $tag }}</span></a>
                            @endforeach
                          @else
                            <a href="{{ route('ecommerce.shop-right-sidebar.html') }}"><span>Clothes</span></a>
                            <a href="{{ route('ecommerce.shop-right-sidebar.html') }}"><span>Shoes</span></a>
                            <a href="{{ route('ecommerce.shop-right-sidebar.html') }}"><span>Bags</span></a>
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
