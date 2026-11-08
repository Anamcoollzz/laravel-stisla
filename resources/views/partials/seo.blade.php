@php
  $seoCustom = [];

  $yieldedTitle = trim($__env->yieldContent('title'));
  $appName = \App\Models\Setting::getValue('site_title') ?: config('app.name', 'Stisla');
  $defaultMetaTitle = \App\Models\Setting::getValue('meta_title') ?: $appName;

  if ($yieldedTitle !== '') {
      if ($yieldedTitle === 'Ecommerce') {
          $seoCustom['title'] = $defaultMetaTitle;
      } elseif (! str_contains($yieldedTitle, $appName)) {
          $seoCustom['title'] = $yieldedTitle . ' — ' . $appName;
      } else {
          $seoCustom['title'] = $yieldedTitle;
      }
  }

  if ($desc = trim($__env->yieldContent('meta_description'))) {
      $seoCustom['description'] = $desc;
  }
  if ($kw = trim($__env->yieldContent('meta_keywords'))) {
      $seoCustom['keywords'] = $kw;
  }
  if ($author = trim($__env->yieldContent('meta_author'))) {
      $seoCustom['author'] = $author;
  }
  if ($robots = trim($__env->yieldContent('meta_robots'))) {
      $seoCustom['robots'] = $robots;
  }
  if ($canon = trim($__env->yieldContent('canonical_url', $__env->yieldContent('canonical')))) {
      $seoCustom['canonical'] = $canon;
  }
  if ($img = trim($__env->yieldContent('meta_image', $__env->yieldContent('og_image')))) {
      $seoCustom['image'] = $img;
  }
  if ($ogTitle = trim($__env->yieldContent('og_title'))) {
      $seoCustom['og_title'] = $ogTitle;
  }
  if ($ogDesc = trim($__env->yieldContent('og_description'))) {
      $seoCustom['og_description'] = $ogDesc;
  }
  if ($ogType = trim($__env->yieldContent('og_type'))) {
      $seoCustom['og_type'] = $ogType;
  }

  $seo = \App\Models\Setting::getSeoMeta($seoCustom);
@endphp

<title>{{ $seo['title'] }}</title>

<!-- Standard SEO Meta Tags -->
<meta name="description" content="{{ $seo['description'] }}">
<meta name="keywords" content="{{ $seo['keywords'] }}">
<meta name="author" content="{{ $seo['author'] }}">
<meta name="robots" content="{{ $seo['robots'] }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">

@if (!empty($seo['google_site_verification']))
  <meta name="google-site-verification" content="{{ $seo['google_site_verification'] }}">
@endif
@if (!empty($seo['bing_site_verification']))
  <meta name="msvalidate.01" content="{{ $seo['bing_site_verification'] }}">
@endif

<!-- Favicon -->
<link rel="shortcut icon" href="{{ $seo['favicon'] }}">
<link rel="icon" type="image/png" href="{{ $seo['favicon'] }}">
<link rel="apple-touch-icon" href="{{ $seo['favicon'] }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $seo['og_type'] }}">
<meta property="og:site_name" content="{{ $seo['app_name'] }}">
<meta property="og:title" content="{{ $seo['og_title'] }}">
<meta property="og:description" content="{{ $seo['og_description'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
@if (!empty($seo['og_image']))
  <meta property="og:image" content="{{ $seo['og_image'] }}">
@endif

<!-- Twitter -->
<meta name="twitter:card" content="{{ $seo['twitter_card'] }}">
@if (!empty($seo['twitter_site']))
  <meta name="twitter:site" content="{{ $seo['twitter_site'] }}">
  <meta name="twitter:creator" content="{{ $seo['twitter_site'] }}">
@endif
<meta name="twitter:title" content="{{ $seo['og_title'] }}">
<meta name="twitter:description" content="{{ $seo['og_description'] }}">
@if (!empty($seo['og_image']))
  <meta name="twitter:image" content="{{ $seo['og_image'] }}">
@endif

<!-- Google Analytics / Custom Tracking Code -->
@if (!empty($seo['google_analytics_code']))
  {!! $seo['google_analytics_code'] !!}
@endif

