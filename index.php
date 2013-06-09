<?php
define("APP_PATH", "http://".$_SERVER['SERVER_NAME'] .$_SERVER['SCRIPT_NAME'] . "/../");
date_default_timezone_set('Europe/Berlin');

require_once 'libs/Slim/Slim.php';
require_once 'libs/Slim/View.php';
require_once 'libs/Slim/Middleware.php';
require_once 'libs/Slim/Extras/Views/Smarty.php';
require_once 'libs/Slim/Extras/Log/DateTimeFileWriter.php';
require_once 'libs/Strong/Strong.php';
require_once 'libs/Slim/Extras/Middleware/StrongAuth.php';
require_once 'Database.php';
require_once 'app/DBObject.php';
require_once 'app/model/Size.php';
require_once 'app/model/Item.php';
require_once 'app/model/User.php';
require_once 'app/model/Order.php';
require_once 'app/model/OrderItem.php';
require_once 'app/Controller.php';
require_once 'app/controller/LoginController.php';
require_once 'app/controller/ShopController.php';
require_once 'app/controller/AdminController.php';
require_once 'config.php';

session_start();
\Slim\Slim::registerAutoloader();

$smartyView = new \Slim\Extras\Views\Smarty();

$app = new \Slim\Slim(array(
		'view' => $smartyView,
		'log.level' => 4,
		'log.enabled' => true
));

$authConfig = array(
		'provider' => 'PDO',
		'pdo' => new PDO(DB_PROVIDER.":host=".DB_HOSTNAME.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD),
		'auth.type' => 'form',
		'login.url' => APP_PATH.'index.php/login',
		'security.urls' => array(
				array('path' => '/admin'),
		),
);

$app->add(new \Slim\Extras\Middleware\StrongAuth($authConfig));

$loginController = new LoginController();
$shopController = new ShopController();
$adminController = new AdminController();

// Login
$app->map('/login/', array($loginController, 'index'))->via('GET', 'POST')->name('login');
$app->get('/logout/', array($loginController, 'logout'))->name('logout');

// Shop
$app->get('/', array($shopController, 'index'))->name('home');
$app->post('/addItem/:id', array($shopController, 'addItem'));
$app->get('/cart', array($shopController, 'cart'));
$app->get('/clearCart', array($shopController, 'clearCart'));
$app->get('/checkout', array($shopController, 'checkout'));
$app->get('/submitOrder', array($shopController, 'submitOrder'));
$app->get('/order/:hash', array($shopController, "order"))->name("order");
$app->post('/noSignup', array($shopController, "noSignup"));

$app->get('/admin', array($adminController, 'index'))->name('admin');
$app->get('/admin/order/delete/:id', array($adminController, 'deleteOrder'));
$app->get('/admin/user/delete/:id', array($adminController, 'deleteUser'));


$app->run();

