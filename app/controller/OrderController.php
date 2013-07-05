<?php

use ActiveRecord\DateTime;
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
			$this->app->flash('warn', "Order could not be found!");
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
	
	public function payed($id){
		$this->checkAdmin();
		
		$order = Order::find($id);
		$order->paymenttime = new DateTime();
		$order->status = 'payed';
		$order->save();
		
		foreach($order->orderitems as $orderitem){
			if($orderitem->item->numbered){
				$freenumbers = $orderitem->item->getFreeNumbers();
				// TODO handle numbers out of range
				// this is more a dirty hack and this needs to be fixed properly with a good specification.
				if(count($freenumbers) != 0){
					$itemnumber = $freenumbers[0];
					$itemnumber->orderitem_id = $orderitem->id;
					$itemnumber->save();
				}else{
					$this->app->flashNow('error', 'Item ' . $orderitem->item->name . ' number sout of range!');
				}
			}
		}
		$this->order($order->hashlink);
	}
	
	public function shipped($id){
		$this->checkAdmin();
		
		$order = Order::find($id);
		$order->shippingtime = new DateTime();
		$order->status = 'shipped';
		$order->address_id = $order->user->currentAddress()->id;
		$order->save();

		$this->order($order->hashlink);
	}
}