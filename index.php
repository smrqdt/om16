<?php
define("APP_PATH", "http://".$_SERVER['SERVER_NAME'] .$_SERVER['SCRIPT_NAME'] . "/../");

require 'vendor/autoload.php';
require_once 'config.php';

ActiveRecord\Config::initialize(function($cfg) {
	$cfg->set_model_directory('.');
	$cfg->set_connections(array('development' => DB_PROVIDER.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOSTNAME.'/'.DB_NAME));
});

session_start();

\Slim\Extras\Views\Smarty::$smartyDirectory = 'vendor/smarty/smarty/distribution/libs';
\Slim\Extras\Views\Smarty::$smartyTemplatesDirectory = 'app/view';
$smartyView = new \Slim\Extras\Views\Smarty();

$app = new \Slim\Slim(array(
		'view' => $smartyView,
		'log.level' => 4,
		'log.enabled' => true
));

$authConfig = array(
		'provider' => 'AuthProvider',
		'auth.type' => 'form',
		'login.url' => APP_PATH.'index.php/login',
		'security.urls' => array(
				array('path' => '/admin'),
		),
);

$app->add(new \Slim\Extras\Middleware\StrongAuth($authConfig));

$app->add(new \Slim\Extras\Middleware\CsrfGuard());


// Login
$loginController = new LoginController();
$app->map('/login/', array($loginController, 'login'))->via('GET', 'POST')->name('login');
$app->get('/logout/', array($loginController, 'logout'))->name('logout');
$app->map('/signup/', array($loginController, 'signup'))->via('GET', 'POST')->name('signup');

// Shop
$shopController = new ShopController();
$app->get('/', array($shopController, 'index'))->name('home');
$app->get('/checkout', array($shopController, 'checkout'))->name("checkout");
$app->post('/noSignup', array($shopController, "noSignup"));

// Admin routings
$adminController = new AdminController();
$app->get('/admin', array($adminController, 'index'))->name('admin');
$app->get('/admin/items', array($adminController, 'items'))->name('adminitems');
$app->get('/admin/orders', array($adminController, 'orders'))->name('adminorders');
// $app->get('/admin/user/delete/:id', array($adminController, 'deleteUser'));

// order routings
$orderController = new OrderController();
$orderController::updateStatus();
$app->post('/order', array($orderController, 'submitOrder'));
$app->get('/order/:hash', array($orderController, "order"))->name("order");
$app->post('/order/delete/:id', array($orderController, 'deleteOrder'));
$app->post('/order/:id/payed', array($orderController, 'payed'));
$app->post('/order/:id/shipped', array($orderController, 'shipped'));

// cart routings
$cartController = new CartController();
$app->post('/cart/addItem/:id', array($cartController, 'addItem'));
$app->get('/cart', array($cartController, 'cart'));
$app->post('/cart/clear', array($cartController, 'clearCart'));
$app->post('/cart/increase', array($cartController, 'increase'));
$app->post('/cart/decrease', array($cartController, 'decrease'));
$app->post('/cart/remove', array($cartController, 'remove'));

// user routings
$userController = new UserController();
$app->post('/user/delete/:id', array($userController, 'delete'));
$app->get('/admin/user/edit/:id', array($userController, 'edit'));
$app->map('/admin/user/save/:id', array($userController, 'save'))->via('GET', 'POST')->name('editUser');

// item routings
$itemController = new ItemController();
$app->get('/item/:id', array($itemController, 'show'));
$app->map('/item/edit/:id', array($itemController, 'edit'))->via('GET', 'POST');
$app->post('/item/delete/:id', array($itemController, 'delete'));
$app->post('/item/:id/addsize', array($itemController, 'addSize'));
$app->post('/item/:id/removeimage', array($itemController, 'removeImage'));
$app->post('/item/:id/addnumbers', array($itemController, 'addNumbers'));
$app->post('/item/:id/takenumbers', array($itemController, 'takeNumbers'));
$app->post('/item/:id/invalidatenumbers', array($itemController, 'invalidateNumbers'));
$app->post('/item/deletesize/:id', array($itemController, 'deleteSize'));
$app->map('/items/create', array($itemController, 'create'))->via('GET', 'POST');

$app->run();
