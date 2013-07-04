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
				"userObject" => $userObject
			);
		$this->render("admin/user/edit.tpl", $data);
	}

	// TODO check for correct password and change password.
	public function saveUser($id){
		$this->checkAdmin();

		// $userObject = new Object();

		$userObject = User::find($id);

		// $userObject->email = $this->post("email");
		// $userObject->name = $this->post("name");
		// $userObject->lastname = $this->post("lastname");
		// $userObject->street = $this->post("street");
		// $userObject->street_number = $this->post("street_number");
		// $userObject->plz = $this->post("plz");
		// $userObject->city = $this->post("city");
		// $userObject->country = $this->post("country");
		// $userObject->password = md5($this->post("email").$this->post("plz"));

		$userObject->save();

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
}
