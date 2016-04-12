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

	public static function increase($id, $size, $support_price = 0)
	{
		$cart = Cart::getCart();

		$numberOfArticle = 0;
		foreach ($cart as $i => $ci) {
			if ($ci["item"]->id == $id) {
				$numberOfArticle += $ci["amount"];
			}
		}

		if ($numberOfArticle < 5) {
			foreach ($cart as &$item) {
				if (Cart::cartItemMatches($item, $id, $size, $support_price)) {
					$item["amount"]++;
				}
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

		$numberOfArticle = 0;
		foreach ($cart as $i => $ci) {
			if ($ci["item"]->id == $item->id) {
				$numberOfArticle += $ci["amount"];
			}
		}

		if ($numberOfArticle < 5) {
			foreach ($cart as $i => $ci) {
				if (Cart::cartItemMatches($ci, $item->id, $size, $support_price)) {

					$cart[$i]["amount"] = $ci["amount"] + 1;


					$_SESSION["cart"] = $cart;
					return;
				}
			}

			$a = array(
				"item" => $item,
				"size" => empty($size) ? null : $size,
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
			if (Cart::cartItemMatches($item, $id, $size, $support_price)) {
				$item["amount"] = max(array(1, --$item["amount"]));
			}
		}
		$_SESSION["cart"] = $cart;
	}

	public static function changeSize($item_id, $currentSize, $newSize, $support_price = 0)
	{
		$cart = Cart::getCart();

		foreach ($cart as &$item) {
			if (Cart::cartItemMatches($item, $item_id, $currentSize, $support_price)) {
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
			if (!Cart::cartItemMatches($item, $item_id, $size, $support_price)) {
				array_push($newCart, $item);
			}
		}
		$_SESSION["cart"] = $newCart;
	}

	public static function getAmount($item_id, $size, $support_price = 0)
	{
		$cart = Cart::getCart();

		foreach ($cart as &$item) {
			if (Cart::cartItemMatches($item, $item_id, $size, $support_price)) {
				return $item["amount"];
			}
		}
		return 0;
	}

	public static function cartItemMatches($cartItem, $id, $size = null, $support_price = 0)
	{
		if ($cartItem["item"]->id == $id) {
			if ($cartItem["size"] == $size || $size == null && $cartItem["size"] == "") {
				if ($cartItem["support_price"] == $support_price) {
					return true;
				}
			}
		}
		return false;
	}
}
