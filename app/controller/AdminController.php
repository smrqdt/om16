<?php

class AdminController extends Controller{

	public function index(){
		$this->checkAdmin();

		$orders = Order::getAll();
		$cart = $this->getCart();
		$users = User::getAll();
		$items = Item::getAll();

		$data = array(
				"orders" => $orders,
				"users" => $users,
				"items" => $items,
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

	private function getCart(){
		$cart = array();
		if (isset($_SESSION["cart"])) {
			$cart = $_SESSION["cart"];
		}
		return $cart;
	}

	public function deleteOrder($id){
		$this->checkAdmin();

		$order = Order::find($id);
		$order->delete();

		// TODO check for errors
		$this->redirect('admin');
	}



	public function deleteUser($id){
		$this->checkAdmin();

		$user = User::find($id);
		$user->delete();

		// TODO check for errors
		$this->redirect('admin');
	}

	private function checkAdmin(){
		if(isset($this->user)){
			$user= User::find($this->user["id"]);
			if($user->admin){
				return;
			}
		}
		$this->redirect("home");
	}
}
