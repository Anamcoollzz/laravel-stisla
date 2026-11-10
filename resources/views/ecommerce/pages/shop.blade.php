@extends('ecommerce.layouts.app-ecommerce')

@section('title', ($currentCategory ? $currentCategory->name . ' - ' : '') . ' - Ecommerce')

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
								<h2 class="mn-breadcrumb-title">{{ $currentCategory ? $currentCategory->name : 'Shop Page' }}</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<!-- mn-breadcrumb-list start -->
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									@if ($currentCategory)
										<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.products.index') }}">Shop Page</a></li>
										<li class="mn-breadcrumb-item active">{{ $currentCategory->name }}</li>
									@else
										<li class="mn-breadcrumb-item active">Shop Page</li>
									@endif
								</ul>
								<!-- mn-breadcrumb-list end -->
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xxl-12">
					<!-- Shop section -->
					<section class="mn-shop padding-tb-30">
						<div class="row">
							<div class="mn-shop-rightside col-lg-9 col-md-12">
								<!-- Shop Top Start -->
								<div class="mn-pro-list-top d-flex">
									<div class="col-md-6 mn-grid-list">
										<div class="mn-gl-btn">
											<button class="grid-btn btn-grid active">
												<i class="ri-gallery-view-2"></i>
											</button>
											<button class="grid-btn btn-list">
												<i class="ri-list-check-2"></i>
											</button>
										</div>
									</div>
									<div class="col-md-6 mn-sort-select">
										<div class="mn-select-inner">
											<select name="mn-select" id="mn-select" onchange="const u = new URL(window.location.href); u.searchParams.set('sort', this.value); u.searchParams.delete('page'); window.location.href = u.toString();">
												<option {{ empty($sort) || $sort === 'latest' ? 'selected' : '' }} value="latest">Sort by: Latest</option>
												<option {{ $sort === 'name_asc' ? 'selected' : '' }} value="name_asc">Name, A to Z</option>
												<option {{ $sort === 'name_desc' ? 'selected' : '' }} value="name_desc">Name, Z to A</option>
												<option {{ $sort === 'price_low' ? 'selected' : '' }} value="price_low">Price, low to high</option>
												<option {{ $sort === 'price_high' ? 'selected' : '' }} value="price_high">Price, high to low</option>
											</select>
										</div>
									</div>
								</div>
								<!-- Shop Top End -->

								<!-- Select Bar Start -->
								@if (request()->hasAny(['category', 'brand', 'size', 'color', 'q']))
									<div class="mn-select-bar d-flex">
										@if ($currentCategory)
											<span class="mn-select-btn">Category: {{ $currentCategory->name }}<a class="mn-select-cancel"
													href="{{ request()->fullUrlWithoutQuery(['category', 'page']) }}">×</a></span>
										@elseif (request('category'))
											<span class="mn-select-btn">Category: {{ ucfirst(request('category')) }}<a class="mn-select-cancel"
													href="{{ request()->fullUrlWithoutQuery(['category', 'page']) }}">×</a></span>
										@endif

										@if (request('brand'))
											@php
												$activeBrand = $brands->firstWhere('slug', request('brand')) ?? (is_numeric(request('brand')) ? $brands->firstWhere('id', (int) request('brand')) : null);
											@endphp
											<span class="mn-select-btn">Brand: {{ $activeBrand?->name ?? request('brand') }}<a class="mn-select-cancel"
													href="{{ request()->fullUrlWithoutQuery(['brand', 'page']) }}">×</a></span>
										@endif

										@if (request('size'))
											@php
												$activeSize = (is_numeric(request('size')) ? $sizes->firstWhere('id', (int) request('size')) : null) ?? $sizes->firstWhere('code', request('size')) ?? $sizes->firstWhere('name', request('size'));
											@endphp
											<span class="mn-select-btn">Size: {{ strtoupper($activeSize?->code ?: $activeSize?->name ?: request('size')) }}<a class="mn-select-cancel"
													href="{{ request()->fullUrlWithoutQuery(['size', 'page']) }}">×</a></span>
										@endif

										@if (request('color'))
											@php
												$activeColor = (is_numeric(request('color')) ? $colors->firstWhere('id', (int) request('color')) : null) ?? $colors->firstWhere('name', request('color'));
											@endphp
											<span class="mn-select-btn">Color: {{ $activeColor?->name ?? request('color') }}<a class="mn-select-cancel"
													href="{{ request()->fullUrlWithoutQuery(['color', 'page']) }}">×</a></span>
										@endif

										@if (request('q'))
											<span class="mn-select-btn">Keyword: "{{ request('q') }}"<a class="mn-select-cancel"
													href="{{ request()->fullUrlWithoutQuery(['q', 'page']) }}">×</a></span>
										@endif

										<span class="mn-select-btn mn-select-btn-clear"><a class="mn-select-clear"
												href="{{ route('ecommerce.products.index') }}">Clear All</a></span>
									</div>
								@endif
								<!-- Select Bar End -->

								<!-- Shop content Start -->
								<div class="shop-pro-content">
									<div class="shop-pro-inner">
										<div class="row">
											@forelse ($products as $product)
												<div
													class="col-md-4 col-sm-6 col-xs-6 m-b-24 mn-product-box pro-gl-content">
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
																	<img class="main-img" src="{{ $product->thumbnail_url }}"
																		alt="{{ $product->title }}">
																	<img class="hover-img" src="{{ $product->images->first()?->image_url ?? $product->thumbnail_url }}"
																		alt="{{ $product->title }}">
																</a>
																<div class="mn-pro-loader"></div>
																<div class="mn-options">
																	<ul>
																		<li><a href="{{ route('ecommerce.products.detail', $product->slug) }}" data-tooltip
																				title="Quick View"><i
																					class="ri-eye-line"></i></a></li>
																		<li><a href="javascript:void(0)" data-tooltip
																				title="Compare" class="mn-compare"><i
																					class="ri-repeat-line"></i></a></li>
																		<li><a href="javascript:void(0)" data-tooltip
																				title="Add To Cart" class="mn-add-cart"><i
																					class="ri-shopping-cart-line"></i></a>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="mn-product-detail">
															<div class="cat">
																@if ($product->category)
																	<a href="{{ route('ecommerce.products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
																@else
																	<a href="{{ route('ecommerce.products.index') }}">Product</a>
																@endif
																@if ($product->size)
																	<ul>
																		<li>{{ strtolower($product->size->code ?: $product->size->name) }}</li>
																	</ul>
																@endif
															</div>
															<h5><a href="{{ route('ecommerce.products.detail', $product->slug) }}">{{ $product->title }}</a></h5>
															<p class="mn-info">{{ Str::limit($product->short_description ?: $product->description_detail, 120) }}</p>
															<div class="mn-price">
																<div class="mn-price-new">Rp {{ number_format($product->final_price, 0, ',', '.') }}</div>
																@if ($product->discount_percent > 0)
																	<div class="mn-price-old">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
																@endif
															</div>
															<div class="mn-pro-option">
																@if ($product->color)
																	<div class="mn-pro-color">
																		<ul class="mn-opt-swatch mn-change-img">
																			<li class="active"><a href="javascript:void(0)" class="mn-opt-clr-img"
																					data-src="{{ $product->thumbnail_url }}"
																					data-src-hover="{{ $product->images->first()?->image_url ?? $product->thumbnail_url }}"
																					data-tooltip="{{ $product->color->name }}"><span
																						style="background-color:{{ $product->color->color_code ?? '#333' }};"></span></a>
																			</li>
																		</ul>
																	</div>
																@endif
																@php
																	$isWishlisted = in_array((int) $product->id, (array) session('ecommerce_wishlist', []), true);
																@endphp
																<a href="javascript:void(0);"
																	class="mn-wishlist {{ $isWishlisted ? 'active' : '' }}"
																	data-product-id="{{ $product->id }}"
																	data-url="{{ route('ecommerce.wishlist.toggle', $product->id) }}"
																	data-tooltip
																	title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
																	<i class="{{ $isWishlisted ? 'ri-heart-fill' : 'ri-heart-line' }}"></i>
																</a>
															</div>
														</div>
													</div>
												</div>
											@empty
												<div class="col-12 py-5 text-center">
													<div class="m-b-15">
														<i class="ri-shopping-bag-3-line" style="font-size: 54px; color: #adb5bd;"></i>
													</div>
													<h4>No products found</h4>
													<p class="text-muted">No products match your selected category or filters.</p>
													<div class="m-t-15">
														<a href="{{ route('ecommerce.products.index') }}" class="btn btn-primary btn-sm">View All Products</a>
													</div>
												</div>
											@endforelse
										</div>
									</div>
									<!-- Pagination Start -->
									<div class="mn-pro-pagination m-b-15">
										<span>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} item(s)</span>
										@if ($products->hasPages())
											<ul class="mn-pro-pagination-inner">
												@if ($products->onFirstPage())
													<li class="disabled"><span><i class="ri-arrow-left-double-line"></i></span></li>
												@else
													<li><a href="{{ $products->previousPageUrl() }}"><i class="ri-arrow-left-double-line"></i></a></li>
												@endif

												@foreach ($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
													@if ($page == $products->currentPage())
														<li><a class="active" href="javascript:void(0)">{{ $page }}</a></li>
													@else
														<li><a href="{{ $url }}">{{ $page }}</a></li>
													@endif
												@endforeach

												@if ($products->hasMorePages())
													<li><a class="next" href="{{ $products->nextPageUrl() }}">Next <i
																class="ri-arrow-right-double-line"></i></a>
													</li>
												@else
													<li class="disabled"><span>Next <i class="ri-arrow-right-double-line"></i></span></li>
												@endif
											</ul>
										@endif
									</div>
									<!-- Pagination End -->
								</div>
								<!--Shop content End -->

							</div>
							<!-- Sidebar Area Start -->
							<div class="mn-shop-sidebar col-lg-3 col-md-12 m-t-991">
								<div id="shop_sidebar">
									<div class="mn-sidebar-wrap">
										<!-- Sidebar Filters Block -->
										<div class="mn-sidebar-block drop">
											<div class="mn-sb-title">
												<h3 class="mn-sidebar-title">Filters</h3>
											</div>
											<div class="mn-sb-block-content p-t-15">
												<ul>
													<li>
														<a href="{{ route('ecommerce.products.index') }}"
															class="mn-sidebar-block-item main {{ !$currentCategory ? 'active' : '' }}">All Categories<span>-{{ \App\Models\Product::active()->count() }}</span></a>
													</li>
												</ul>
											</div>
											@foreach ($categories as $cat)
												<div class="mn-sb-block-content">
													<ul>
														<li>
															@if ($cat->children->isNotEmpty())
																@php
																	$isCatExpanded = $currentCategory && ($currentCategory->id === $cat->id || $currentCategory->parent_id === $cat->id);
																@endphp
																<a href="javascript:void(0)"
																	class="mn-sidebar-block-item main drop {{ $isCatExpanded ? 'active' : '' }}">{{ $cat->name }}</a>
																<ul style="{{ $isCatExpanded ? 'display: block;' : '' }}">
																	<li>
																		<div class="mn-sidebar-sub-item">
																			<a href="{{ route('ecommerce.products.index', ['category' => $cat->slug]) }}"
																				class="{{ $currentCategory?->id === $cat->id ? 'active' : '' }}">All {{ $cat->name }}
																				<span>-{{ $cat->products_count }}</span>
																			</a>
																		</div>
																	</li>
																	@foreach ($cat->children as $child)
																		<li>
																			<div class="mn-sidebar-sub-item">
																				<a href="{{ route('ecommerce.products.index', ['category' => $child->slug]) }}"
																					class="{{ $currentCategory?->id === $child->id ? 'active' : '' }}">{{ $child->name }}
																					<span>-{{ $child->products_count }}</span>
																				</a>
																			</div>
																		</li>
																	@endforeach
																</ul>
															@else
																<a href="{{ route('ecommerce.products.index', ['category' => $cat->slug]) }}"
																	class="mn-sidebar-block-item main {{ $currentCategory?->id === $cat->id ? 'active' : '' }}">{{ $cat->name }}<span>-{{ $cat->products_count }}</span></a>
															@endif
														</li>
													</ul>
												</div>
											@endforeach
										</div>
										<!-- Sidebar Brand Block -->
										@if ($brands->isNotEmpty())
											<div class="mn-sidebar-block">
												<div class="mn-sb-title">
													<h3 class="mn-sidebar-title">Brand</h3>
												</div>
												<div class="mn-sb-block-content">
													<ul>
														@foreach ($brands as $brand)
															@php
																$isBrandActive = request('brand') == $brand->slug || (is_numeric(request('brand')) && (int) request('brand') == $brand->id);
																$brandUrl = $isBrandActive ? request()->fullUrlWithoutQuery(['brand', 'page']) : request()->fullUrlWithQuery(['brand' => $brand->slug, 'page' => null]);
															@endphp
															<li>
																<div class="mn-sidebar-block-item">
																	<input type="checkbox" {{ $isBrandActive ? 'checked' : '' }} onchange="window.location.href='{{ $brandUrl }}'">
																	<a href="{{ $brandUrl }}">
																		<span>{{ $brand->name }}</span>
																	</a>
																	<span class="checked"></span>
																</div>
															</li>
														@endforeach
													</ul>
												</div>
											</div>
										@endif
										<!-- Sidebar Weight/Size Block -->
										@if ($sizes->isNotEmpty())
											<div class="mn-sidebar-block">
												<div class="mn-sb-title">
													<h3 class="mn-sidebar-title">Size</h3>
												</div>
												<div class="mn-sb-block-content">
													<ul>
														@foreach ($sizes as $size)
															@php
																$isSizeActive = (string) request('size') === (string) $size->id;
																$sizeUrl = $isSizeActive ? request()->fullUrlWithoutQuery(['size', 'page']) : request()->fullUrlWithQuery(['size' => $size->id, 'page' => null]);
															@endphp
															<li>
																<div class="mn-sidebar-block-item">
																	<input type="checkbox" value="{{ $size->id }}" {{ $isSizeActive ? 'checked' : '' }} onchange="window.location.href='{{ $sizeUrl }}'">
																	<a href="{{ $sizeUrl }}">{{ strtoupper($size->code ?: $size->name) }} - Size</a>
																	<span class="checked"></span>
																</div>
															</li>
														@endforeach
													</ul>
												</div>
											</div>
										@endif
										<!-- Sidebar Color item -->
										@if ($colors->isNotEmpty())
											<div class="mn-sidebar-block color-block mn-sidebar-block-clr">
												<div class="mn-sb-title">
													<h3 class="mn-sidebar-title">Color</h3>
												</div>
												<div class="mn-sb-block-content">
													<ul>
														@foreach ($colors as $color)
															@php
																$isColorActive = (string) request('color') === (string) $color->id;
																$colorUrl = $isColorActive ? request()->fullUrlWithoutQuery(['color', 'page']) : request()->fullUrlWithQuery(['color' => $color->id, 'page' => null]);
															@endphp
															<li class="{{ $isColorActive ? 'active' : '' }}">
																<div class="mn-sidebar-block-item">
																	<input type="checkbox" value="{{ $color->id }}" {{ $isColorActive ? 'checked' : '' }} onchange="window.location.href='{{ $colorUrl }}'">
																	<span class="mn-clr-block"
																		style="background-color:{{ $color->color_code ?? '#333' }};" data-tooltip="{{ $color->name }}" title="{{ $color->name }}"></span>
																	<span class="checked"></span>
																</div>
															</li>
														@endforeach
													</ul>
												</div>
											</div>
										@endif
										<!-- Sidebar Price Block -->
										<div class="mn-sidebar-block">
											<div class="mn-sb-title">
												<h3 class="mn-sidebar-title">Price</h3>
											</div>
											<div class="mn-sb-block-content mn-price-range-slider es-price-slider">
												<div class="mn-price-filter">
													<div class="mn-price-input">
														<label class="filter__label">
															From<input type="text" class="filter__input">
														</label>
														<span class="mn-price-divider"></span>
														<label class="filter__label">
															To<input type="text" class="filter__input">
														</label>
													</div>
													<div id="mn-sliderPrice" class="filter__slider-price" data-min="0"
														data-max="250" data-step="10"></div>
												</div>
											</div>
										</div>
										<!-- Sidebar tags -->
										<div class="mn-sidebar-block">
											<div class="mn-sb-title">
												<h3 class="mn-sidebar-title">Tags</h3>
											</div>
											<div class="mn-tag-block mn-sb-block-content">
												@foreach ($categories as $cat)
													<a href="{{ route('ecommerce.products.index', ['category' => $cat->slug]) }}"><span>{{ $cat->name }}</span></a>
												@endforeach
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
	document.getElementById('mn-select')?.addEventListener('change', function () {
		const url = new URL(window.location.href);
		url.searchParams.set('sort', this.value);
		url.searchParams.delete('page');
		window.location.href = url.toString();
	});
</script>
@endpush
