<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	@include('partials.seo')

	<!-- Icon CSS -->
	<link href="{{ asset('mantu/assets/css/vendor/materialdesignicons.min.css') }}" rel="stylesheet">
	<link href="{{ asset('mantu/assets/css/vendor/remixicon.css') }}" rel="stylesheet">

	<!-- Vendor -->
	@yield('vendor-css')

	<!-- Main CSS -->
	<link id="mainCss" href="{{ asset('mantu/assets/css/style.css') }}" rel="stylesheet">
</head>

<body data-mn-mode="light">

	<main class="wrapper sb-default">

		<!-- Loader -->
		<div id="mn-overlay">
			<div class="loader">
				<img src="{{ asset('mantu/assets/img/logo/loader.png') }}" alt="loader">
				<span class="shape"></span>
			</div>
		</div>

		<!-- Sidebar -->
		<div class="mn-sidebar-overlay"></div>
		<div class="mn-sidebar">
			<div class="mn-sidebar-body">
				<button type="button" class="side-close" title="Close"></button>
				<ul class="mn-sb-list">
					@if (isset($ecommerceCategories) && $ecommerceCategories->isNotEmpty())
						@foreach ($ecommerceCategories as $level1)
							<li class="mn-sb-title condense">
								<a href="{{ route('ecommerce.products.index', ['category' => $level1->slug]) }}" class="text-reset">
									<span>{{ $level1->name }}</span>
								</a>
							</li>
							@foreach ($level1->children as $level2)
								<li class="mn-sb-item sb-drop-item">
									@if ($level2->children->isNotEmpty())
										<a href="javascript:void(0)" class="mn-drop-toggle">
											@if ($level2->icon_url)
												<img src="{{ $level2->icon_url }}" alt="{{ $level2->name }}">
											@endif
											<span class="condense">{{ $level2->name }}<i class="drop-arrow ri-arrow-down-s-line"></i></span>
										</a>
										<ul class="mn-sb-drop">
											@foreach ($level2->children as $level3)
												<li class="list">
													<a href="{{ route('ecommerce.products.index', ['category' => $level3->slug]) }}" class="mn-page-link drop">{{ $level3->name }}</a>
												</li>
											@endforeach
										</ul>
									@else
										<a href="{{ route('ecommerce.products.index', ['category' => $level2->slug]) }}" class="mn-drop-toggle">
											@if ($level2->icon_url)
												<img src="{{ $level2->icon_url }}" alt="{{ $level2->name }}">
											@endif
											<span class="condense">{{ $level2->name }}</span>
										</a>
									@endif
								</li>
							@endforeach
						@endforeach
					@else
						<li class="mn-sb-title condense">
							<a href="{{ route('ecommerce.products.index', ['category' => 'fashion']) }}" class="text-reset">
								<span>Fashion</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="javascript:void(0)" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/clothes-2.svg') }}" alt="clothes">
								<span class="condense">Clothes<i class="drop-arrow ri-arrow-down-s-line"></i></span>
							</a>
							<ul class="mn-sb-drop">
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 't-shirts']) }}" class="mn-page-link drop">T-shirts</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'shirts']) }}" class="mn-page-link drop">Shirts</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'gowns']) }}" class="mn-page-link drop">gowns</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'dresses']) }}" class="mn-page-link drop">Dresses</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'sharees']) }}" class="mn-page-link drop">sharees</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'jeans']) }}" class="mn-page-link drop">jeans</a>
								</li>
							</ul>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'shoes']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/shoes.svg') }}" alt="clothes">
								<span class="condense">Shoes</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'glasses']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/glasses.svg') }}" alt="glasses">
								<span class="condense">glasses</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="javascript:void(0)" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/bag.svg') }}" alt="bag">
								<span class="condense">Bags<i class="drop-arrow ri-arrow-down-s-line"></i></span>
							</a>
							<ul class="mn-sb-drop">
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'purse']) }}" class="mn-page-link drop">Purse</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'bags']) }}" class="mn-page-link drop">Bags</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'wallet']) }}" class="mn-page-link drop">wallet</a>
								</li>
							</ul>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'hat']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/hat.svg') }}" alt="hat">
								<span class="condense">Hat</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="javascript:void(0)" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/makeup.svg') }}" alt="makeup">
								<span class="condense">Makeup<i class="drop-arrow ri-arrow-down-s-line"></i></span>
							</a>
							<ul class="mn-sb-drop">
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'lipstick']) }}" class="mn-page-link drop">Lipstick</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'eye-liner']) }}" class="mn-page-link drop">eye liner</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'nail-paint']) }}" class="mn-page-link drop">nail paint</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'makeup-kit']) }}" class="mn-page-link drop">Makeup kit</a>
								</li>
							</ul>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="javascript:void(0)" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/cosmetics.svg') }}" alt="cosmetics">
								<span class="condense">Cosmetics<i class="drop-arrow ri-arrow-down-s-line"></i></span>
							</a>
							<ul class="mn-sb-drop">
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'shampoo']) }}" class="mn-page-link drop">Shampoo</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'face-wash']) }}" class="mn-page-link drop">face wash</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'body-wash']) }}" class="mn-page-link drop">body wash</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'sunscreen']) }}" class="mn-page-link drop">sunscreen</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'serum']) }}" class="mn-page-link drop">serum</a>
								</li>
							</ul>
						</li>
						<li class="mn-sb-title condense">
							<a href="{{ route('ecommerce.products.index', ['category' => 'bakery']) }}" class="text-reset">
								<span>Bakery</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="javascript:void(0)" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/cake.svg') }}" alt="Cake">
								<span class="condense">Cake<i class="drop-arrow ri-arrow-down-s-line"></i></span>
							</a>
							<ul class="mn-sb-drop">
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'cup-cake']) }}" class="mn-page-link drop">cup cake</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'pastry']) }}" class="mn-page-link drop">pastry</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'cake']) }}" class="mn-page-link drop">Cake</a>
								</li>
							</ul>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'bread']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/bread.svg') }}" alt="Bread">
								<span class="condense">Bread</span>
							</a>
						</li>
						<li class="mn-sb-title condense">
							<a href="{{ route('ecommerce.products.index', ['category' => 'vegetables']) }}" class="text-reset">
								<span>Vegetables</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="javascript:void(0)" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/tuber.svg') }}" alt="tuber">
								<span class="condense">tuber root<i class="drop-arrow ri-arrow-down-s-line"></i></span>
							</a>
							<ul class="mn-sb-drop">
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'sweet-potato']) }}" class="mn-page-link drop">Sweet Potato</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'ginger']) }}" class="mn-page-link drop">Ginger</a>
								</li>
								<li class="list">
									<a href="{{ route('ecommerce.products.index', ['category' => 'cassava']) }}" class="mn-page-link drop">cassava</a>
								</li>
							</ul>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'tomato']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/tomato.svg') }}" alt="tomato">
								<span class="condense">Tomato</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'lemon']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/lemon.svg') }}" alt="lemon">
								<span class="condense">Lemon</span>
							</a>
						</li>
						<li class="mn-sb-title condense">
							<a href="{{ route('ecommerce.products.index', ['category' => 'fruits']) }}" class="text-reset">
								<span>Fruits</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'avocado']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/avocado.svg') }}" alt="avocado">
								<span class="condense">avocado</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'strawberry']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/strawberry.svg') }}" alt="strawberry">
								<span class="condense">strawberry</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'cherry']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/cherry.svg') }}" alt="cherry">
								<span class="condense">cherry</span>
							</a>
						</li>
						<li class="mn-sb-item sb-drop-item">
							<a href="{{ route('ecommerce.products.index', ['category' => 'lychee']) }}" class="mn-drop-toggle">
								<img src="{{ asset('mantu/assets/img/icons/lychee.svg') }}" alt="Lychee">
								<span class="condense">Lychee</span>
							</a>
						</li>
					@endif
				</ul>
			</div>
		</div>

		<!-- Header -->
		<header>
			<div class="mn-header">
				<div class="mn-header-items">
					<div class="left-header">
						<a href="javascript:void(0)" class="mn-toggle-sidebar">
							<span class="outer-ring">
								<span class="inner-ring"></span>
							</span>
						</a>
						<a href="{{ route('ecommerce.index.html') }}" class="logo"><img src="{{ asset('mantu/assets/img/logo/logo.png') }}" alt="logo"></a>
						<a href="javascript:void(0)" class="mn-toggle-menu">
							<div class="header-icon">
								<i class="ri-menu-3-fill"></i>
							</div>
						</a>
					</div>
					<div class="right-header">
						<!-- Main Menu Start -->
						<div id="mn-main-menu-desk" class="d-none d-lg-block sticky-nav">
							<div class="nav-desk">
								<div class="row">
									<div class="col-md-12 align-self-center">
										<div class="mn-main-menu">
											<ul>
												<li class="non-drop">
													<a href="{{ route('ecommerce.index.html') }}">Home</a>
												</li>
												<li class="dropdown drop-list">
													<a href="javascript:void(0)" class="dropdown-arrow">Categories<i
															class="ri-arrow-down-s-line"></i></a>
													<ul class="mega-menu d-block">
														<li class="d-flex">
															<span class="bg"></span>
															<ul class="d-block mega-block">
																<li class="menu_title"><a
																		href="javascript:void(0)">Classic</a></li>
																<li><a href="{{ route('ecommerce.shop-right-sidebar.html') }}">Shop Right
																		sidebar</a></li>
																<li><a href="{{ route('ecommerce.shop-banner-right-sidebar.html') }}">Banner
																		Right sidebar</a></li>
																<li><a href="{{ route('ecommerce.shop-full-width.html') }}">Shop Full
																		Width</a></li>
																<li><a href="{{ route('ecommerce.shop-banner-full-width.html') }}">Banner Full
																		Width</a></li>
															</ul>
															<ul class="d-block mega-block">
																<li class="menu_title"><a
																		href="javascript:void(0)">List</a></li>
																<li><a href="{{ route('ecommerce.shop-list-right-sidebar.html') }}">Shop Right
																		sidebar</a></li>
																<li><a href="{{ route('ecommerce.shop-list-banner-right-sidebar.html') }}">Banner
																		Right sidebar</a></li>
																<li><a href="{{ route('ecommerce.shop-list-full-width.html') }}">Shop
																		Full Width</a></li>
																<li><a href="{{ route('ecommerce.shop-list-banner-full-width.html') }}">Banner
																		Full Width</a></li>
															</ul>
														</li>
													</ul>
												</li>
												<li class="dropdown drop-list">
													<a href="javascript:void(0)" class="dropdown-arrow">Products<i
															class="ri-arrow-down-s-line"></i></a>
													<ul class="sub-menu">
														<li><a href="{{ route('ecommerce.product-detail.html') }}">product detail</a></li>
														<li><a href="{{ route('ecommerce.product-accordion.html') }}">Product accordion</a></li>
														<li><a href="{{ route('ecommerce.product-full-width.html') }}">Product full width</a>
														</li>
														<li><a href="{{ route('ecommerce.product-accordion-full-width.html') }}">accordion full
																width</a></li>
													</ul>
												</li>
												<li class="dropdown drop-list">
													<a href="javascript:void(0)" class="dropdown-arrow">Pages<i
															class="ri-arrow-down-s-line"></i></a>
													<ul class="sub-menu">
														<li class="dropdown position-static">
															<a href="javascript:void(0)" class="mn-sub-drop">Blog
																<i class="ri-arrow-down-s-line"></i></a>
															<ul class="sub-menu sub-menu-child">
																<li><a href="{{ route('ecommerce.blog-right-sidebar.html') }}">right sidebar</a>
																</li>
																<li><a href="{{ route('ecommerce.blog-full-width.html') }}">Full Width</a></li>
																<li><a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Detail
																		right sidebar</a></li>
																<li><a href="{{ route('ecommerce.blog-detail-full-width.html') }}">Detail Full
																		Width</a></li>
															</ul>
														</li>
														<li><a href="{{ route('ecommerce.about-us.html') }}">About Us</a></li>
														<li><a href="{{ route('ecommerce.contact-us.html') }}">Contact Us</a></li>
														<li><a href="{{ route('ecommerce.cart.html') }}">Cart</a></li>
														<li><a href="{{ route('ecommerce.checkout.html') }}">Checkout</a></li>
														<li><a href="{{ route('ecommerce.compare.html') }}">Compare</a></li>
														<li><a href="{{ route('ecommerce.faq.html') }}">FAQ</a></li>
														<li><a href="{{ route('ecommerce.login.html') }}">Login</a></li>
													</ul>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mn-mobile-menu-overlay"></div>
						<div id="mn-mobile-menu" class="mn-mobile-menu">
							<div class="mn-menu-title">
								<span class="menu_title">My Menu</span>
								<button type="button" class="mn-close-menu">×</button>
							</div>
							<div class="mn-menu-inner">
								<div class="mn-menu-content">
									<ul>
										<li>
											<a href="{{ route('ecommerce.index.html') }}">Home</a>
										</li>
										<li>
											<a href="javascript:void(0)">Categories</a>
											<ul class="sub-menu">
												<li>
													<a href="javascript:void(0)">Classic</a>
													<ul class="sub-menu">
														<li><a href="{{ route('ecommerce.shop-right-sidebar.html') }}">Shop Right
																sidebar</a></li>
														<li><a href="{{ route('ecommerce.shop-banner-right-sidebar.html') }}">Banner
																Right sidebar</a></li>
														<li><a href="{{ route('ecommerce.shop-full-width.html') }}">Shop Full
																Width</a></li>
														<li><a href="{{ route('ecommerce.shop-banner-full-width.html') }}">Banner Full
																Width</a></li>
													</ul>
												</li>
												<li>
													<a href="javascript:void(0)">List</a>
													<ul class="sub-menu">
														<li><a href="{{ route('ecommerce.shop-list-right-sidebar.html') }}">Shop Right
																sidebar</a></li>
														<li><a href="{{ route('ecommerce.shop-list-banner-right-sidebar.html') }}">Banner
																Right sidebar</a></li>
														<li><a href="{{ route('ecommerce.shop-list-full-width.html') }}">Shop
																Full Width</a></li>
														<li><a href="{{ route('ecommerce.shop-list-banner-full-width.html') }}">Banner
																Full Width</a></li>
													</ul>
												</li>
											</ul>
										</li>
										<li>
											<a href="javascript:void(0)">Products</a>
											<ul class="sub-menu">
												<li><a href="{{ route('ecommerce.product-detail.html') }}">product detail</a></li>
												<li><a href="{{ route('ecommerce.product-accordion.html') }}">Product accordion</a></li>
												<li><a href="{{ route('ecommerce.product-full-width.html') }}">Product full width</a></li>
												<li><a href="{{ route('ecommerce.product-accordion-full-width.html') }}">accordion full width</a>
												</li>
											</ul>
										</li>
										<li>
											<a href="javascript:void(0)">Pages</a>
											<ul class="sub-menu">
												<li><a href="{{ route('ecommerce.about-us.html') }}">About Us</a></li>
												<li><a href="{{ route('ecommerce.contact-us.html') }}">Contact Us</a></li>
												<li><a href="{{ route('ecommerce.cart.html') }}">Cart</a></li>
												<li><a href="{{ route('ecommerce.checkout.html') }}">Checkout</a></li>
												<li><a href="{{ route('ecommerce.compare.html') }}">Compare</a></li>
												<li><a href="{{ route('ecommerce.faq.html') }}">Faq</a></li>
												<li><a href="{{ route('ecommerce.login.html') }}">Login</a></li>
												<li><a href="{{ route('ecommerce.register.html') }}">Register</a></li>
											</ul>
										</li>
										<li>
											<a href="javascript:void(0)">Blog</a>
											<ul class="sub-menu">
												<li><a href="{{ route('ecommerce.blog-right-sidebar.html') }}">Right Sidebar</a></li>
												<li><a href="{{ route('ecommerce.blog-full-width.html') }}">Full Width</a></li>
												<li><a href="{{ route('ecommerce.blog-detail-right-sidebar.html') }}">Detail Right Sidebar</a>
												</li>
												<li><a href="{{ route('ecommerce.blog-detail-full-width.html') }}">Detail Full Width</a></li>
											</ul>
										</li>
									</ul>
								</div>
								<div class="header-res-lan-curr">
									<!-- Social Start -->
									<div class="header-res-social">
										<div class="header-top-social">
											<ul class="mb-0">
												<li class="list-inline-item">
													<a href="#"><i class="ri-facebook-fill"></i></a>
												</li>
												<li class="list-inline-item">
													<a href="#"><i class="ri-twitter-fill"></i></a>
												</li>
												<li class="list-inline-item">
													<a href="#"><i class="ri-instagram-line"></i></a>
												</li>
												<li class="list-inline-item">
													<a href="#"><i class="ri-linkedin-fill"></i></a>
												</li>
											</ul>
										</div>
									</div>
									<!-- Social End -->
								</div>
							</div>
						</div>
						<!-- Main menu icons -->
						<div class="mn-tool-icons">
							<div class="mn-tool-search">
								<a href="javascript:void(0)" class="mn-main-search mn-search-toggle">
									<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" x="0"
										y="0" viewBox="0 0 612.01 612.01" style="enable-background:new 0 0 512 512"
										xml:space="preserve">
										<g>
											<path
												d="M606.209 578.714 448.198 423.228C489.576 378.272 515 318.817 515 253.393 514.98 113.439 399.704 0 257.493 0S.006 113.439.006 253.393s115.276 253.393 257.487 253.393c61.445 0 117.801-21.253 162.068-56.586l158.624 156.099c7.729 7.614 20.277 7.614 28.006 0a19.291 19.291 0 0 0 .018-27.585zM257.493 467.8c-120.326 0-217.869-95.993-217.869-214.407S137.167 38.986 257.493 38.986c120.327 0 217.869 95.993 217.869 214.407S377.82 467.8 257.493 467.8z"
												fill="#000000" opacity="1" data-original="#000000"></path>
										</g>
									</svg>
								</a>
							</div>
							<div class="mn-tool-user">
								<a href="javascript:void(0)" class="mn-main-user">
									<svg class="svg-icon" viewBox="0 0 1024 1024" version="1.1"
										xmlns="http://www.w3.org/2000/svg">
										<path
											d="M512.476 648.247c-170.169 0-308.118-136.411-308.118-304.681 0-168.271 137.949-304.681 308.118-304.681 170.169 0 308.119 136.411 308.119 304.681C820.594 511.837 682.645 648.247 512.476 648.247L512.476 648.247zM512.476 100.186c-135.713 0-246.12 109.178-246.12 243.381 0 134.202 110.407 243.381 246.12 243.381 135.719 0 246.126-109.179 246.126-243.381C758.602 209.364 648.195 100.186 512.476 100.186L512.476 100.186zM935.867 985.115l-26.164 0c-9.648 0-17.779-6.941-19.384-16.35-2.646-15.426-6.277-30.52-11.142-44.95-24.769-87.686-81.337-164.13-159.104-214.266-63.232 35.203-134.235 53.64-207.597 53.64-73.555 0-144.73-18.537-208.084-53.922-78 50.131-134.75 126.68-159.564 214.549 0 0-4.893 18.172-11.795 46.4-2.136 8.723-10.035 14.9-19.112 14.9L88.133 985.116c-9.415 0-16.693-8.214-15.47-17.452C91.698 824.084 181.099 702.474 305.51 637.615c58.682 40.472 129.996 64.267 206.966 64.267 76.799 0 147.968-23.684 206.584-63.991 124.123 64.932 213.281 186.403 232.277 329.772C952.56 976.901 945.287 985.115 935.867 985.115L935.867 985.115z">
										</path>
									</svg>
								</a>
								<ul class="sub-menu">
									@auth
										<li>
											<a href="{{ route('dashboard') }}">Dashboard ({{ Auth::user()->name }})</a>
										</li>
										<li>
											<form method="POST" action="{{ route('ecommerce.logout') }}" class="d-inline">
												@csrf
												<a href="javascript:void(0)" onclick="this.closest('form').submit()">Logout</a>
											</form>
										</li>
									@else
										<li>
											<a href="{{ route('ecommerce.login') }}">Login</a>
										</li>
										<li>
											<a href="{{ route('ecommerce.register') }}">Register</a>
										</li>
									@endauth
									<li>
										<a href="{{ route('ecommerce.checkout.html') }}">Checkout</a>
									</li>
								</ul>
							</div>
							<div class="mn-tool-wish">
								<a href="javascript:void(0)" class="mn-main-wishlist mn-wishlist-toggle">
									<span class="label lbl-1 wishlist-count">{{ count((array) session('ecommerce_wishlist', [])) }}</span>
									<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" x="0"
										y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512"
										xml:space="preserve">
										<g>
											<path
												d="M474.644 74.27C449.391 45.616 414.358 29.836 376 29.836c-53.948 0-88.103 32.22-107.255 59.25-4.969 7.014-9.196 14.047-12.745 20.665-3.549-6.618-7.775-13.651-12.745-20.665-19.152-27.03-53.307-59.25-107.255-59.25-38.358 0-73.391 15.781-98.645 44.435C13.267 101.605 0 138.213 0 177.351c0 42.603 16.633 82.228 52.345 124.7 31.917 37.96 77.834 77.088 131.005 122.397 19.813 16.884 40.302 34.344 62.115 53.429l.655.574c2.828 2.476 6.354 3.713 9.88 3.713s7.052-1.238 9.88-3.713l.655-.574c21.813-19.085 42.302-36.544 62.118-53.431 53.168-45.306 99.085-84.434 131.002-122.395C495.367 259.578 512 219.954 512 177.351c0-39.138-13.267-75.746-37.356-103.081zM309.193 401.614c-17.08 14.554-34.658 29.533-53.193 45.646-18.534-16.111-36.113-31.091-53.196-45.648C98.745 312.939 30 254.358 30 177.351c0-31.83 10.605-61.394 29.862-83.245C79.34 72.007 106.379 59.836 136 59.836c41.129 0 67.716 25.338 82.776 46.594 13.509 19.064 20.558 38.282 22.962 45.659a15 15 0 0 0 28.524 0c2.404-7.377 9.453-26.595 22.962-45.66 15.06-21.255 41.647-46.593 82.776-46.593 29.621 0 56.66 12.171 76.137 34.27C471.395 115.957 482 145.521 482 177.351c0 77.007-68.745 135.588-172.807 224.263z"
												fill="#000000" opacity="1" data-original="#000000"></path>
										</g>
									</svg>
								</a>
							</div>
							<div class="mn-tool-cart">
								<a href="javascript:void(0)" class="mn-main-cart mn-cart-toggle">
									<span class="label lbl-2 cart-count">{{ array_sum(array_column((array) session('ecommerce_cart', []), 'quantity')) }}</span>
									<svg class="svg-icon" viewBox="0 0 1024 1024" version="1.1"
										xmlns="http://www.w3.org/2000/svg">
										<path
											d="M351.552 831.424c-35.328 0-63.968 28.64-63.968 63.968 0 35.328 28.64 63.968 63.968 63.968 35.328 0 63.968-28.64 63.968-63.968C415.52 860.064 386.88 831.424 351.552 831.424L351.552 831.424 351.552 831.424zM799.296 831.424c-35.328 0-63.968 28.64-63.968 63.968 0 35.328 28.64 63.968 63.968 63.968 35.328 0 63.968-28.64 63.968-63.968C863.264 860.064 834.624 831.424 799.296 831.424L799.296 831.424 799.296 831.424zM862.752 799.456 343.264 799.456c-46.08 0-86.592-36.448-92.224-83.008L196.8 334.592 165.92 156.128c-1.92-15.584-16.128-28.288-29.984-28.288L95.2 127.84c-17.664 0-32-14.336-32-31.968 0-17.664 14.336-32 32-32l40.736 0c46.656 0 87.616 36.448 93.28 83.008l30.784 177.792 54.464 383.488c1.792 14.848 15.232 27.36 28.768 27.36l519.488 0c17.696 0 32 14.304 32 31.968S880.416 799.456 862.752 799.456L862.752 799.456zM383.232 671.52c-16.608 0-30.624-12.8-31.872-29.632-1.312-17.632 11.936-32.928 29.504-34.208l433.856-31.968c15.936-0.096 29.344-12.608 31.104-26.816l50.368-288.224c1.28-10.752-1.696-22.528-8.128-29.792-4.128-4.672-9.312-7.04-15.36-7.04L319.04 223.84c-17.664 0-32-14.336-32-31.968 0-17.664 14.336-31.968 32-31.968l553.728 0c24.448 0 46.88 10.144 63.232 28.608 18.688 21.088 27.264 50.784 23.52 81.568l-50.4 288.256c-5.44 44.832-45.92 81.28-92 81.28L385.6 671.424C384.8 671.488 384 671.52 383.232 671.52L383.232 671.52zM383.232 671.52">
										</path>
									</svg>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>

		<!-- Main Content -->
		@yield('content')

		<!-- Footer -->
		<footer class="m-t-15">
			<div class="mn-footer">
				<div class="footer-container">
					<div class="footer-top p-tb-30">
						<div class="row m-minus-991">
							<div class="col-sm-12 col-lg-3 mn-footer-cat">
								<div class="mn-footer-widget mn-footer-company">
									<img src="{{ asset('mantu/assets/img/logo/logo.png') }}" class="mn-footer-logo" alt="footer logo">
									<img src="{{ asset('mantu/assets/img/logo/logo-dark.png') }}" class="mn-footer-dark-logo"
										alt="footer logo">
									<p class="mn-footer-detail">The Mantu is the biggest market of grocery products. Get
										your daily needs from our store.</p>
									<div class="mn-app-store">
										<a href="javascript:void(0)" class="app-img">
											<img src="{{ asset('mantu/assets/img/footer/android.png') }}" class="adroid" alt="apple">
										</a>
										<a href="javascript:void(0)" class="app-img">
											<img src="{{ asset('mantu/assets/img/footer/apple.png') }}" class="apple" alt="apple">
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-lg-2 mn-footer-info">
								<div class="mn-footer-widget">
									<h4 class="mn-footer-heading">Category</h4>
									<div class="mn-footer-links mn-footer-dropdown">
										<ul class="align-items-center">
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.products.index', ['category' => 'fashion']) }}">Fashion</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.products.index', ['category' => 'cosmetics']) }}">Cosmetics</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.products.index', ['category' => 'bags']) }}">Bags & Purse</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.products.index', ['category' => 'shoes']) }}">Shoes</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.products.index', ['category' => 'hat']) }}">Hat & Accessories</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.products.index', ['category' => 'makeup']) }}">Makeup</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-lg-2 mn-footer-account">
								<div class="mn-footer-widget">
									<h4 class="mn-footer-heading">Company</h4>
									<div class="mn-footer-links mn-footer-dropdown">
										<ul class="align-items-center">
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.about-us.html') }}">About us</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.track-order.html') }}">Delivery</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.faq.html') }}">Legal Notice</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.terms.html') }}">Terms of use</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.checkout.html') }}">Secure payment</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.contact-us.html') }}">Contact us</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-lg-2 mn-footer-service">
								<div class="mn-footer-widget">
									<h4 class="mn-footer-heading">Account</h4>
									<div class="mn-footer-links mn-footer-dropdown">
										<ul class="align-items-center">
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.login.html') }}">Sign In</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.cart.html') }}">View Cart</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.faq.html') }}">Return Policy</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.shop-right-sidebar.html') }}">Become a Vendor</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.product-detail.html') }}">Affiliate Program</a>
											</li>
											<li class="mn-footer-link">
												<a href="{{ route('ecommerce.checkout.html') }}">Payments</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-lg-3 mn-footer-cont-social">
								<div class="mn-footer-contact">
									<div class="mn-footer-widget">
										<h4 class="mn-footer-heading">Contact</h4>
										<div class="mn-footer-links mn-footer-dropdown">
											<ul class="align-items-center">
												<li class="mn-footer-link mn-foo-location">
													<span class="mt-15px">
														<i class="ri-map-pin-line"></i>
													</span>
													<p>1234 Elm Street Springfield Avenue, Brooklyn den, IL 62704 USA.
													</p>
												</li>
												<li class="mn-footer-link mn-foo-call">
													<span>
														<i class="ri-whatsapp-line"></i>
													</span>
													<a href="tel:+009876543210">+00 9876543210</a>
												</li>
												<li class="mn-footer-link mn-foo-mail">
													<span>
														<i class="ri-mail-line"></i>
													</span>
													<a href="mailto:example@email.com">example@email.com</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="mn-footer-social">
									<div class="mn-footer-widget">
										<div class="mn-footer-links mn-footer-dropdown">
											<ul class="align-items-center">
												<li class="mn-footer-link">
													<a href="javascript:void(0)"><i class="ri-facebook-fill"></i></a>
												</li>
												<li class="mn-footer-link">
													<a href="javascript:void(0)"><i class="ri-twitter-fill"></i></a>
												</li>
												<li class="mn-footer-link">
													<a href="javascript:void(0)"><i class="ri-linkedin-fill"></i></a>
												</li>
												<li class="mn-footer-link">
													<a href="javascript:void(0)"><i class="ri-instagram-line"></i></a>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="footer-bottom">
						<div class="row">
							<div class="mn-bottom-info">
								<div class="footer-copy">
									<div class="footer-bottom-copy ">
										<div class="mn-copy">Copyright © <span id="copyright_year"></span>
											<a class="site-name" href="{{ route('ecommerce.index.html') }}">The Mantu</a> all rights reserved.
										</div>
									</div>
								</div>
								<div class="footer-bottom-right">
									<div class="footer-bottom-payment d-flex justify-content-center">
										<div class="payment-link">
											<img src="{{ asset('mantu/assets/img/footer/payment.png') }}" alt="payment">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>

		<!-- Footer Nav For Mobile -->
		<div class="mn-footer-nav">
			<ul>
				<li>
					<a href="javascript:void(0)" class="mn-main-search mn-search-toggle">
						<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" x="0" y="0"
							viewBox="0 0 612.01 612.01" style="enable-background:new 0 0 512 512" xml:space="preserve">
							<g>
								<path
									d="M606.209 578.714 448.198 423.228C489.576 378.272 515 318.817 515 253.393 514.98 113.439 399.704 0 257.493 0S.006 113.439.006 253.393s115.276 253.393 257.487 253.393c61.445 0 117.801-21.253 162.068-56.586l158.624 156.099c7.729 7.614 20.277 7.614 28.006 0a19.291 19.291 0 0 0 .018-27.585zM257.493 467.8c-120.326 0-217.869-95.993-217.869-214.407S137.167 38.986 257.493 38.986c120.327 0 217.869 95.993 217.869 214.407S377.82 467.8 257.493 467.8z"
									fill="#000000" opacity="1" data-original="#000000"></path>
							</g>
						</svg>
					</a>
				</li>
				<li>
					<a href="{{ route('ecommerce.login.html') }}" class="mn-main-user">
						<svg class="svg-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M512.476 648.247c-170.169 0-308.118-136.411-308.118-304.681 0-168.271 137.949-304.681 308.118-304.681 170.169 0 308.119 136.411 308.119 304.681C820.594 511.837 682.645 648.247 512.476 648.247L512.476 648.247zM512.476 100.186c-135.713 0-246.12 109.178-246.12 243.381 0 134.202 110.407 243.381 246.12 243.381 135.719 0 246.126-109.179 246.126-243.381C758.602 209.364 648.195 100.186 512.476 100.186L512.476 100.186zM935.867 985.115l-26.164 0c-9.648 0-17.779-6.941-19.384-16.35-2.646-15.426-6.277-30.52-11.142-44.95-24.769-87.686-81.337-164.13-159.104-214.266-63.232 35.203-134.235 53.64-207.597 53.64-73.555 0-144.73-18.537-208.084-53.922-78 50.131-134.75 126.68-159.564 214.549 0 0-4.893 18.172-11.795 46.4-2.136 8.723-10.035 14.9-19.112 14.9L88.133 985.116c-9.415 0-16.693-8.214-15.47-17.452C91.698 824.084 181.099 702.474 305.51 637.615c58.682 40.472 129.996 64.267 206.966 64.267 76.799 0 147.968-23.684 206.584-63.991 124.123 64.932 213.281 186.403 232.277 329.772C952.56 976.901 945.287 985.115 935.867 985.115L935.867 985.115z">
							</path>
						</svg>
					</a>
				</li>
				<li>
					<a href="{{ route('ecommerce.index.html') }}" class="mn-toggle-menu">
						<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" x="0" y="0"
							viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
							<g>
								<path
									d="M19 22.42H5c-2.07 0-3.75-1.68-3.75-3.75v-8.44c0-1.22.6-2.37 1.6-3.07l7-4.9c1.29-.9 3.02-.9 4.3 0l7 4.9c1 .7 1.6 1.85 1.6 3.07v8.44c0 2.07-1.68 3.75-3.75 3.75zM12 3.08c-.45 0-.9.14-1.29.41l-7 4.9c-.6.42-.96 1.11-.96 1.84v8.44c0 1.24 1.01 2.25 2.25 2.25h14c1.24 0 2.25-1.01 2.25-2.25v-8.44c0-.73-.36-1.42-.96-1.84l-7-4.9c-.39-.27-.84-.4-1.29-.4z"
									fill="#000000" opacity="1" data-original="#000000"></path>
								<path
									d="M16 22.42H8c-.41 0-.75-.34-.75-.75v-5c0-2.62 2.13-4.75 4.75-4.75s4.75 2.13 4.75 4.75v5c0 .41-.34.75-.75.75zm-7.25-1.5h6.5v-4.25c0-1.79-1.46-3.25-3.25-3.25s-3.25 1.46-3.25 3.25z"
									fill="#000000" opacity="1" data-original="#000000"></path>
							</g>
						</svg>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="mn-main-wishlist mn-wishlist-toggle">
						<span class="label lbl-1 wishlist-count">{{ count((array) session('ecommerce_wishlist', [])) }}</span>
						<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" x="0" y="0"
							viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve">
							<g>
								<path
									d="M474.644 74.27C449.391 45.616 414.358 29.836 376 29.836c-53.948 0-88.103 32.22-107.255 59.25-4.969 7.014-9.196 14.047-12.745 20.665-3.549-6.618-7.775-13.651-12.745-20.665-19.152-27.03-53.307-59.25-107.255-59.25-38.358 0-73.391 15.781-98.645 44.435C13.267 101.605 0 138.213 0 177.351c0 42.603 16.633 82.228 52.345 124.7 31.917 37.96 77.834 77.088 131.005 122.397 19.813 16.884 40.302 34.344 62.115 53.429l.655.574c2.828 2.476 6.354 3.713 9.88 3.713s7.052-1.238 9.88-3.713l.655-.574c21.813-19.085 42.302-36.544 62.118-53.431 53.168-45.306 99.085-84.434 131.002-122.395C495.367 259.578 512 219.954 512 177.351c0-39.138-13.267-75.746-37.356-103.081zM309.193 401.614c-17.08 14.554-34.658 29.533-53.193 45.646-18.534-16.111-36.113-31.091-53.196-45.648C98.745 312.939 30 254.358 30 177.351c0-31.83 10.605-61.394 29.862-83.245C79.34 72.007 106.379 59.836 136 59.836c41.129 0 67.716 25.338 82.776 46.594 13.509 19.064 20.558 38.282 22.962 45.659a15 15 0 0 0 28.524 0c2.404-7.377 9.453-26.595 22.962-45.66 15.06-21.255 41.647-46.593 82.776-46.593 29.621 0 56.66 12.171 76.137 34.27C471.395 115.957 482 145.521 482 177.351c0 77.007-68.745 135.588-172.807 224.263z"
									fill="#000000" opacity="1" data-original="#000000"></path>
							</g>
						</svg>
						<span>3</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="mn-main-cart mn-cart-toggle">
						<span class="label lbl-2 cart-count">{{ array_sum(array_column((array) session('ecommerce_cart', []), 'quantity')) }}</span>
						<svg class="svg-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M351.552 831.424c-35.328 0-63.968 28.64-63.968 63.968 0 35.328 28.64 63.968 63.968 63.968 35.328 0 63.968-28.64 63.968-63.968C415.52 860.064 386.88 831.424 351.552 831.424L351.552 831.424 351.552 831.424zM799.296 831.424c-35.328 0-63.968 28.64-63.968 63.968 0 35.328 28.64 63.968 63.968 63.968 35.328 0 63.968-28.64 63.968-63.968C863.264 860.064 834.624 831.424 799.296 831.424L799.296 831.424 799.296 831.424zM862.752 799.456 343.264 799.456c-46.08 0-86.592-36.448-92.224-83.008L196.8 334.592 165.92 156.128c-1.92-15.584-16.128-28.288-29.984-28.288L95.2 127.84c-17.664 0-32-14.336-32-31.968 0-17.664 14.336-32 32-32l40.736 0c46.656 0 87.616 36.448 93.28 83.008l30.784 177.792 54.464 383.488c1.792 14.848 15.232 27.36 28.768 27.36l519.488 0c17.696 0 32 14.304 32 31.968S880.416 799.456 862.752 799.456L862.752 799.456zM383.232 671.52c-16.608 0-30.624-12.8-31.872-29.632-1.312-17.632 11.936-32.928 29.504-34.208l433.856-31.968c15.936-0.096 29.344-12.608 31.104-26.816l50.368-288.224c1.28-10.752-1.696-22.528-8.128-29.792-4.128-4.672-9.312-7.04-15.36-7.04L319.04 223.84c-17.664 0-32-14.336-32-31.968 0-17.664 14.336-31.968 32-31.968l553.728 0c24.448 0 46.88 10.144 63.232 28.608 18.688 21.088 27.264 50.784 23.52 81.568l-50.4 288.256c-5.44 44.832-45.92 81.28-92 81.28L385.6 671.424C384.8 671.488 384 671.52 383.232 671.52L383.232 671.52zM383.232 671.52">
							</path>
						</svg>
						<span class="cart-count">{{ array_sum(array_column((array) session('ecommerce_cart', []), 'quantity')) }}</span>
					</a>
				</li>
			</ul>
		</div>

		<!-- Quick view Modal -->
		<div class="modal fade quickview-modal" id="quickview_modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<button type="button" class="qty-close" data-bs-dismiss="modal" aria-label="Close"
						title="Close"></button>
					<div class="modal-body">
						<div class="row mb-minus-24">
							<div class="col-md-5 col-sm-12 col-xs-12 mb-24">
								<div class="single-pro-img single-pro-img-no-sidebar">
									<div class="single-product-scroll">
										<div class="single-slide-quickview owl-carousel">
											<img class="img-responsive" src="{{ asset('mantu/assets/img/product/1.jpg') }}"
												alt="product-img-1">
											<img class="img-responsive" src="{{ asset('mantu/assets/img/product/2.jpg') }}"
												alt="product-img-1">
											<img class="img-responsive" src="{{ asset('mantu/assets/img/product/3.jpg') }}"
												alt="product-img-1">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-7 col-sm-12 col-xs-12 mb-24">
								<div class="quickview-pro-content">
									<h5 class="mn-quick-title">
										<a href="{{ route('ecommerce.product-detail.html') }}">Best cotton fabric women's half sleeve
											T-shirt white color.</a>
									</h5>
									<div class="mn-pro-rating">
										<i class="ri-star-fill"></i>
										<i class="ri-star-fill"></i>
										<i class="ri-star-fill"></i>
										<i class="ri-star-fill"></i>
										<i class="ri-star-fill grey"></i>
									</div>
									<div class="mn-quickview-desc">Lorem Ipsum is simply dummy text of the printing and
										typesetting industry. Lorem Ipsum has been the industry's standard dummy text
										ever
										since the 1900s.</div>
									<div class="mn-quickview-price">
										<span class="new-price">$50.00</span>
										<span class="old-price">$62.00</span>
									</div>
									<div class="mn-pro-variations">
										<ul>
											<li class="active">
												<a href="javascript:void(0)" class="mn-opt-sz"
													data-tooltip="Small">s</a>
											</li>
											<li>
												<a href="javascript:void(0)" class="mn-opt-sz"
													data-tooltip="Medium">m</a>
											</li>
											<li>
												<a href="javascript:void(0)" class="mn-opt-sz"
													data-tooltip="Large">l</a>
											</li>
											<li>
												<a href="javascript:void(0)" class="mn-opt-sz"
													data-tooltip="Extra Large">xl</a>
											</li>
										</ul>
									</div>
									<div class="mn-quickview-qty">
										<div class="qty-plus-minus">
											<input class="qty-input" type="text" name="mn-qtybtn" value="1">
										</div>
										<div class="mn-quickview-cart">
											<a href="{{ route('ecommerce.cart.html') }}" class="mn-btn-1">
												<span><i class="ri-shopping-bag-line"></i>Add To Cart</span>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Search Sidebar -->
		<div class="mn-side-search-overlay"></div>
		<div class="mn-side-search">
			<div class="mn-search-inner">
				<div class="mn-search-title">
					<span class="search_title">Search</span>
					<a href="javascript:void(0)" class="mn-search-close">
						<i class="ri-close-line"></i>
					</a>
				</div>
				<div class="mn-search">
					<form action="{{ route('ecommerce.products.index') }}" method="GET">
						<input type="text" name="q" placeholder="Search here..." value="{{ request('q') }}">
						<a href="javascript:void(0)" onclick="$(this).closest('form').submit();"><i class="ri-search-line"></i></a>
					</form>
				</div>
				<div class="mn-search-list">
					@php
						$randomProducts = $randomProducts ?? (\App\Models\Product::active()->with(['category', 'brand'])->inRandomOrder()->take(4)->get());
					@endphp
					<ul class="mn-search-pro-items">
						@forelse ($randomProducts as $randProd)
							<li class="search-sidebar-list">
								<a href="{{ route('ecommerce.products.detail', $randProd->slug) }}" class="mn-pro-img">
									<img src="{{ $randProd->thumbnail_url }}" alt="{{ $randProd->title }}">
								</a>
								<div class="mn-pro-content">
									<a href="{{ route('ecommerce.products.detail', $randProd->slug) }}" class="search-pro-title">{{ $randProd->title }}</a>
									@if ($randProd->category)
										<a href="{{ route('ecommerce.products.index', ['category' => $randProd->category->slug]) }}" class="search-cat">{{ $randProd->category->name }}</a>
									@endif
									<ul class="mn-ratings">
										<li><i class="ri-star-fill"></i></li>
										<li><i class="ri-star-fill"></i></li>
										<li><i class="ri-star-fill"></i></li>
										<li><i class="ri-star-fill"></i></li>
										<li><i class="ri-star-fill {{ $randProd->stock > 0 ? '' : 'grey' }}"></i></li>
									</ul>
									<span class="search-price">
										<span>Rp {{ number_format($randProd->final_price, 0, ',', '.') }}</span>
										<span class="stock">- {{ $randProd->stock > 0 ? $randProd->stock . ' in Stock' : 'Out of Stock' }}</span>
									</span>
									<a href="javascript:void(0)" class="search-remove-item">×</a>
								</div>
							</li>
						@empty
							<li class="p-3 text-muted text-center">No products available.</li>
						@endforelse
					</ul>
				</div>
				<div class="mn-search-also m-t-15">
					<div class="mn-search-title d-flex justify-content-between align-items-center">
						<span class="search_title">Recently searches</span>
						@php
							$recentSearches = array_filter((array) session('recent_searches', []));
						@endphp
						@if (! empty($recentSearches))
							<a href="javascript:void(0)" onclick="clearSearchHistory()" id="clear-recent-searches-btn" class="small text-muted" style="font-size: 11px; text-decoration: underline;">Clear</a>
						@endif
					</div>
					<ul id="recent-searches-list">
						@forelse ($recentSearches as $recentTerm)
							<li><a href="{{ route('ecommerce.products.index', ['q' => $recentTerm]) }}">{{ $recentTerm }}</a></li>
						@empty
							<li class="text-muted small">No recent searches</li>
						@endforelse
					</ul>
				</div>
			</div>
		</div>

		<!-- Cart sidebar Start -->
		<div class="mn-side-cart-overlay"></div>
		<div id="mn-side-cart" class="mn-side-cart">
			<div class="mn-cart-inner">
				<div class="mn-cart-top">
					<div class="mn-cart-title">
						<span class="cart_title">My Cart</span>
						<a href="javascript:void(0)" class="mn-cart-close">
							<i class="ri-close-line"></i>
						</a>
					</div>
					<ul class="mn-cart-pro-items">
						@php
							$layoutCart = (array) session('ecommerce_cart', []);
							$layoutCartSubtotal = array_sum(array_map(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1), $layoutCart));
						@endphp
						@include('ecommerce.partials.cart-sidebar-items', ['cartItems' => $layoutCart])
					</ul>
				</div>
				<div class="mn-cart-bottom">
					<div class="cart-sub-total">
						<table class="table cart-table">
							<tbody>
								<tr>
									<td class="text-left">Sub-Total :</td>
									<td class="text-right cart-subtotal-val">Rp {{ number_format($layoutCartSubtotal, 0, ',', '.') }}</td>
								</tr>
								<tr>
									<td class="text-left">Total :</td>
									<td class="text-right primary-color cart-total-val">Rp {{ number_format($layoutCartSubtotal, 0, ',', '.') }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="cart_btn">
						<a href="{{ route('ecommerce.cart') }}" class="mn-btn-1"><span>Cart<i class="ri-arrow-right-s-line"></i></span></a>
						<a href="{{ route('ecommerce.checkout.html') }}" class="mn-btn-2"><span>Checkout<i
									class="ri-arrow-right-s-line"></i></span></a>
					</div>
				</div>
			</div>
		</div>

		<!-- Wishlist sidebar Start -->
		<div class="mn-side-wishlist-overlay"></div>
		<div id="mn-side-wishlist" class="mn-side-wishlist">
			<div class="mn-wishlist-inner">
				<div class="mn-wishlist-top">
					<div class="mn-wishlist-title">
						<span class="wishlist_title">My Wishlist</span>
						<a href="javascript:void(0)" class="mn-wishlist-close">
							<i class="ri-close-line"></i>
						</a>
					</div>
					<ul class="mn-wishlist-pro-items">
						@php
							$layoutWishlistIds = array_map('intval', (array) session('ecommerce_wishlist', []));
							$layoutWishlistProducts = !empty($layoutWishlistIds)
								? \App\Models\Product::query()->active()->whereIn('id', $layoutWishlistIds)->get()
								: collect();
						@endphp
						@include('ecommerce.partials.wishlist-sidebar-items', ['wishlistProducts' => $layoutWishlistProducts])
					</ul>
				</div>
				<div class="mn-wishlist-bottom">
					<div class="wishlist_btn">
						<a href="{{ route('ecommerce.wishlist') }}" class="mn-btn-1"><span>View Wishlist<i
									class="ri-arrow-right-s-line"></i></span></a>
					</div>
				</div>
			</div>
		</div>
	</main>

	<!-- Vendor Custom -->
	@yield('vendor-js')

	<!-- Main Custom -->
	<script src="{{ asset('mantu/assets/js/main.js') }}"></script>

	<script>
		function clearSearchHistory() {
			fetch('{{ route("ecommerce.recent-searches.clear") }}', {
				method: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}',
					'Accept': 'application/json'
				}
			}).then(function() {
				var list = document.getElementById('recent-searches-list');
				if (list) {
					list.innerHTML = '<li class="text-muted small">No recent searches</li>';
				}
				var btn = document.getElementById('clear-recent-searches-btn');
				if (btn) {
					btn.remove();
				}
			});
		}
	</script>

	@stack('scripts')
</body>

</html>