<?php
namespace Tapeshop;

use ActiveRecord\RecordNotFound;
use Slim\Slim;
use Strong\Strong;
use Tapeshop\Controllers\Cart;
use Tapeshop\Models\User;
use Valitron\Validator;

abstract class Controller {
	public $app;
	protected $user;
	protected $responseData = array();

	public function __construct() {
		$this->app = !empty($slim) ? $slim : Slim::getInstance();
		$this->auth = Strong::getInstance();

		if ($this->auth->loggedIn()) {
			$auth_user = $this->auth->getUser();
			try{
				$this->user = User::find($auth_user['id']);
			}catch(RecordNotFound $e){
				$this->app->flashNow('error', 'User not found!');
			}
		}
	}

	public function render($template, $data = array(), $status = null) {
		$data['path'] = APP_PATH;
		$data['item_placeholder'] = APP_PATH . ITEM_PLACEHOLDER;
		$data = array_merge($data, array(
			"noCartItems" => $this->getCartCount(),
			"user" => $this->user
		));
		$data = array_merge($data, $this->responseData);
		$this->app->render($template, $data, $status);
	}

	protected function getCartCount() {
		return Cart::getCartCount();
	}

	/**
	 * @param $data
	 * @param array $fields
	 * @return Validator
	 */
	protected function validator($data, $fields = array()) {
		return new Validator($data, $fields, null);
	}

	/**
	 * @param array $errors
	 * @return array
	 */
	protected function errorOutput(array $errors = array()) {
		$outputErrors = array();
		foreach ($errors as $value) {
			$outputErrors[] = $value[0];
		}
		return $outputErrors;
	}

	/**
	 * @return array The current cart or a dummy (empty array.)
	 */
	protected function getCart() {
		return Cart::getCart();
	}

	/**
	 * @return String A generated UUID.
	 */
	protected function gen_uuid() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),

			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,

			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	// helper function needed across controllers

	/**
	 * Check if the current user has admin privileges
	 */
	protected function checkAdmin() {
		if (isset($this->user)) {
			if ($this->user->admin) {
				return;
			}
		}
		$this->redirect("home");
	}

	public function redirect($name, $routeName = true) {
		$url = $routeName ? $this->app->urlFor($name) : $name;
		$this->app->redirect($url);
	}

	/**
	 * Get data from the request and put them into the response
	 * @param string $name key of the session var to set
	 * @param array $keys keys from the request
	 */
	protected function useDataFromRequest($name, $keys) {
		$map = array();
		foreach ($keys as $key) {
			if ($this->app->request()->isPost()) {
				$map[$key] = $this->post($key);
			} else {
				$map[$key] = $this->get($key);
			}
		}
		$this->responseData[$name] = $map;
	}

	public function post($value = null) {
		return $this->app->request()->post($value);
	}

	public function get($value = null) {
		return $this->app->request()->get($value);
	}
}