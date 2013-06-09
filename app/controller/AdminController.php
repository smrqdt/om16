<?php

class AdminController extends Controller{

	public function index(){
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
		$order = Order::find($id);
		$order->delete();
		
		// TODO check for errors
		$this->redirect('admin');
	}
	
	public function deleteUser($id){
		$user = User::find($id);
		$user->delete();
	
		// TODO check for errors
		$this->redirect('admin');
	}
}
