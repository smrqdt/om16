<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property String name
 * @property String description
 * @property int price
 * @property int shipping
 * @property int stock
 * @property boolean manage_stock
 * @property boolean ticketscript
 * @property String image
 * @property int id
 * @property array orderitems
 * @property $sizes Size
 */
class Item extends Model {

	static $has_many = array(
		array('sizes'),
		array('orderitems'),
		array('itemnumbers')
	);

	function getUnrequestedNumberCount() {
		$count = count($this->getFreeNumbers());

		if ($count == 0) {
			return 0;
		}

		foreach ($this->orderitems as $oi) {
			if ($oi->order->status != 'overdue') {
				$count -= $oi->amount;
			}
		}
		return $count;
	}

	function getFreeNumbers() {
		return Itemnumber::find('all', array('conditions' => array('item_id = ? and valid = true and free = true', $this->id)));
	}

	function getInvalidNumbers() {
		return Itemnumber::find('all', array('conditions' => array('item_id = ? and valid = false', $this->id)));
	}
}