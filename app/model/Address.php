<?php

class Address extends ActiveRecord\Model{
	
	static $table_name = "addresses";
	
	static $belongs_to = array(
			array('user')
	);
	
	static $has_many = array(
			array('orders')
	);
}