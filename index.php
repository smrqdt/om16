<?php
define("APP_PATH", "http://".$_SERVER['SERVER_NAME'] .$_SERVER['SCRIPT_NAME'] . "/../");
date_default_timezone_set('Europe/Berlin');

require_once 'libs/Slim/Slim.php';
require_once 'libs/Slim/View.php';
require_once 'libs/Slim/Middleware.php';
require_once 'libs/Slim/Extras/Views/Smarty.php';
require_once 'libs/Slim/Extras/Log/DateTimeFileWriter.php';
require_once 'libs/ActiveRecord.php';
require_once 'libs/Strong/Strong.php';
require_once 'libs/Slim/Extras/Middleware/StrongAuth.php';

require_once 'app/model/Size.php';
require_once 'app/model/Item.php';
require_once 'app/model/User.php';
require_once 'app/model/Order.php';
require_once 'app/model/OrderItem.php';

require_once 'app/Controller.php';
require_once 'app/controller/LoginController.php';
require_once 'app/controller/ShopController.php';
require_once 'app/controller/CartController.php';
require_once 'app/controller/OrderController.php';
require_once 'app/controller/UserController.php';
require_once 'app/controller/ItemController.php';
require_once 'app/controller/AdminController.php';
require_once 'config.php';

ActiveRecord\Config::initialize(function($cfg) {
	$cfg->set_model_directory('.');
	$cfg->set_connections(array('development' => DB_PROVIDER.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOSTNAME.'/'.DB_NAME));
});

session_start();

\Slim\Slim::registerAutoloader();

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


// Login
$loginController = new LoginController();
$app->map('/login/', array($loginController, 'index'))->via('GET', 'POST')->name('login');
$app->get('/logout/', array($loginController, 'logout'))->name('logout');
$app->map('/signup/', array($loginController, 'signup'))->via('GET', 'POST')->name('signup');

// Shop
$shopController = new ShopController();
$app->get('/', array($shopController, 'index'))->name('home');
$app->get('/checkout', array($shopController, 'checkout'));
$app->post('/noSignup', array($shopController, "noSignup"));

// order routings
$orderController = new OrderController();
$app->post('/order', array($orderController, 'submitOrder'));
$app->get('/order/:hash', array($orderController, "order"))->name("order");
$app->post('/order/delete/:id', array($orderController, 'deleteOrder'));

// cart routings
$cartController = new CartController();
$app->post('/cart/addItem/:id', array($cartController, 'addItem'));
$app->get('/cart', array($cartController, 'cart'));
$app->post('/cart/clear', array($cartController, 'clearCart'));

// user routings
$userController = new UserController();
$app->post('/user/delete/:id', array($userController, 'deleteUser'));

// item routings
$itemController = new ItemController();
$app->get('/item/:id', array($itemController, 'show'));
$app->map('/item/edit/:id', array($itemController, 'edit'))->via('GET', 'POST');
$app->post('/item/delete/:id', array($itemController, 'delete'));
$app->post('/item/:id/addsize', array($itemController, 'addSize'));
$app->post('/item/deletesize/:id', array($itemController, 'deleteSize'));

// Admin routings
$adminController = new AdminController();
$app->get('/admin', array($adminController, 'index'))->name('admin');
$app->get('/admin/items', array($adminController, 'items'))->name('adminitems');


$app->run();
