<?php

namespace Tapeshop\Rest;

use ActiveRecord\RecordNotFound;
use Slim\Slim;
use Strong\Strong;
use Tapeshop\Models\User;

class RestController {
	public $app;
	protected $user;
	protected $responseData = array();
	protected $params;

	public function __construct() {
		$this->app = !empty($slim) ? $slim : Slim::getInstance();
		$this->auth = Strong::getInstance();

		if ($this->auth->loggedIn()) {
			$auth_user = $this->auth->getUser();
			try {
				$this->user = User::find($auth_user['id']);
			} catch (RecordNotFound $e) {
				$this->haltReponse("User not found!", 500);
			}
		}
	}

	public function params() {
		if(!isset($params)){
			$params = json_decode($this->app->request()->getBody());
		}
		return $params;
	}

	/**
	 * Check if the current user has admin privileges
	 */
	protected function checkAdmin() {
		if (isset($this->user)) {
			if ($this->user->admin) {
				return;
			}
		}
		$this->haltReponse(array("error" => "Not logged in as admin!"), 403);
	}

	public function response($json, $status = 200) {
		$response = $this->app->response();
		$response['Content-Type'] = 'application/json';
		$response->setStatus($status);
		if (is_string($json)) {
			$response->body($json);
		} else {
			$response->body(json_encode($json));
		}
	}

	public function haltReponse($json, $status = 200) {
		if (is_string($json)) {
			$this->app->halt($status, $json);
		} else {
			$this->app->halt($status, json_encode($json));
		}
	}
} 
