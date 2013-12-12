<?php
define("APP_PATH", "http://".$_SERVER['SERVER_NAME'] .$_SERVER['SCRIPT_NAME'] . "/../");

require 'vendor/autoload.php';
require_once 'config.php';

/*
 * Configure phpactiverecord
 */
ActiveRecord\Config::initialize(function($cfg) {
	$cfg->set_model_directory('.');
	$cfg->set_connections(array('development' => DB_PROVIDER.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOSTNAME.'/'.DB_NAME));
});


/*
 * Set up Slim application
 */
$app = new \Slim\Slim();

$app->add(new \Tapeshop\SofortPayment());

$app->run();
