<?php

class Orderitem extends ActiveRecord\Model {
	
	static $belongs_to = array(
			array('item'),
			array('order')
	);
}