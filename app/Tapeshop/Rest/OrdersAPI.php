<?php

namespace Tapeshop\Rest;

use ActiveRecord\DateTime;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controllers\Helpers\EmailOutbound;
use Tapeshop\Controllers\OrderStatus;
use Tapeshop\Models\Order;
use Tapeshop\Models\Orderitem;

class OrdersAPI extends RestController
{

	public function get($id)
	{
		$this->checkAdmin();

		try {
			$order = Order::find_by_pk($id, array());
			$this->response($order->to_json(array("include" => array("user", "address", "orderitems", "items"))));
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}

	public function payed($id)
	{
		$this->checkAdmin();

		try {
			/** @var Order $order */
			$order = Order::find_by_pk($id, array());
			if ($order->status != OrderStatus::SHIPPED) {
				$order->status = OrderStatus::PAYED;
			}
			$order->paymenttime = new DateTime();
			$order->save();
			EmailOutbound::sendPaymentConfirmation($order);
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}

	public function notpayed($id)
	{
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

	public function shipped($id)
	{
		$this->checkAdmin();

		try {
			/** @var Order $order */
			$order = Order::find_by_pk($id, array());
			$order->status = OrderStatus::SHIPPED;
			$order->shippingtime = new DateTime();
			$order->save();
			EmailOutbound::sendShippedConfirmation($order);
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Order with id " . $id . " not found!"), 404);
		}
	}

	public function notshipped($id)
	{
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

	public function findByTicketcode($ticketcode)
	{
		$order = Order::find(
			'first',
			array(
				'conditions' => array(
					"hashlink LIKE '" . $ticketcode . "%'"
				)
			)
		);

		$query = Order::connection()->last_query;

		if ($order == null) {
			$this->haltReponse(array("error" => "Order with ticket code " . $ticketcode . " not found!", "query" => $query), 404);
		} else {
			$this->response($order->to_json());
		}
	}

	public function allAsCsv()
	{
		$this->checkAdmin();

		$orders = Order::all();
		$response = "ticketcode;id;name;bezahlt;names\n";
		foreach ($orders as $order) {
			// ticketcode, bestellnummer, name, bezahlt, personen?
			if ($order->hasTicketCodes()) {
				$response .= $order->getTicketcode();
			}

			$response .= ";" . $order->id;
			$response .= ";" . $order->address->name;
			$response .= ";" . ($order->paymenttime == null ? "ja" : "nein");
			$response .= ";";

			$tickets = 0;

			foreach ($order->orderitems as $orderitem) {
				if ($orderitem->item->ticketcode)
					$tickets += $orderitem->amount;
			}
			$response .= $tickets;

			$response .= "\n";
		}

		$this->response($response);
	}
} 
