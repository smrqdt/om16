<?php

class User extends ActiveRecord\Model{
	public $logged_in;
	
	static $has_many = array(
			array('orders'),
			array('addresses')
	);
	
	function currentAddress(){
		return Address::find('first', array('conditions' => array('user_id = ? AND current = ?',$this->id, 1)));
	}
}