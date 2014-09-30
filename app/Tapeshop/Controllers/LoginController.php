<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use Tapeshop\Controller;
use Tapeshop\Models\Address;
use Tapeshop\Models\User;

/**
 * Handle authentication operations.
 */
class LoginController extends Controller {

	/**
	 * Login an user.
	 */
	public function login() {
		if ($this->app->request()->isPost()) {
			$v = $this->validator($this->post());
			$v->rule('required', array('email', 'password'));
			if ($v->validate()) {
				if ($this->auth->login($this->post('email'), $this->post('password'))) {
					$this->app->flash('info', 'Your login was successfull');
					$u = $this->auth->getUser();
					$user = User::find($u["id"]);
					if ($user->admin > 0) {
						$this->redirect("admin");
					} else {
						$this->redirect('home');
					}
				}
			}
			$this->app->flashNow('error', "Login failed!");
		}
		$this->render('login/login.html');
	}

	/**
	 * Register a new user.
	 */
	public function signup() {
		if ($this->app->request()->isPost()) {
			$v = $this->validator($this->post());
			$v->rule('required', array('email', 'password', 'password_verify'));
			$v->rule('email', 'email');
			$v->rule('length', 'password', 6, 256);
			$v->rule('equals', 'password', 'password_verify');
			if ($v->validate()) {
				$u = new User();
				$u->email = $this->post('email');
				$u->password = $this->auth->getProvider()->initPassword($this->post('password'));
				$u->admin = true;
				try {
					$u->save();
				} catch (ActiveRecordException $e) {
					$this->app->flashNow('error', 'Could not create user! ' . $e->getMessage());
					$this->useDataFromRequest('signupform', array('email', 'password', 'password_verify'));
					$this->render('login/signup.tpl');
				}

				$this->app->flash('info', 'Your registration was successfull');
				$this->auth->login($this->post('email'), $this->post('password'), $this->post('remember'));
				$this->redirect('home');
			}
			$this->app->flashNow('error', $this->errorOutput($v->errors()));
			$this->useDataFromRequest('signupform', array('email', 'password', 'password_verify'));
		}
		$this->render('login/signup.html');
	}

	/**
	 * Logout the current user.
	 */
	public function logout() {
		$this->app->flash('info', 'Come back sometime soon!');
		$this->auth->logout(true);
		$this->redirect('home');
	}
}