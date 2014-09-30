<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property String email
 * @property String username
 * @property String password
 * @property int id
 */
class User extends Model {
	static $has_many = array(
		array('orders'),
		array('addresses')
	);

	function currentAddress() {
		$address = Address::find('first', array('conditions' => array('user_id = ? AND current = ?', $this->id, 1)));
		if ($address) {
			return $address;
		}
		return new Address();
	}
}