<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'News HTML-5 Template')</title>
    <meta name="description" content="@yield('meta_description', '')">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('news-assets/img/favicon.ico') }}">

    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('news-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/ticker-style.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/slicknav.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('news-assets/css/style.css') }}">
    @stack('css')
</head>

<body>
<!-- Preloader Start -->
<div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
            <div class="preloader-circle"></div>
            <div class="preloader-img pere-text">
                <img src="{{ asset('news-assets/img/logo/logo.png') }}" alt="">
            </div>
        </div>
    </div>
</div>
<!-- Preloader End -->

@include('news.partials.header')

<main>
    @yield('content')
</main>

@include('news.partials.footer')

<!-- Search model Begin -->
<div class="search-model-box">
    <div class="d-flex align-items-center h-100 justify-content-center">
        <div class="search-close-btn">+</div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Searching key.....">
        </form>
    </div>
</div>
<!-- Search model end -->

<!-- JS here -->
<script src="{{ asset('news-assets/js/vendor/modernizr-3.5.0.min.js') }}"></script>
<!-- Jquery, Popper, Bootstrap -->
<script src="{{ asset('news-assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
<script src="{{ asset('news-assets/js/popper.min.js') }}"></script>
<script src="{{ asset('news-assets/js/bootstrap.min.js') }}"></script>
<!-- Jquery Mobile Menu -->
<script src="{{ asset('news-assets/js/jquery.slicknav.min.js') }}"></script>

<!-- Jquery Slick , Owl-Carousel Plugins -->
<script src="{{ asset('news-assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('news-assets/js/slick.min.js') }}"></script>
<!-- Date Picker -->
<script src="{{ asset('news-assets/js/gijgo.min.js') }}"></script>
<!-- One Page, Animated-HeadLin -->
<script src="{{ asset('news-assets/js/wow.min.js') }}"></script>
<script src="{{ asset('news-assets/js/animated.headline.js') }}"></script>
<script src="{{ asset('news-assets/js/jquery.magnific-popup.js') }}"></script>

<!-- Scrollup, nice-select, sticky -->
<script src="{{ asset('news-assets/js/jquery.scrollUp.min.js') }}"></script>
<script src="{{ asset('news-assets/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('news-assets/js/jquery.sticky.js') }}"></script>

<!-- contact js -->
<script src="{{ asset('news-assets/js/contact.js') }}"></script>
<script src="{{ asset('news-assets/js/jquery.form.js') }}"></script>
<script src="{{ asset('news-assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('news-assets/js/mail-script.js') }}"></script>
<script src="{{ asset('news-assets/js/jquery.ajaxchimp.min.js') }}"></script>

<!-- Jquery Plugins, main Jquery -->
<script src="{{ asset('news-assets/js/plugins.js') }}"></script>
<script src="{{ asset('news-assets/js/main.js') }}"></script>
@stack('js')

</body>
</html>
