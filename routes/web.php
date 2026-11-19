<?php

require __DIR__.'/stisla-web.php';
require __DIR__.'/stisla-pages-web.php';
require __DIR__.'/spark-admin-web.php';

if (config('app.menu_app') === 'online-shop') {
    require __DIR__.'/ecommerce-pages-web.php';
}

if (config('app.menu_app') === 'spp-payment') {
    require __DIR__.'/spp-payment-web.php';
}

if (config('app.menu_app') === 'news') {
    require __DIR__.'/news-web.php';
}

if (config('app.menu_app') === 'lp-compro-workers') {
    require __DIR__.'/lp-compro-web.php';
}
