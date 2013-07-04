<?php

class Itemnumber extends ActiveRecord\Model{
	static $belongs_to = array(
		array('item'),
		array('orderitem')
	);
}
