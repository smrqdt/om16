<?php

class AdminController extends Controller{

	public function index(){
		$this->checkAdmin();

		$orders = Order::all();
		$cart = $this->getCart();
		$users = User::all();

		$data = array(
				"orders" => $orders,
				"users" => $users,
				"noCartItems"=> count($cart),
				"user" => $this->user
		);

		$this->render("admin/index.tpl", $data);
	}

	public function editUser($id){
		$this->checkAdmin();
		$userObject = User::find($id);
		$data = array(
				"user" => $this->user,
				"noCartItems" => count($this->getCart()),
				"userObject" => $userObject
			);
		$this->render("user/edit.tpl", $data);
	}

	// TODO check for correct password and change password.
	public function saveUser($id){
		$this->checkAdmin();

		// $userObject = new Object();

		$userObject = User::find($id);

		$userObject->email = $this->post("email");
		$userObject->save();
		
		$address = $userObject->currentAddress();
		if($address->orders){
			$address->current = false;
			$address->save();
			$address = new Address();
			$address->user_id = $userObject->id;
		}
		$address->name = $this->post("name");
		$address->lastname = $this->post("lastname");
		$address->street = $this->post("street");
		$address->building_number = $this->post("street_number");
		$address->postcode = $this->post("plz");
		$address->city = $this->post("city");
		$address->country = $this->post("country");
		$address->save();

		$this->redirect('admin');

	}
	
	public function items(){
		$this->checkAdmin();
		
		$cart = $this->getCart();
		$items = Item::all();
		
		$data = array(
				"items" => $items,
				"noCartItems"=> count($cart),
				"user" => $this->user
		);
		
		$this->render("admin/items.tpl", $data);
		
	}
	
	public function orders(){
		$this->checkAdmin();
		
		$cart = $this->getCart();
		
		$orders = Order::all();
		
		$data = array(
				"orders" => $orders,
				"noCartItems" => count($cart),
				"user" => $this->user
		);
		
		$this->render("admin/orders.tpl", $data);
	}
}
