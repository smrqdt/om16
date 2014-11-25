<?php

namespace Tapeshop\Rest;

use ActiveRecord\DateTime;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controllers\OrderStatus;
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

	public function payed($id) {
		$this->checkAdmin();

		try {
			/** @var Order $order */
			$order = Order::find_by_pk($id, array());
			if ($order->status != OrderStatus::SHIPPED) {
				$order->status = OrderStatus::PAYED;
			}
			$order->paymenttime = new DateTime();
			$order->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}

	public function notpayed($id) {
		$this->checkAdmin();

		try {
			/** @var Order $order */
			$order = Order::find_by_pk($id, array());
			if ($order->status != OrderStatus::SHIPPED) {
				$order->status = OrderStatus::NEW_ORDER;
			}
			$order->paymenttime = null;
			$order->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}

	public function shipped($id) {
		$this->checkAdmin();

		try {
			/** @var Order $order */
			$order = Order::find_by_pk($id, array());
			$order->status = OrderStatus::SHIPPED;
			$order->shippingtime = new DateTime();
			$order->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}

	public function notshipped($id) {
		$this->checkAdmin();

		try {
			/** @var Order $order */
			$order = Order::find_by_pk($id, array());
			if (empty($order->paymenttime)) {
				$order->status = OrderStatus::NEW_ORDER;
			} else {
				$order->status = OrderStatus::PAYED;
			}
			$order->shippingtime = null;
			$order->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}
} 