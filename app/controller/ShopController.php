<?php

class ShopController extends Controller {

	public function index(){
			
		$items = Item::getAll();
		$cart = $this->getCart();
		if(!isset($this->user) || $this->user == null){
			$this->user = array("logged_in"=> false);
		}

		$data = array(
				"items" => $items,
				"noCartItems" => count($cart),
				"user" => $this->user
		);

		$this->render("shop/index.tpl", $data);
	}

	public function addItem($id){
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
		$this->redirect('home');
	}

	public function cart(){
		$cart = $this->getCart();
		$sum = 0;
		if(!isset($this->user) || $this->user == null){
			$this->user = array("logged_in"=> false);
		}

		foreach ($cart as $item){
			$sum += ($item["item"]->price/100)*$item["amount"];
		}

		$data = array(
				"cart" => $cart,
				"count" => count($cart),
				"noCartItems" => count($cart),
				"sum" => $sum,
				"user" => $this->user
		);
		$this->render("shop/cart.tpl", $data);
	}

	public function clearCart(){
		$_SESSION["cart"] = array();
		$this->redirect('home');
	}

	public function checkout(){
		$cart = $this->getCart();
		$sum = 0;

		foreach ($cart as $item){
			$sum += ($item["item"]->price/100)*$item["amount"];
		}
		
		if(!isset($this->user) || $this->user == null){
			$this->user = array("logged_in"=> false);
		}
		
		$data = array(
				"cart" => $cart,
				"noCartItems" => count($cart),
				"sum" => $sum,
				"user" => $this->user
		);
		
		if(isset($this->user["id"])){
			$user = User::find($this->user["id"]);
			$data["userObj"] = $user;
			$this->render("shop/reviewOrder.tpl", $data);
		}else{
			$this->render("shop/checkout.tpl", $data);
		}
	}

	public function submitOrder(){
		$order = new Order();
		$user = User::find($this->user["id"]);
		$order->user = $user->id;
		// TODO: generate order and bill numbers
		$order->number = "asdf";
		$order->bill = "asdf";
		$order->save();
		$cart = $this->getCart();
		foreach($cart as $ci){
			$orderItem = new OrderItem(array(
					"item" => $ci["item"]->id,
					"order" => $order->id,
					"amount" => $ci["amount"],
					"size" => $ci["size"]
			)
			);
			$orderItem->save();
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
		$this->order($order->hashlink);
	}

	public function order($hash){
		$order = Order::findHash($hash);
		$cart = $this->getCart();
		
		if(!isset($this->user) || $this->user == null){
			$this->user = array("logged_in"=> false);
		}

		$data = array(
				"order" => $order,
				"noCartItems"=> count($cart),
				"user" => $this->user
		);

		$this->render("shop/order.tpl", $data);
	}

	public function noSignup(){
		// TODO add salt to generated password
		$u = array(
				"email" => $this->post("email"),
				"name" => $this->post("name"),
				"lastname" => $this->post("lastname"),
				"street" => $this->post("street"),
				"street_number" => $this->post("street_number"),
				"plz" => $this->post("plz"),
				"city" => $this->post("city"),
				"country" => $this->post("country"),
				"password" => md5($this->post("email").$this->post("plz")),
				"logged_in" => false
		);
		print_r($u);

		$user = new User($u);
		$user->save();
		$u["id"] = $user->id;
		$this->user = $u;
		$_SESSION["auth_user"] = $u;
		$this->checkout();
	}

	private function getCart(){
		$cart = array();
		if (isset($_SESSION["cart"])) {
			$cart = $_SESSION["cart"];
		}
		return $cart;
	}
}