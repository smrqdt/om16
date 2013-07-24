<?php

class ShopController extends Controller {

	public function index(){
		$items = Item::all();

		$data = array(
				"items" => $items
		);

		$this->render("shop/index.tpl", $data);
	}

	public function checkout(){
		$cart = $this->getCart();
		$sum = 0;

		foreach ($cart as $item){
			$sum += ($item["item"]->price / 100) * $item["amount"];
		}

		$data = array(
				"cart" => $cart,
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
		$v = $this->validator($this->post());
		$v->rule('required', array('email', 'name', 'street', 'city'));
		
		if($v->validate()){
			$c = User::connection();
			$c->transaction();
			try{
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
						"building_number" => $this->post("building_number"),
						"postcode" => $this->post("postcode"),
						"city" => $this->post("city"),
						"country" => $this->post("country"),
				);
		
				$address = new Address($a);
				$address->save();
				
				// create session user object
				$u["id"] = $user->id;
				$u["logged_in"] = false;
				$_SESSION["auth_user"] = $u;
				$c->commit();
			}catch(ActiveRecord\ActiveRecordException $e){
				$c->rollback();
				$this->app->flashNow('error', 'An error occured! Please try again.' . $this->errorOutput($e));
			}
		}else{
			$this->app->flashNow('error', $this->errorOutput($v->errors()));
			$this->useDataFromRequest('checkoutform', array('email', 'name', 'lastname', 'street', 'building_number', 'postcode', 'city', 'country'));
		}

		$this->checkout();
	}
}