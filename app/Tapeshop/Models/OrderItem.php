<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property int amount
 * @property int price
 * @property \Tapeshop\Models\Item item
 * @property \Tapeshop\Models\Size size
 * @property \Tapeshop\Models\Order order
 */
class Orderitem extends Model {

	static $belongs_to = array(
		array('item'),
		array('order'),
		array('size')
	);

	static $has_many = array(
		array('itemnumbers')
	);
}