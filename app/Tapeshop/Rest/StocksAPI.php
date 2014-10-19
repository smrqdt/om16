<?php

namespace Tapeshop\Rest;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Item;
use Tapeshop\Models\Size;

class StocksAPI extends RestController {
	public function addVariation($id) {
		$this->checkAdmin();

		$stock = $this->params()->amount;
		try {
			/** @var $size Size */
			$size = Size::find_by_pk($id, array());
			$size->stock = max(0, $size->stock + $stock);
			$size->save();
			$this->updateItemStock($size->item);
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Size with id " . $id . "not found!"), 404);
		}
	}

	public function addItem($id) {
		$this->checkAdmin();

		$stock = $this->params()->amount;
		try {
			/** @var $item Item */
			$item = Item::find_by_pk($id, array());
			$item->stock = max(0, $item->stock + $stock);
			$item->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Item with id " . $id . "not found!"), 404);
		}
	}

	/**
	 * Update the sum of variations in stock for an item.
	 * @param $item Item
	 */
	private function updateItemStock($item) {
		$stock = 0;

		/** @var $size Size */
		foreach ($item->sizes as $size) {
			$stock += $size->stock;
		}
		$item->stock = $stock;
		try {
			$item->save();
		} catch (ActiveRecordException $e) {
			$this->haltReponse(array("error" => "Could not update item stock! " . $e->getMessage()), 500);
		}
	}
}
