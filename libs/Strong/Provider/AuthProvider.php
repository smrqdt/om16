<?php
namespace Strong\Provider;
class AuthProvider extends \Strong\Provider {

	/**
	 * User login check based on provider
	 *
	 * @return boolean
	 */
	public function loggedIn() {
		return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
	}

	/**
	 * To authenticate user based on username or email
	 * and password
	 *
	 * @param string $usernameOrEmail
	 * @param string $password
	 * @return boolean
	 */
	public function login($usernameOrEmail, $password) {
		if(! is_object($usernameOrEmail)) {
			$user = \User::find_by_username_or_email($usernameOrEmail, $usernameOrEmail);
		}
		if($user == null){
			return false;
		}

		if(($user->email === $usernameOrEmail || $user->username === $usernameOrEmail) && $this->verify($user, $password)) {
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
	 * @param array $user
	 * @return boolean
	 */
	protected function completeLogin($user) {
		$users = \User::find($user->id);
		$users->save();

		$userInfo = array(
				'id' => $user->id,
				'username' => $user->username,
				'email' => $user->email,
				'name' => $user->currentAddress()->name,
				'lastname' => $user->currentAddress()->lastname,
				'street' => $user->currentAddress()->street,
				'street_number' => $user->currentAddress()->building_number,
				'plz' => $user->currentAddress()->postcode,
				'country' => $user->currentAddress()->country,
				'city' => $user->currentAddress()->city,
				'logged_in' => true,
				'admin' => $user->admin
		);

		return parent::completeLogin($userInfo);
	}
}
