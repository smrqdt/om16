<?php
namespace Tapeshop\Controllers;

use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Item;

/**
 * Handle shopping cart functionality.
 */
class CartController extends Controller {

	/**
	 * Add an item to the shopping cart.
	 * @param int $id Id of the item.
	 */
	public function addItem($id) {
		try {
			$item = Item::find($id);
			$size = $this->post("size");
			$cart = $this->getCart();
			$incart = false;
			foreach ($cart as $i => $ci) {
				if ($item->id == $ci["item"]->id && $size == $ci["size"]) {
					$incart = true;
					$cart[$i]["amount"] = $ci["amount"] + 1;
					print_r($ci);
					break;
				}
			}

			if (!$incart) {
				$a = array(
					"item" => $item,
					"size" => $size,
					"amount" => 1
				);
				array_push($cart, $a);
			}

			$_SESSION["cart"] = $cart;
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Could not add item to cart, because the item was not found!');
		}
		$this->redirect('home');
	}

	/**
	 * Remove all items from the shopping cart. Redirects to Home screen.
	 */
	public function clearCart() {
		$_SESSION["cart"] = array();
		$this->redirect('home');
	}

	/**
	 * Increase the amount of an ordered item.
	 * @param POST int id Id of the item
	 * @param POST String size Variation of the item
	 */
	public function increase() {
		$cart = $this->getCart();
		$id = $this->post("id");
		$size = $this->post("size");

		foreach ($cart as &$item) {
			if ($item["item"]->id == $id && $item["size"] == $size) {
				$item["amount"]++;
			}
		}
		$_SESSION["cart"] = $cart;
		$this->cart();
	}

	/**
	 * Show the shopping cart and its contents.
	 */
	public function cart() {
		$cart = $this->getCart();
		$sum = 0;
		$shipping = 0;

		foreach ($cart as $item) {
			$sum += ($item["item"]->price / 100) * $item["amount"];
			$shipping = max(array($shipping, $item["item"]->shipping));
		}
		$sum += ($shipping / 100);

		$data = array(
			"cart" => $cart,
			"shipping" => $shipping,
			"sum" => $sum
		);
		$this->render("cart/cart.html", $data);
	}

	/**
	 * Decrease the amount of an ordered item, to the minimum of 1.
	 * @param POST int id Id of the item
	 * @param POST String size Variation of the item
	 */
	public function decrease() {
		$cart = $this->getCart();
		$id = $this->post("id");
		$size = $this->post("size");

		foreach ($cart as &$item) {
			if ($item["item"]->id == $id && $item["size"] == $size) {
				$item["amount"] = max(array(1, --$item["amount"]));
			}
		}
		$_SESSION["cart"] = $cart;
		$this->cart();
	}

	/**
	 * Remove an item from the shopping cart
	 * @param POST int id Id of the item
	 * @param POST String size variation of the item
	 */
	public function remove() {
		$cart = $this->getCart();
		$newCart = array();
		$id = $this->post("id");
		$size = $this->post("size");

		foreach ($cart as $item) {
			if (!($item["item"]->id == $id && $item["size"] == $size)) {
				array_push($newCart, $item);
			}
		}
		$_SESSION["cart"] = $newCart;
		$this->cart();
	}
}