<?php

namespace Tapeshop\Rest;

use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Order;

class OrdersAPI extends RestController {

	public function get($id) {
		$this->checkAdmin();

		try {
			$order = Order::find_by_pk($id, array());
			$this->response($order->to_json(array("include" => array("user", "address", "orderitems", "items"))));
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}

	public function updateStatus($id){
		$this->checkAdmin();

		$status = $this->params()->status;

		try {
			$order = Order::find_by_pk($id, array());
			$order->status = status;
			$order->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}
} 