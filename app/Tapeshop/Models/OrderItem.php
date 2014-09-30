<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property int amount
 * @property int price
 * @property \Tapeshop\Models\Item item
 */
class Orderitem extends Model {

	static $belongs_to = array(
		array('item'),
		array('order')
	);

	static $has_many = array(
		array('itemnumbers')
	);
}