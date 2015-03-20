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

        Cart::addItem($item, $size);

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
        $json .= '"amount": ' . $cartItem["amount"];
        $json .= "}";
        return $json;
    }

    function remove($item_id)
    {
        $size_id = $this->params()->size;

        $amount = Cart::getAmount($item_id, $size_id);

        if ($amount > 1) {
            Cart::decrease($item_id, $size_id);
        } else {

            Cart::remove($item_id, $size_id);
        }

        $this->get();
    }

    function clear(){
        Cart::clear();
        $this->get();
    }
}
