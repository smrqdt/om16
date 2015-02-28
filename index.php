<?php
define("APP_PATH", "http://".$_SERVER['SERVER_NAME'] .$_SERVER['SCRIPT_NAME'] . "/../");

require 'vendor/autoload.php';
require_once 'config.php';

/*
 * I18N support
 */

$language  = str_replace("-","_",substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5));
$lang_short = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);

$folder = "assets/locale";
$domain = "messages";
$encoding = "UTF-8";

putenv("LANG=" . $language);
setlocale(LC_ALL, $language, $lang_short);

bindtextdomain($domain, $folder);
bind_textdomain_codeset($domain, $encoding);

textdomain($domain);

/*
 * Configure phpactiverecord
 */
ActiveRecord\Config::initialize(function($cfg) {
	/** @var ActiveRecord\Config $cfg */
	$cfg->set_connections(array('development' => DB_PROVIDER.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOSTNAME.'/'.DB_NAME));
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
		'login.url' => APP_PATH.'login',
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
$app->get('/', array($shopController, 'index'))->name('home');
$app->get('/checkout/', array($shopController, 'checkout'))->name("checkout");
$app->post('/noSignup/', array($shopController, "noSignup"));
$app->get('/ticketscript/', array($shopController, "ticketscript"));
$app->get('/changeaddress', array($shopController, 'changeAddress'));

// Admin routings
$adminController = new Tapeshop\Controllers\AdminController();
$app->get('/admin/', array($adminController, 'index'))->name('admin');
$app->get('/admin/items/', array($adminController, 'items'))->name('adminitems');
$app->get('/admin/orders/', array($adminController, 'orders'))->name('adminorders');

// order routings
$orderController = new \Tapeshop\Controllers\OrderController();
$orderController::updateStatus();
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
$app->post('/item/:id/numberspdf', array($itemController, 'numbersPdf'));

$ticketcodeController = new \Tapeshop\Controllers\TicketcodeController();
$app->get('/ticketcodes/:item_id', array($ticketcodeController, 'show'))->name('ticketcodes');
$app->post('/ticketcode/:orderitem_id', array($ticketcodeController, 'invalidate'));
$app->post('/ticketcode/:orderitem_id/reactivate', array($ticketcodeController, 'reactivate'));

// Static Pages
$staticController = new Tapeshop\Controllers\StaticController();
$app->get('/:pageName/', array($staticController, "renderStaticPage"));

// REST

$items = new \Tapeshop\Rest\Items();
$app->get('/items/:id/', array($items, 'get'));
$app->put('/items/:id/manage/', array($items, 'updateManageStock'));

$stocks = new \Tapeshop\Rest\StocksAPI();
$app->put('/stocks/item/:id/', array($stocks, 'addItem'));
$app->put('/stocks/variation/:id/', array($stocks,'addVariation'));

$variations = new \Tapeshop\Rest\VariationsAPI();
$app->post('/variations/', array($variations, 'add'));
$app->delete('/variations/:id/', array($variations, 'delete'));

$numbers = new \Tapeshop\Rest\NumbersAPI();
$app->put('/items/:id/numbered/', array($numbers, 'updateManageNumbers'));
$app->put('/items/:id/shownumbers', array($numbers, 'updateShowNumbers'));
$app->put('/numbers/:id/', array($numbers, 'updateNumbers'));
$app->put('/numbers/invalid/:id/', array($numbers, 'updateInvalidNumbers'));
$app->put('/numbers/override/:id/', array($numbers, 'overrideWarning'));

$orders = new \Tapeshop\Rest\OrdersAPI();
$app->get('/orders/:id/',array($orders, 'get'));
$app->put('/orders/:id/payed/',array($orders, 'payed'));
$app->put('/orders/:id/notpayed/',array($orders, 'notpayed'));
$app->put('/orders/:id/shipped/',array($orders, 'shipped'));
$app->put('/orders/:id/notshipped/',array($orders, 'notshipped'));

$app->run();
