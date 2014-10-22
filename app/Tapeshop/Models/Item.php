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
 * @property boolean ticketscript
 * @property String image
 * @property int id
 * @property array orderitems
 * @property $sizes Size
 * @property boolean numbered
 * @property array itemnumbers
 * @property boolean shownumbers
 */
class Item extends Model {

	static $has_many = array(
		array('sizes', array('conditions' => array('deleted = false'))),
		array('orderitems'),
		array('itemnumbers')
	);

	function getUnrequestedNumberCount() {
		return count($this->getFreeNumbers());
	}

	function getFreeNumbers() {
		return Itemnumber::find('all', array('conditions' => array('item_id = ? and valid = true and orderitem_id IS NULL', $this->id)));
	}

	function getInvalidNumbers() {
		return Itemnumber::find('all', array('conditions' => array('item_id = ? and valid = false', $this->id)));
	}

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
					return ($this->stock >= $amount);
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

		if ($this->numbered) {
			if (count($this->getFreeNumbers()) >= $amount) {
				return true;
			}
		}
		return false;
	}
}