<?php

class ShopController extends Controller {

	public function index(){
			
		$items = Item::all();
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

		// create session user object
		$user = new User($u);
		$user->save();
		$u["id"] = $user->id;
		$this->user = $u;
		$_SESSION["auth_user"] = $u;
		$this->checkout();
	}
}