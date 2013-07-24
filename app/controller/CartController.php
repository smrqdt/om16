<?php

class CartController extends Controller{
	public function addItem($id){
		try{
			$item = Item::find($id);
			$size = $this->post("size");
			$cart = $this->getCart();
			$incart = false;
			foreach($cart as $i => $ci){
				if($item->id == $ci["item"]->id && $size == $ci["size"]){
					$incart = true;
					$cart[$i]["amount"] = $ci["amount"] +1;
					print_r($ci);
					break;
				}
			}
		
			if (!$incart){
				$a = array(
						"item" => $item,
						"size" => $size,
						"amount" => 1
				);
				array_push($cart, $a);
			}
		
			$_SESSION["cart"] = $cart;
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Could not add Item to cart, because the Item was not found!');
		}
		$this->redirect('home');
	}
	
	public function cart(){
		$cart = $this->getCart();
		$sum = 0;
	
		foreach ($cart as $item){
			$sum += ($item["item"]->price / 100) * $item["amount"];
		}
	
		$data = array(
				"cart" => $cart,
				"sum" => $sum
		);
		$this->render("cart/cart.tpl", $data);
	}
	
	public function clearCart(){
		$_SESSION["cart"] = array();
		$this->redirect('home');
	}
}