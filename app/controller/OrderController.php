<?php
/**
 * Handle orders.
 */
use ActiveRecord\DateTime;
class OrderController extends Controller{
	
	/**
	 * Submit a new order. Get all items form the cart and add them as OrderItem
	 */
	public function submitOrder(){
		$shipping = 0;
		$c = Order::connection();
		try{
			$c->transaction();
			$order = $this->user->create_orders(array(
					'hashlink'=> $this->gen_uuid(),
					'address_id' => $this->user->currentAddress()->id
			));
		
			$cart = $this->getCart();
			foreach($cart as $ci){
				$order->create_orderitems(array(
						"item_id" => $ci["item"]->id,
						"amount" => $ci["amount"],
						"size" => $ci["size"],
						"price" => $ci["item"]->price
				));
				
				$shipping = max(array($shipping, $ci["item"]->shipping));
			}
			$order->shipping = $shipping;
			$order->save();
			$c->commit();
			$order->reload();
			$mailSuccess = EmailOutbound::sendNotification($order);

			if($mailSuccess){
				$this->app->flash('success', 'Notification Mail was sent!');
			}else{
				$this->app->flash('error', 'Could not send Notification Mail!');
			}

		}catch(ActiveRecord\ActiveRecordException $e){
			$c->rollback();
			$this->app->flash('error', "Could not create order! " . $e->getMessage());
			$this->redirect('checkout');
		}
		
		$_SESSION["cart"] = array();
	
		$auth_user = $this->auth->getUser();
		if(!$auth_user['logged_in']){
			unset($_SESSION["auth_user"]);
		}
		
		$url = $this->app->urlFor('order', array('hash'=> $order->hashlink));
		$this->app->redirect($url);
	}
	
	/**
	 * Show an order and its details.
	 * @param String $hash Hash (UUID) of the order.
	 */
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
	
			$data = array(
					"order" => $order
			);

			$this->render("order/order.html", $data);
		}
	}
	
	/**
	 * Delete an order.
	 * @param int $id Id of the order.
	 */
	public function deleteOrder($id){
		$this->checkAdmin();
		
		try {
			$order = Order::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', "Order not found1!");
			$this->redirect('adminorders');
		}
		
		try{
			$order->delete();
		}catch(ActiveRecord\ActiveRecordException $e){
			$this->app->flash('error', "Could not delete order! " . $e->getMessage());
			$this->redirect('adminorders');
		}
	
		$this->redirect('adminorders');
	}
	
	/**
	 * Mark an order as payed.
	 * @param int $id
	 */
	public function payed($id){
		$this->checkAdmin();
		
		try{
			$order = Order::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Order not found2!');
			$this->redirect('adminorders');
		}
		
		$order->paymenttime = new DateTime();
		$order->status = 'payed';
		
		try{
			$order->save();
			$mailSuccess = EmailOutbound::sendNotification($order);

			if($mailSuccess){
				$this->app->flash('success', 'Notification Mail was sent!');
			}else{
				$this->app->flash('error', 'Could not send Notification Mail!');
			}

		}catch(ActiveRecord\ActiveRecordException $e){
			$this->app->flashNow('error', 'Could not update status!' . $e->getMessage() . $e->getTrace());
		}
		
		foreach($order->orderitems as $orderitem){
			if($orderitem->item->numbered){
				$freenumbers = $orderitem->item->getFreeNumbers();

				if(count($freenumbers) >= $orderitem->amount){
					for($i = 0; $i < $orderitem->amount; $i++){
						$itemnumber = $freenumbers[$i];
						$itemnumber->orderitem_id = $orderitem->id;
						try{
							$itemnumber->save();
						}catch(ActiveRecord\ActiveRecordException $e){
							$this->app->flashNow('error', 'Could not assign item number!');
						}
					}
				}else{
					$this->app->flashNow('error', 'Item ' . $orderitem->item->name . ' not enough numbers left!');
				}
			}
		}
		
		$this->order($order->hashlink);
	}

	/**
	 * Generate invoice PDF.
	 * @param String $hash Hash (UUID) of the order
	 */
	function billing($hash){
		$this->checkAdmin();

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
			$billing = new Billing('P','mm','A4');
			$billing->order = $order;
			$billing->AddPage();
			$billing->Output();
			$app->response()->header("Content-Type", "application/pdf");
		}
	}
	
	/**
	 * Mark an order as shipped.
	 * @param int $id
	 */
	public function shipped($id){
		$this->checkAdmin();
		
		try {
			$order = Order::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Order not found3!');
			$this->redirect('adminorders');
		}
		
		$order->shippingtime = new DateTime();
		$order->status = 'shipped';
		$order->address_id = $order->user->currentAddress()->id;
		
		try{
			$order->save();
			$mailSuccess = EmailOutbound::sendNotification($order);

			if($mailSuccess){
				$this->app->flash('success', 'Notification Mail was sent!');
			}else{
				$this->app->flash('error', 'Could not send Notification Mail!');
			}

		}catch(ActiveRecord\ActiveRecordException $e){
			$this->app->flashNow('error', 'Could not update status!');
		}

		
		$this->order($order->hashlink);
	}
	
	/**
	 * Update the status for orders not payed after 14 days.
	 */
	public static function updateStatus(){
		$date = new DateTime();
		$orders = Order::find('all', array("conditions" => array("status = 'new' AND ordertime < ?", $date->sub(new DateInterval("P14D")))));
		foreach ($orders as $order){
			$order->status = 'overdue';
			$order->save();
		}
	}
}