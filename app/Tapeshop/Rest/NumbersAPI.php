<?php
/**
 * Created by IntelliJ IDEA.
 * User: robert
 * Date: 20.10.14
 * Time: 19:43
 */

namespace Tapeshop\Rest;

use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Item;

class NumbersAPI extends RestController {

	public function updateManageNumbers($id) {
		$this->checkAdmin();
		$numbered = $this->params()->numbered;
		try {
			/** @var Item $item */
			$item = Item::find_by_pk($id, array());
			$item->numbered = $numbered;
			$item->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Could not find item with id " . $id), 404);
		}
	}
} 