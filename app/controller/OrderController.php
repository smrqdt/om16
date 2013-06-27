<?php

class OrderController extends Controller{
	public function submitOrder(){
		$user = User::find($this->user["id"]);
	
		// TODO: generate order and bill numbers
		$order = $user->create_orders(array(
				'number' => "asdf",
				'bill' => "asdf",
				'hashlink'=> $this->gen_uuid()
		));
	
		$cart = $this->getCart();
		foreach($cart as $ci){
			$order->create_orderitems(array(
					"item_id" => $ci["item"]->id,
					"amount" => $ci["amount"],
					"size" => $ci["size"]
			)
			);
		}
		$_SESSION["cart"] = array();
	
		$data = array(
				"order" => $order,
				"noCartItems" => 0
		);
		if(!isset($this->user["logged_in"])){
			unset($_SESSION["auth_user"]);
		}
		// send email
		$url = $this->app->urlFor('order', array('hash'=> $order->hashlink));
		$this->app->redirect($url);
	}
	
	public function order($hash){
		$order = Order::find(
				'first',
				array(
						'hashlink' => $hash
				)
		);
	
		if($order == null){
			$this->app->flashNow('warning', "Order could not be found!");
			$this->redirect('home');
		}else{
			$cart = $this->getCart();
	
			if(!isset($this->user) || $this->user == null){
				$this->user = array("logged_in"=> false);
			}
	
			$data = array(
					"order" => $order,
					"noCartItems"=> count($cart),
					"user" => $this->user
			);
	
			$this->render("order/order.tpl", $data);
		}
	}
	
	public function deleteOrder($id){
		$this->checkAdmin();
	
		$order = Order::find($id);
		$order->delete();
	
		// TODO check for errors
		$this->redirect('admin');
	}
}