<?php

namespace Tapeshop\Rest;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Item;
use Tapeshop\Models\Itemnumber;
use Tapeshop\Models\Size;


class Items extends RestController{

	public function get($id){
		$item = null;
		try {
			$item = Item::find_by_pk($id, array("conditions" => array("deleted = false")));
		} catch (RecordNotFound $e) {
			$this->response("");
		}
		$this->response($item->to_json(array('include'=>array('sizes'))));
	}
} 