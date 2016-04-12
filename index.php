<?php
define("APP_PATH", (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . str_replace("index.php", "", $_SERVER['SCRIPT_NAME']));
date_default_timezone_set('Europe/Berlin');

require 'vendor/autoload.php';
require_once 'config.php';

/*
 * I18N support
 */
$folder = "assets/locale";
$domain = "messages";
$encoding = "UTF-8";

setlocale(LC_ALL, "de_DE", "de");

bindtextdomain($domain, $folder);
bind_textdomain_codeset($domain, $encoding);

textdomain($domain);

/*
 * Configure phpactiverecord
 */
ActiveRecord\Config::initialize(function ($cfg) {
	/** @var ActiveRecord\Config $cfg */
	$cfg->set_connections(array('development' => DB_PROVIDER . '://' . DB_USERNAME . ':' . DB_PASSWORD . '@' . DB_HOSTNAME . '/' . DB_NAME));
});

/*
 * Set up Slim application
 */
$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Smarty(),
	'templates.path' => 'app/Tapeshop/Views/' . VIEW,
	'log.level' => 4,
	'log.enabled' => true
));

/*
 * Configure authentication
 */
$authConfig = array(
	'provider' => 'AuthProvider',
	'auth.type' => 'form',
	'login.url' => APP_PATH . 'login',
	'security.urls' => array(
		array('path' => '/admin'),
	),
);

// add authentication 
$app->add(new \Slim\Extras\Middleware\StrongAuth($authConfig));

// add CSRF protection
$app->add(new \Tapeshop\Middleware\CsrfGuard());

/*
 * Set up routes
 */

// Login
$loginController = new Tapeshop\Controllers\LoginController();
$app->map('/login/', array($loginController, 'login'))->via('GET', 'POST')->name('login');
$app->get('/logout/', array($loginController, 'logout'))->name('logout');
$app->map('/signup/', array($loginController, 'signup'))->via('GET', 'POST')->name('signup');

// Shop
$shopController = new Tapeshop\Controllers\ShopController();
$app->get('/shop', array($shopController, 'index'));
$app->get('/tickets', array($shopController, 'shop'));
$app->get('/checkout/', array($shopController, 'checkout'))->name("checkout");
$app->post('/noSignup/', array($shopController, "noSignup"));
$app->get('/changeaddress', array($shopController, 'changeAddress'));

// Admin routings
$adminController = new Tapeshop\Controllers\AdminController();
$app->get('/admin/', array($adminController, 'index'))->name('admin');
$app->get('/admin/items/', array($adminController, 'items'))->name('adminitems');
$app->get('/admin/orders/', array($adminController, 'orders'))->name('adminorders');

// order routings
$orderController = new \Tapeshop\Controllers\OrderController();
$app->get('/order/allAsPdf/', array($orderController, 'allBillingsAsPdf'));
$app->post('/order/', array($orderController, 'submitOrder'));
$app->get('/order/:hash/', array($orderController, "order"))->name("order");
$app->post('/order/delete/:id/', array($orderController, 'deleteOrder'));
$app->get('/order/billing/:hash/', array($orderController, 'billing'));

// cart routings
$cartController = new \Tapeshop\Controllers\CartController();
$app->post('/cart/addItem/:id/', array($cartController, 'addItem'));
$app->get('/cart/', array($cartController, 'cart'))->name('cart');
$app->post('/cart/clear/', array($cartController, 'clearCart'));
$app->post('/cart/increase/', array($cartController, 'increase'));
$app->post('/cart/decrease/', array($cartController, 'decrease'));
$app->post('/cart/remove/', array($cartController, 'remove'));
$app->post('/cart/changesize/', array($cartController, 'changeSize'));

// user routings
$userController = new \Tapeshop\Controllers\UserController();
$app->post('/user/delete/:id/', array($userController, 'delete'));
$app->get('/admin/user/edit/:id/', array($userController, 'edit'));
$app->map('/admin/user/save/:id/', array($userController, 'save'))->via('GET', 'POST')->name('editUser');

// item routings
$itemController = new \Tapeshop\Controllers\ItemController();
$app->get('/item/:id/', array($itemController, 'show'));
$app->map('/item/edit/:id/', array($itemController, 'edit'))->via('GET', 'POST')->name('editItem');
$app->post('/item/delete/:id/', array($itemController, 'delete'));
$app->post('/item/:id/removeimage/', array($itemController, 'removeImage'));
$app->map('/items/create/', array($itemController, 'create'))->via('GET', 'POST');

$ticketcodeController = new \Tapeshop\Controllers\TicketcodeController();
$app->get('/ticketcodes/:item_id', array($ticketcodeController, 'show'))->name('ticketcodes');
$app->post('/ticketcode/:orderitem_id', array($ticketcodeController, 'invalidate'));
$app->post('/ticketcode/:orderitem_id/reactivate', array($ticketcodeController, 'reactivate'));

// REST
$items = new \Tapeshop\Rest\Items();
$app->get('/items/', array($items, 'getAll'));
$app->get('/items/:id/', array($items, 'get'));
$app->put('/items/:id/manage/', array($items, 'updateManageStock'));

$stocks = new \Tapeshop\Rest\StocksAPI();
$app->put('/stocks/item/:id/', array($stocks, 'addItem'));
$app->put('/stocks/variation/:id/', array($stocks, 'addVariation'));

$variations = new \Tapeshop\Rest\VariationsAPI();
$app->post('/variations/', array($variations, 'add'));
$app->delete('/variations/:id/', array($variations, 'delete'));

$orders = new \Tapeshop\Rest\OrdersAPI();
$app->get('/orders/allAsCsv', array($orders, 'allAsCsv'));
$app->get('/orders/:id/', array($orders, 'get'));
$app->put('/orders/:id/payed/', array($orders, 'payed'));
$app->put('/orders/:id/notpayed/', array($orders, 'notpayed'));
$app->put('/orders/:id/shipped/', array($orders, 'shipped'));
$app->put('/orders/:id/notshipped/', array($orders, 'notshipped'));


$carts = new \Tapeshop\Rest\CartsAPI();
$app->get('/cartapi/', array($carts, 'get'));
$app->post('/cartapi/:item_id', array($carts, 'add'));
$app->delete('/cartapi/:item_id', array($carts, 'remove'));
$app->delete('/cartapi/', array($carts, 'clear'));

$nametags = new \Tapeshop\Rest\NametagAPI();
$app->get('/nametags/:order_id', array($nametags, 'get'));
$app->post('/nametags/', array($nametags, 'create'));
$app->delete('/nametags/:id', array($nametags, 'delete'));
$app->get('/namensschilder/allAsCsv', array($nametags, 'allAsCsv'));

$app->get('/orders/ticketcode/:ticketcode', array($orders, 'findByTicketcode'));

$app->get('/namensschilder/', array($shopController, 'nametags'));


// Static Pages
// Needs to be placed last, since it all not defined routes
$staticController = new Tapeshop\Controllers\StaticController();
$app->get('/', array($staticController, "renderIndex"))->name('home');
$app->get('/:pageName/', array($staticController, "renderStaticPage"))->name("static");

$app->run();
