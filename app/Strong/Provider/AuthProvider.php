<?php
namespace Strong\Provider;

use \Tapeshop\Models\User;
use \Strong\Provider;

class AuthProvider extends Provider {

	/**
	 * User login check based on provider
	 *
	 * @return boolean
	 */
	public function loggedIn() {
		return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
	}

	/**
	 * To authenticate user based on username
	 * and password
	 *
	 * @param string $email
	 * @param string $password
	 * @return boolean
	 */
	public function login($email, $password) {
		/** @var $user \Tapeshop\Models\User */
		$user = null;
		if(! is_object($email)) {
			$user = User::find_by_email($email);
		}
		if($user == null){
			return false;
		}

		if(($user->email === $email) && $this->verify($user, $password)) {
			return $this->completeLogin($user);
		}

		return false;
	}

	private function verify($user, $password){
		$bcrypt = new Bcrypt();
		return $bcrypt->verify($password, $user->password);
	}

	public function initPassword($password){
		$bcrypt = new Bcrypt();
		return $bcrypt->hash($password);
	}

	/**
	 * Login and store user details in Session
	 *
	 * @param \Tapeshop\Models\User $user
	 * @return boolean
	 */
	protected function completeLogin($user) {
		$users = User::find($user->id);
		$users->save();

		$userInfo = array(
				'id' => $user->id,
				'logged_in' => true
		);

		return parent::completeLogin($userInfo);
	}
}
