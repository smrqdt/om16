<?php

namespace Tapeshop\Rest;

use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Item;

class Items extends RestController {

	public function get($id) {
		$item = null;
		try {
			$item = Item::find_by_pk($id, array("conditions" => array("deleted = false")));
			$this->response($item->to_json(array('include' => array('sizes'))));
		} catch (RecordNotFound $e) {
			$this->response(array("error" => "Item with id " . $id . " not found!"), 404);
		}
	}
}
