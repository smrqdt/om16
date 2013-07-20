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

		$data = array(
				"cart" => $cart,
				"noCartItems" => count($cart),
				"sum" => $sum
		);

		if(isset($this->user)){
			$data["user"] = $this->user;
			$this->render("shop/reviewOrder.tpl", $data);
		}else{
			$this->render("shop/checkout.tpl", $data);
		}
	}

	public function noSignup(){
		$u = array(
				"email" => $this->post("email"),
				"password" => $this->auth->getProvider()->initPassword($this->gen_uuid())
		);
		$user = new User($u);
		$user->save();
		$this->user = $user;
		
		$a = array(
				"user_id" => $user->id,
				"name" => $this->post("name"),
				"lastname" => $this->post("lastname"),
				"street" => $this->post("street"),
				"building_number" => $this->post("street_number"),
				"postcode" => $this->post("plz"),
				"city" => $this->post("city"),
				"country" => $this->post("country"),
		);

		$address = new Address($a);
		$address->save();
		
		// create session user object
		$u["id"] = $user->id;
		$u["logged_in"] = false;
		$_SESSION["auth_user"] = $u;
		$this->checkout();
	}
}