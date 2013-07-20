<?php
use Valitron\Validator;

abstract class Controller {
	public $app;
	protected $user;

	public function __construct(){
		$this->app = !empty($slim) ? $slim : \Slim\Slim::getInstance();
		$this->auth = \Strong\Strong::getInstance();

		if ($this->auth->loggedIn()) {
			$auth_user = $this->auth->getUser();
			$this->user = User::find($auth_user['id']);
		}
	}

	public function redirect($name, $routeName = true){
		$url = $routeName ? $this->app->urlFor($name) : $name;
		$this->app->redirect($url);
	}

	public function get($value = null){
		return $this->app->request()->get($value);
	}

	public function post($value = null){
		return $this->app->request()->post($value);
	}

	public function response($body){
		$response = $this->app->response();
		$response['Content-Type'] = 'application/json';
		$response->body(json_encode(array($body)));
	}

	public function render($template, $data = array(), $status = null){
		$data['path'] = APP_PATH;
		$this->app->render($template, $data, $status);
	}

	protected function validator($data, $fields = array()){
		return new Validator($data, $fields, null);
	}

	protected function errorOutput(array $errors = array()){
		$outputErrors = array();
		foreach ($errors as $key => $value) {
			$outputErrors[] = ucfirst($key) . ' ' . $value[0];
		}
		return $outputErrors;
	}
	
	// helper function needed accross controllers
	/**
	 * @return The current cart or a dummy (emphty array.)
	 */
	protected function getCart(){
		$cart = array();
		if (isset($_SESSION["cart"])) {
			$cart = $_SESSION["cart"];
		}
		return $cart;
	}
	
	/**
	 * @return A generated UUID.
	 */
	protected function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	
				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),
	
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,
	
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,
	
				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	
	/**
	 * Check if the current user has admi privileges
	 */
	protected function checkAdmin(){
		if(isset($this->user)){
			if($this->user->admin){
				return;
			}
		}
		$this->redirect("home");
	}
}