<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

class Size extends Model {

	static $belongs_to = array(
		array('item')
	);
}