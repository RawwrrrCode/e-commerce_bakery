<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── HOME ──────────────────────────────────────────────────────────
$routes->get('/', 'Home::index');
$routes->get('/about',   'Pages::about');
$routes->get('/faq',     'Pages::faq');
$routes->get('/contact', 'Pages::contact');
$routes->get('/product/(:num)', 'Home::detail/$1');
$routes->post('/api/track-interaction', 'Home::trackInteraction', ['filter' => 'auth:user']);

// ── AUTH ──────────────────────────────────────────────────────────
$routes->get('/login',     'Auth::login');
$routes->post('/login',    'Auth::processLogin');
$routes->get('/register',  'Auth::register');
$routes->post('/register', 'Auth::processRegister');
$routes->get('/logout',    'Auth::logout');

// ── USER (harus login) ────────────────────────────────────────────
$routes->get('/cart',                'Cart::index',    ['filter' => 'auth:user']);
$routes->post('/cart/add',           'Cart::add',      ['filter' => 'auth:user']);
$routes->post('/cart/update',        'Cart::update',   ['filter' => 'auth:user']);
$routes->post('/cart/delete',        'Cart::delete',   ['filter' => 'auth:user']);
$routes->get('/checkout',            'Order::checkout',        ['filter' => 'auth:user']);
$routes->post('/checkout',           'Order::processCheckout', ['filter' => 'auth:user']);
$routes->get('/orders',              'Order::index',           ['filter' => 'auth:user']);
$routes->get('/orders/(:num)/pay',          'Order::pay/$1',         ['filter' => 'auth:user']);
$routes->get('/orders/(:num)/check-status', 'Order::checkStatus/$1', ['filter' => 'auth:user']);
$routes->get('/orders/(:num)/invoice',      'Order::invoice/$1',     ['filter' => 'auth:user']);
$routes->post('/orders/(:num)/confirm',     'Order::confirm/$1',     ['filter' => 'auth:user']);
$routes->post('/orders/(:num)/reorder',     'Order::reorder/$1',     ['filter' => 'auth:user']);
$routes->get('/orders/(:num)',              'Order::detail/$1',      ['filter' => 'auth:user']);

// ── WISHLIST (hanya AJAX, no page) ────────────────────────────────
$routes->post('/wishlist/add',       'Wishlist::add',          ['filter' => 'auth:user']);
$routes->post('/wishlist/remove',    'Wishlist::remove',       ['filter' => 'auth:user']);
$routes->get('/wishlist/check/(:num)', 'Wishlist::check/$1',   ['filter' => 'auth:user']);

// ── MIDTRANS WEBHOOK (tanpa auth — dipanggil server Midtrans) ─────
$routes->post('/payment/notification', 'Order::notification');

// ── PROFIL USER ───────────────────────────────────────────────────
$routes->get('/profile',  'Auth::profile',       ['filter' => 'auth:user']);
$routes->post('/profile', 'Auth::updateProfile', ['filter' => 'auth:user']);

// ── RATING & REVIEW ───────────────────────────────────────────────
$routes->post('/rate',   'Admin\Product::rate');
$routes->post('/review', 'Order::review');

// ── RECOMMENDATION TRACKING (AJAX) ────────────────────────────────
$routes->post('/rec/track', 'Admin\Recommendation::trackClick');

// ── ADMIN ─────────────────────────────────────────────────────────
$routes->get('/admin',                              'Admin\Dashboard::index',       ['filter' => 'auth:admin']);

// Produk
$routes->get('/admin/products',                     'Admin\Product::index',         ['filter' => 'auth:admin']);
$routes->get('/admin/products/create',              'Admin\Product::create',        ['filter' => 'auth:admin']);
$routes->post('/admin/products/store',              'Admin\Product::store',         ['filter' => 'auth:admin']);
$routes->get('/admin/products/edit/(:num)',         'Admin\Product::edit/$1',       ['filter' => 'auth:admin']);
$routes->post('/admin/products/update/(:num)',      'Admin\Product::update/$1',     ['filter' => 'auth:admin']);
$routes->get('/admin/products/delete/(:num)',       'Admin\Product::delete/$1',     ['filter' => 'auth:admin']);

// Laporan
$routes->get('/admin/report',                       'Admin\Report::index',          ['filter' => 'auth:admin']);

// Stok
$routes->get('/admin/stock',                        'Admin\Stock::index',           ['filter' => 'auth:admin']);
$routes->post('/admin/stock/update',                'Admin\Stock::update',          ['filter' => 'auth:admin']);

// Orders
$routes->get('/admin/orders',                       'Admin\Order::index',           ['filter' => 'auth:admin']);
$routes->get('/admin/orders/export',                'Admin\Order::export',          ['filter' => 'auth:admin']);
$routes->get('/admin/orders/(:num)',                'Admin\Order::detail/$1',       ['filter' => 'auth:admin']);
$routes->post('/admin/orders/update',               'Admin\Order::updateStatus',    ['filter' => 'auth:admin']);

// Users
$routes->get('/admin/users',                        'Admin\User::index',            ['filter' => 'auth:admin']);

// Reviews
$routes->get('/admin/reviews',                      'Admin\Review::index',          ['filter' => 'auth:admin']);
$routes->get('/admin/reviews/delete/(:num)',        'Admin\Review::delete/$1',      ['filter' => 'auth:admin']);

// Categories
$routes->get('/admin/categories',                   'Admin\Category::index',        ['filter' => 'auth:admin']);
$routes->post('/admin/categories/store',            'Admin\Category::store',        ['filter' => 'auth:admin']);
$routes->get('/admin/categories/delete/(:num)',     'Admin\Category::delete/$1',    ['filter' => 'auth:admin']);

// Settings
$routes->get('/admin/settings',                     'Admin\Settings::index',        ['filter' => 'auth:admin']);
$routes->post('/admin/settings/save',               'Admin\Settings::save',         ['filter' => 'auth:admin']);

// Evaluasi Rekomendasi (Hybrid Filtering)
$routes->get('/admin/recommendation',               'Admin\Recommendation::index',      ['filter' => 'auth:admin']);
$routes->post('/admin/recommendation/export',       'Admin\Recommendation::export',     ['filter' => 'auth:admin']);
$routes->get('/admin/recommendation/pdf',           'Admin\Recommendation::exportPdf',  ['filter' => 'auth:admin']);
$routes->get('/admin/recommendation/data',          'Admin\Recommendation::dataInput',  ['filter' => 'auth:admin']);
$routes->post('/admin/recommendation/config',       'Admin\Recommendation::saveConfig', ['filter' => 'auth:admin']);
