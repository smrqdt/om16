<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\DateTime;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Controllers\Helpers\Billing;
use Tapeshop\Controllers\Helpers\EmailOutbound;
use Tapeshop\Models\Item;
use Tapeshop\Models\Order;
use Tapeshop\Models\Size;
use Tapeshop\OutOfStockException;

/**
 * Handle orders.
 */
class OrderController extends Controller {

	/**
	 * Update the status for orders not payed after 14 days.
	 */
	public static function updateStatus() {
		$date = new DateTime();
		$orders = Order::find('all', array("conditions" => array("status = 'new' AND ordertime < ?", $date->sub(new \DateInterval("P14D")))));
		foreach ($orders as $order) {
			/** @var $order \Tapeshop\Models\Order */
			$order->status = 'overdue';
			$order->save();
		}
	}

	/**
	 * Submit a new order. Get all items form the cart and add them as OrderItem
	 */
	public function submitOrder() {
		$shipping = 0;
		/** @var $order \Tapeshop\Models\Order */
		$order = null;
		$c = Order::connection();
		try {
			$c->transaction();
			$order = $this->user->create_orders(array(
				'hashlink' => $this->gen_uuid(),
				'address_id' => $this->user->currentAddress()->id
			));

			$cart = $this->getCart();
			foreach ($cart as $ci) {
				/** @var $item Item */
				$item = null;
				/** @var $size Size */
				$size = null;

				$item = Item::find_by_pk($ci["item"]->id, array());
				if (!empty($ci["size"])) {
					$size = Size::find('first', array('conditions' => array("size LIKE ? and item_id = ?", $ci["size"], $ci["item"]->id)));
				}

				if ($item->manage_stock) {
					if ($size == null) {
						if ($item->stock > 1) {
							$item->stock--;
							$item->save();
						} else {
							throw new OutOfStockException(gettext("item.error.outofstock"), $item);
						}
					} else {
						if ($size->stock > 1) {
							$size->stock--;
							$size->save();
						} else {
							throw new OutOfStockException(gettext("size.error.outofstock"), $item, $size);
						}
					}
				}

				$order->create_orderitems(array(
					"item_id" => $item->id,
					"amount" => $ci["amount"],
					"size_id" => $size == null ? "" : $size->id,
					"price" => $ci["item"]->price
				));

				$shipping = max($shipping, $ci["item"]->shipping);
			}
			$order->shipping = $shipping;
			$order->save();
			$c->commit();
			$order->reload();
			$mailSuccess = EmailOutbound::sendBilling($order);

			if ($mailSuccess) {
				$this->app->flash('success', 'Notification Mail was sent!');
			} else {
				$this->app->flash('error', 'Could not send Notification Mail!');
			}
		} catch (RecordNotFound $e) {
			$c->rollback();
			$this->app->flash('error', gettext("item.error.notfound"));
			$this->redirect('cart');
		} catch (ActiveRecordException $e) {
			$c->rollback();
			$this->app->flash('error', "Could not create order! " . $e->getMessage());
			$this->redirect('cart');
		} catch (OutOfStockException $e) {
			$c->rollback();
			$this->app->flash('warn', $e->getMessage());
			$this->redirect('cart');
		} catch (\Exception $e) {
			echo $e;
		}

		$_SESSION["cart"] = array();

		$auth_user = $this->auth->getUser();
		if (!$auth_user['logged_in']) {
			unset($_SESSION["auth_user"]);
		}

		$url = $this->app->urlFor('order', array('hash' => $order->hashlink));
		$this->app->redirect($url);
	}

	/**
	 * Delete an order.
	 * @param int $id Id of the order.
	 * @var $order \Tapeshop\Models\Order
	 */
	public function deleteOrder($id) {
		$this->checkAdmin();
		$order = null;

		try {
			$order = Order::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', "Order not found1!");
			$this->redirect('adminorders');
		}

		try {
			$order->delete();
		} catch (ActiveRecordException $e) {
			$this->app->flash('error', "Could not delete order! " . $e->getMessage());
			$this->redirect('adminorders');
		}

		$this->redirect('adminorders');
	}

	/**
	 * Mark an order as payed.
	 * @param int $id
	 * @var $order \Tapeshop\Models\Order
	 */
	public function payed($id) {
		$this->checkAdmin();
		$order = null;

		try {
			$order = Order::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Order not found2!');
			$this->redirect('adminorders');
		}

		$order->paymenttime = new DateTime();
		$order->status = 'payed';

		try {
			$order->save();
			$mailSuccess = EmailOutbound::sendPaymentConfirmation($order);

			if ($mailSuccess) {
				$this->app->flash('success', 'Notification Mail was sent!');
			} else {
				$this->app->flash('error', 'Could not send Notification Mail!');
			}
		} catch (ActiveRecordException $e) {
			$this->app->flashNow('error', 'Could not update status!' . $e->getMessage() . $e->getTrace());
		}

		foreach ($order->orderitems as $orderitem) {
			if ($orderitem->item->numbered) {
				$freenumbers = $orderitem->item->getFreeNumbers();

				if (count($freenumbers) >= $orderitem->amount) {
					for ($i = 0; $i < $orderitem->amount; $i++) {
						/** @var $itemnumber \Tapeshop\Models\Itemnumber */
						$itemnumber = $freenumbers[$i];
						$itemnumber->orderitem_id = $orderitem->id;
						try {

							$itemnumber->save();
						} catch (ActiveRecordException $e) {
							$this->app->flashNow('error', 'Could not assign item number!');
						}
					}
				} else {
					$this->app->flashNow('error', 'Item ' . $orderitem->item->name . ' not enough numbers left!');
				}
			}
		}

		$this->order($order->hashlink);
	}

	/**
	 * Show an order and its details.
	 * @param String $hash Hash (UUID) of the order.
	 */
	public function order($hash) {
		$order = Order::find(
			'first',
			array(
				'hashlink' => $hash
			)
		);

		if ($order == null) {
			$this->app->flash('warn', "Order could not be found!" . $hash);
			$this->redirect('home');
		} else {

			$data = array(
				"order" => $order,
				"paypal_merchant_id" => PAYPAL_MERCHANT_ID
			);

			$this->render("order/order.html", $data);
		}
	}

	/**
	 * Generate invoice PDF.
	 * @param String $hash Hash (UUID) of the order
	 */
	function billing($hash) {
		$this->checkAdmin();

		$order = Order::find(
			'first',
			array(
				'hashlink' => $hash
			)
		);

		if ($order == null) {
			$this->app->flash('warn', "Order could not be found!");
			$this->redirect('home');
		} else {
			$billing = new Billing('P', 'mm', 'A4');
			$billing->order = $order;
			$billing->AddPage();
			$billing->Output();
			$this->app->response()->header("Content-Type", "application/pdf");
		}
	}

	/**
	 * Mark an order as shipped.
	 * @param int $id
	 */
	public function shipped($id) {
		$this->checkAdmin();

		/** @var $order \Tapeshop\Models\Order */
		$order = null;

		try {
			$order = Order::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Order not found3!');
			$this->redirect('adminorders');
		}

		$order->shippingtime = new DateTime();
		$order->status = 'shipped';
		$order->address_id = $order->user->currentAddress()->id;

		try {
			$order->save();
			$mailSuccess = EmailOutbound::sendShippedConfirmation($order);

			if ($mailSuccess) {
				$this->app->flash('success', 'Notification Mail was sent!');
			} else {
				$this->app->flash('error', 'Could not send Notification Mail!');
			}
		} catch (ActiveRecordException $e) {
			$this->app->flashNow('error', 'Could not update status!');
		}

		$this->order($order->hashlink);
	}
}
