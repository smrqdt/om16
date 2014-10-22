<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property int item_id
 * @property int number
 * @property int orderitem_id
 * @property \Tapeshop\Models\OrderItem orderitem
 * @property boolean valid
 */
class Itemnumber extends Model {
	static $belongs_to = array(
		array('item'),
		array('orderitem')
	);
}
