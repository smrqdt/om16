<?php
namespace Tapeshop\Rest;


use ActiveRecord\RecordNotFound;
use Tapeshop\Controllers\Cart;
use Tapeshop\Models\Item;
use Tapeshop\Models\Size;

class CartsAPI extends RestController
{

    function add($item_id)
    {
        $size_id = $this->params()->size;
		$support_price = $this->params()->support_price;

        /** @var Item $item */
        $item = null;
        try {
            $item = Item::find_by_pk($item_id, array("conditions" => array("deleted = false")));
        } catch (RecordNotFound $e) {
            $this->response(array("error" => "Item with id " . $item_id . " not found!"), 404);
        }

        $size = null;

        if (!empty($size_id)) {
            try {
                $size = Size::find_by_pk($size_id, array());
            } catch (RecordNotFound $e) {
                $this->response(array("error" => "Size with id " . $size_id . " not found!"), 404);
            }
        }

		if($item->support_ticket){
			if($support_price < 500){
				$this->response(array("error" => "Support price must be at least 500!"), 400);
			}
		}else{
			$support_price = 0;
		}

        Cart::addItem($item, $size, $support_price);

        $this->get();
    }

    function get()
    {
        $cartItems = Cart::getCart();

        $json = "[";

        foreach ($cartItems as $cartItem) {
            $json .= $this->cartItemToJson($cartItem);
            if ($cartItem !== end($cartItems)) {
                $json .= ", ";
            }
        }

        $json .= "]";

        $this->response($json);
    }

    private function cartItemToJson($cartItem)
    {
        $json = "{";
        $json .= '"item": ' . $cartItem["item"]->to_json(array('include' => array('sizes'))) . ", ";
        if (!empty($cartItem["size"])) {
            $json .= '"size": ' . $cartItem["size"] . ", ";
        } else {
            $json .= '"size": null, ';
        }
        $json .= '"amount": ' . $cartItem["amount"]. ", ";
		$json .= '"support_price": '. $cartItem["support_price"];
        $json .= "}";
        return $json;
    }

    function remove($item_id)
    {
        $size_id = $this->params()->size;
		$support_price = $this->params()->support_price;

        $amount = Cart::getAmount($item_id, $size_id, $support_price);

        if ($amount > 1) {
            Cart::decrease($item_id, $size_id, $support_price);
        } else {

            Cart::remove($item_id, $size_id, $support_price);
        }

        $this->get();
    }

    function clear(){
        Cart::clear();
        $this->get();
    }
}
