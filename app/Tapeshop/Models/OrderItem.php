<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property int amount
 * @property int price
 * @property \Tapeshop\Models\Item item
 * @property int item_id
 * @property \Tapeshop\Models\Size size
 * @property int size_id
 * @property \Tapeshop\Models\Order order
 * @property int order_id
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