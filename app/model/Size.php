<?php

class Size extends ActiveRecord\Model {
	
	static $belongs_to = array(
			array('item')
	);
}