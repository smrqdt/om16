<?php

class Item extends ActiveRecord\Model {
	
	static $has_many = array(
			array('sizes'),
			array('orderitems')
	);
}