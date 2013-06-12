<?php

class User extends ActiveRecord\Model{
	public $logged_in;
	
	static $has_many = array(
			array('orders')
	);
}