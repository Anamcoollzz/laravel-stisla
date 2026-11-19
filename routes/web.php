<?php

require __DIR__.'/stisla-web.php';
require __DIR__.'/pages-web.php';
require __DIR__.'/ecommerce-pages-web.php';
require __DIR__.'/spark-admin-web.php';
require __DIR__.'/news-web.php';
require __DIR__.'/spp-payment-web.php';

if (config('app.menu_app') === 'lp-compro-workers') {
    require __DIR__.'/lp-compro-web.php';
}
