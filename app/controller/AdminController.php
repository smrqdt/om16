<?php

class AdminController extends Controller{

	public function index(){
		$this->checkAdmin();

		$orders = Order::all();
		$users = User::all();

		$data = array(
				"orders" => $orders,
				"users" => $users
		);

		$this->render("admin/users.html", $data);
	}
	
	public function items(){
		$this->checkAdmin();
		
		$items = Item::all();
		
		$data = array(
				"items" => $items
		);
		
		$this->render("admin/items.html", $data);
	}
	
	public function orders(){
		$this->checkAdmin();
		
		$orders = Order::all();
		
		$data = array(
				"orders" => $orders
		);
		
		$this->render("admin/orders.html", $data);
	}
}
