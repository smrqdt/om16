<?php
namespace Tapeshop\Controllers;


class Cart
{
	static function getCartCount()
	{
		$i = 0;
		if (isset($_SESSION["cart"])) {
			$cart = $_SESSION["cart"];
			foreach ($cart as $item) {
				$i += $item['amount'];
			}
		}
		return $i;
	}

	public static function clear()
	{
		$_SESSION["cart"] = array();
	}

	public static function increase($id, $size)
	{
		$cart = Cart::getCart();

		foreach ($cart as &$item) {
			if ($item["item"]->id == $id && $item["size"] == $size) {
				$item["amount"]++;
			}
		}
		$_SESSION["cart"] = $cart;
	}

	/**
	 * @return array The current cart or a dummy (empty array.)
	 */
	static function getCart()
	{
		$cart = array();
		if (isset($_SESSION["cart"])) {
			$cart = $_SESSION["cart"];
		}
		return $cart;
	}

	public static function addItem($item, $size, $support_price = 0)
	{
		$cart = Cart::getCart();
		$incart = false;
		foreach ($cart as $i => $ci) {
			if ($item->id == $ci["item"]->id) {
				if ((($size == null && $ci["size"] == "") || $size->id == $ci["size"]) && $ci['support_price'] == $support_price) {
					$incart = true;
					$cart[$i]["amount"] = $ci["amount"] + 1;
					break;
				}
			}
		}

		if (!$incart) {
			$a = array(
				"item" => $item,
				"size" => empty($size) ? null : $size->id,
				"amount" => 1,
				"support_price" => $support_price
			);
			array_push($cart, $a);
		}

		$_SESSION["cart"] = $cart;
	}

	public static function decrease($id, $size, $support_price = 0)
	{
		$cart = Cart::getCart();
		foreach ($cart as &$item) {
			if ($item["item"]->id == $id && $item["size"] == $size && $item["support_price"] == $support_price) {
				$item["amount"] = max(array(1, --$item["amount"]));
			}
		}
		$_SESSION["cart"] = $cart;
	}

	public static function changeSize($item_id, $currentSize, $newSize)
	{
		$cart = Cart::getCart();

		foreach ($cart as &$item) {
			if ($item["item"]->id == $item_id && $item["size"] == $currentSize) {
				$item["size"] = $newSize;
			}
		}
		$_SESSION["cart"] = $cart;
	}

	public static function remove($item_id, $size, $support_price = 0)
	{
		$cart = Cart::getCart();
		$newCart = array();

		foreach ($cart as $item) {
			if (!($item["item"]->id == $item_id && $item["size"] == $size && $item["support_price"] == $support_price)) {
				array_push($newCart, $item);
			}
		}
		$_SESSION["cart"] = $newCart;
	}

	public static function getAmount($item_id, $size, $support_price = 0)
	{
		$cart = Cart::getCart();

		foreach ($cart as &$item) {
			if ($item["item"]->id == $item_id && $item["size"] == $size && $item["support_price"] == $support_price) {
				return $item["amount"];
			}
		}
	}
}
