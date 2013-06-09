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
