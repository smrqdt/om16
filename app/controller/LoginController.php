<?php

class LoginController extends Controller {

	public function login(){
		if ($this->app->request()->isPost()) {
			$v = $this->validator($this->post());
			$v->rule('required', array('email', 'password'));
			if ($v->validate()) {
				if ($this->auth->login($this->post('email'), $this->post('password'))) {
					$this->app->flash('info', 'Your login was successfull');
					$u = $this->auth->getUser();
					$user = User::find($u["id"]);
					if($user->admin > 0){
						$this->redirect("admin");
					}else{
						$this->redirect('home');
					}
				}
				$this->app->flashNow('error', "Login failed!");
			}
			$this->app->flashNow('error', $this->errorOutput($v->errors()));
		}
		$this->render('login/login.tpl');
	}

	public function signup(){
		if ($this->app->request()->isPost()) {
			$v = $this->validator($this->post());
			$v->rule('required', array('email', 'username', 'password', 'password_verify', 'name', 'lastname', 'street', 'building_number', 'postcode', 'city', 'country'));
			$v->rule('email', 'email');
			$v->rule('length', 'username', 3, 128);
			$v->rule('length', 'password', 6, 256);
			$v->rule('equals', 'password', 'password_verify');
			if ($v->validate()) {
				$u = new User();
				$u->email = $this->post('email');
				$u->username = $this->post('username');
				$u->password = $this->auth->getProvider()->initPassword($this->post('password'));
				try{
					$u->save();
				}catch(ActiveRecord\ActiveRecordException $e){
					$this->app->flashNow('error', 'Could not create user! ' . $this->errorOutput($e));	
					$this->useDataFromRequest('signupform', array('email', 'username', 'password', 'password_verify', 'name', 'lastname', 'street', 'building_number', 'postcode', 'city', 'country'));
					$this->render('login/signup.tpl');
				}
				
				$a = new Address();
				$a->user_id = $u->id;
				$a->name = $this->post('name');
				$a->lastname = $this->post('lastname');
				$a->street = $this->post('street');
				$a->building_number = $this->post('building_number');
				$a->postcode = $this->post('postcode');
				$a->city = $this->post('city');
				$a->country = $this->post('country');
				try{
					$a->save();
				}catch(ActiveRecord\ActiveRecordException $e){
					$this->app->flashNow('error', 'Error saving the adress! Please verify it in the user settings. ' . $this->errorOutput($e));
				}

				$this->app->flash('info', 'Your registration was successfull');
				$this->auth->login($this->post('username'), $this->post('password'), $this->post('remember'));
				$this->redirect('home');
			}
			$this->app->flashNow('error', $this->errorOutput($v->errors()));
			$this->useDataFromRequest('signupform', array('email', 'username', 'password', 'password_verify', 'name', 'lastname', 'street', 'building_number', 'postcode', 'city', 'country'));
		}
		$this->render('login/signup.tpl');
	}

	public function logout(){
		$this->app->flash('info', 'Come back sometime soon!');
		$this->auth->logout(true);
		$this->redirect('home');
	}

	public function forgot(){
		// TODO
		if ($this->auth->loggedIn()) {
			$this->redirect('/', false);
		}
		$this->render('login/forgot');
	}
}