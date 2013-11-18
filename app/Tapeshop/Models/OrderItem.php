<?php
namespace Tapeshop\Models;

class Orderitem extends \ActiveRecord\Model {
	
	static $belongs_to = array(
			array('item'),
			array('order')
	);
	
	static $has_many = array(
			array('itemnumbers')
	);
}