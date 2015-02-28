<?php
namespace Tapeshop\Controllers;

use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Item;
use Tapeshop\Models\Size;

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
			$size = Size::find('first', array('conditions' => array('item_id = ? AND size LIKE ? AND deleted = false', $id, $this->post("size"))));

            Cart::addItem($item, $size);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Could not add item to cart, because the item was not found!');
		}
		$this->redirect('home');
	}

	/**
	 * Remove all items from the shopping cart. Redirects to Home screen.
	 */
	public function clearCart() {
        Cart::clear();
		$this->redirect('home');
	}

	/**
	 * Increase the amount of an ordered item.
	 * @param POST int id Id of the item
	 * @param POST String size Variation of the item
	 */
	public function increase() {
        $id = $this->post("id");
        $size = $this->post("size");

        Cart::increase($id, $size);

		$this->redirect('cart');
	}

	/**
	 * Show the shopping cart and its contents.
	 */
	public function cart() {

		$outofstock = array('items' => array(), 'sizes' => array());
		if (isset($_SESSION['out_of_stock'])) {
			$outofstock = $_SESSION['out_of_stock'];
			unset($_SESSION['out_of_stock']);
		}

		$cart = Cart::getCart();
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
			"sum" => $sum,
			"outofstock" => $outofstock
		);

		$this->render("cart/cart.html", $data);
	}

	/**
	 * Decrease the amount of an ordered item, to the minimum of 1.
	 * @param POST int id Id of the item
	 * @param POST String size Variant of the item
	 */
	public function decrease() {
		$id = $this->post("id");
		$size = $this->post("size");

        Cart::decrease($id, $size);

		$this->redirect('cart');
	}

	/**
	 * Change the size/variant of an item in the cart.
	 * @param POST int id Id of the item
	 * @param POST String currentSize Current variant of the item.
	 * @param POST String newSize New variant of the item.
	 */
	public function changeSize() {
		$item_id = $this->post("id");
		$currentSize = $this->post("currentSize");
		$newSize = $this->post("newSize");

        Cart::changeSize($item_id, $currentSize, $newSize);

		$this->redirect('cart');
	}

	/**
	 * Remove an item from the shopping cart
	 * @param POST int id Id of the item
	 * @param POST String size variation of the item
	 */
	public function remove() {
		$item_id = $this->post("id");
		$size = $this->post("size");

        Cart::remove($item_id, $size);
        
		if (Cart::getCartCount() > 0) {
			$this->redirect('cart');
		} else {
			$this->redirect('home');
		}
	}
}
