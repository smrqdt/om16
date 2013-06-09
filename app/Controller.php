<?php
use Valitron\Validator;

abstract class Controller {
	public $app;

	public function __construct(){
		$this->app = !empty($slim) ? $slim : \Slim\Slim::getInstance();
		$this->auth = \Strong\Strong::getInstance();

		if ($this->auth->loggedIn()) {
			$this->user = $this->auth->getUser();
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
		$this->app->view()->appendData(array('auth' => $this->auth));
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
}