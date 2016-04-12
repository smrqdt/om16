<?php
namespace Tapeshop\Models;

use ActiveRecord\Model;
use ActiveRecord\RecordNotFound;

/**
 * @property String name
 * @property String description
 * @property int price
 * @property int shipping
 * @property int stock
 * @property boolean manage_stock
 * @property String image
 * @property int id
 * @property array orderitems
 * @property $sizes Size
 * @property boolean numbered
 * @property boolean ticketcode
 * @property int sort_order
 * @property boolean support_ticket
 */
class Item extends Model {

	static $has_many = array(
		array('sizes', array('conditions' => array('deleted = false'))),
		array('orderitems')
	);

	function getSizesCount() {
		return array_filter($this->sizes, function ($object) { return !$object->deleted; });
	}

	/**
	 * @param int $amount Amount of items
	 * @param mixed $variation Variation of item
	 * @return bool Item is in stock
	 */
	function inStock($amount = 1, $variation = null) {
		if (!$this->manage_stock && !$this->numbered) {
			return true;
		}

		if ($this->manage_stock) {
			if (count($this->sizes) == 0) {
				if ($this->stock >= $amount) {
					return true;
				}
			} else {
				if ($variation == null) {
					/**@var Size $size */
					foreach($this->sizes as $size){
						if ($size->stock >= $amount) {
							return true;
						}
					}
					return false;
				}

				if ($variation instanceof Size) {
					if ($variation->stock >= $amount) {
						return true;
					} else {
						return false;
					}
				} else {
					try {
						/** @var Size $size */
						$size = Size::find_by_pk($variation, array());
						if ($size->stock >= $amount) {
							return true;
						}
					} catch (RecordNotFound $e) {
						return false;
					}
				}
			}
		}

		return false;
	}
}
