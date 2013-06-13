<?php

class AdminController extends Controller{

	public function index(){
		$this->checkAdmin();

		$orders = Order::all();
		$cart = $this->getCart();
		$users = User::all();
		$items = Item::all();

		$data = array(
				"orders" => $orders,
				"users" => $users,
				"items" => $items,
				"noCartItems"=> count($cart),
				"user" => $this->user
		);

		$this->render("admin/index.tpl", $data);
	}
}
