<?php

namespace Tapeshop\Rest;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Item;
use Tapeshop\Models\Size;

class VariationsAPI extends RestController {

	public function add() {
		$this->checkAdmin();

		$itemId = $this->params()->itemId;
		$variationName = $this->params()->name;
		$stock = $this->params()->stock;

		$item = null;

		try {
			$item = Item::find($itemId);
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Item not found"), 404);
		}

		try {
			$item->create_sizes(array(
				"size" => $variationName, "stock" => $stock
			));
		} catch (ActiveRecordException $e) {
			$this->haltReponse(array('error' => 'Could not add variation. ' . $e->getMessage()), 500);
		}
	}

	public function delete($id) {
		$this->checkAdmin();

		try {
			$size = Size::find($id);
			$size->delete();
		} catch (ActiveRecordException $e) {
			$this->haltReponse(array('error' => 'Could not delete size! ' . $e->getMessage()), 500);
		}
	}
}