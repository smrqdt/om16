<?php

namespace Tapeshop\Rest;

use Slim\Slim;
use Strong\Strong;
use Tapeshop\Models\User;


class RestController {
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

	public function response($json) {
		$response = $this->app->response();
		$response['Content-Type'] = 'application/json';
		$response->body($json);
	}

	public function param($name = null){
		return $this->app->request()->params($name);
	}
} 