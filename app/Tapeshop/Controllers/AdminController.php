<?php
namespace Tapeshop\Controllers;

use DateTime;
use Tapeshop\Controller;
use Tapeshop\Controllers\Helpers\EmailOutbound;
use Tapeshop\Models\Item;
use Tapeshop\Models\Itemnumber;
use Tapeshop\Models\Order;
use Tapeshop\Models\Orderitem;
use Tapeshop\Models\User;

/**
 * Controller to handle administrative views.
 */
class AdminController extends Controller {

	/**
	 * Show list of all users/customers.
	 */
	public function index() {
		$this->checkAdmin();

		$orders = Order::all();
		$users = User::all();

		$data = array(
			"orders" => $orders,
			"users" => $users
		);

		$this->render("admin/users.html", $data);
	}

	/**
	 * Show list of all items/articles.
	 */
	public function items() {
		$this->checkAdmin();

		$items = Item::all(array("conditions" => array("deleted = false"), "order" => "sort_order asc"));

		$data = array(
			"items" => $items
		);

		$this->render("admin/items.html", $data);
	}

	/**
	 * Show list of all orders.
	 */
	public function orders() {
		$this->checkAdmin();

		$this->updateOrderStatus();

		$orders = Order::all();

		$data = array(
			"orders" => $orders
		);

		$this->render("admin/orders.html", $data);
	}

	/**
	 * Update the status for orders not payed after 14 days.
	 */
	private function updateOrderStatus(){
		$date = new DateTime();

		// send reminder email
		$orders = Order::find('all', array("conditions" => array("status = 'new' AND ordertime < ?", $date->sub(new \DateInterval("P10D")))));
		foreach ($orders as $order) {
			/** @var $order \Tapeshop\Models\Order */
			if (!$order->reminder_sent) {
				EmailOutbound::sendReminder($order);

				$order->reminder_sent = true;
				$order->save();
			}
		}

		// cancel late orders
		$orders = Order::find('all', array("conditions" => array("status = 'new' AND ordertime < ?", $date->sub(new \DateInterval("P17D")))));
		$c = Order::connection();
		foreach ($orders as $order) {
			$c->transaction();
			/** @var $order \Tapeshop\Models\Order */
			$order->status = OrderStatus::OVERDUE;
			$order->save();

			/** @var $oi OrderItem */
			foreach ($order->orderitems as $oi) {
				$item = $oi->item;
				if ($item->numbered) {
					/** @var $in ItemNumber */
					foreach ($oi->itemnumbers as $in) {
						$in->orderitem_id = null;
						$in->save();
					}
				}

				if ($item->manage_stock) {
					if (!empty($oi->size)) {
						$oi->size->stock += $oi->amount;
						$oi->size->save();
					}
					$item->stock += $oi->amount;
					$item->save();
				}
			}
			$c->commit();
		}
	}
}
