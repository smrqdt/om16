<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property int stock
 * @property String size
 * @property \Tapeshop\Models\Item item
 * @property int id
 * @property int item_id
 */
class Size extends Model {

	static $belongs_to = array(
		array('item')
	);
}