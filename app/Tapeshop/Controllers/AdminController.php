<?php
namespace Tapeshop\Controllers;

use Tapeshop\Controller;
use Tapeshop\Models\Item;
use Tapeshop\Models\Order;
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

		$items = Item::all(array("conditions" => array("deleted = false")));

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

		$orders = Order::all();

		$data = array(
			"orders" => $orders
		);

		$this->render("admin/orders.html", $data);
	}
}
