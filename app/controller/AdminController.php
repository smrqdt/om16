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

		$this->render("admin/index.tpl", $data);
	}
	
	public function items(){
		$this->checkAdmin();
		
		$items = Item::all();
		
		$data = array(
				"items" => $items
		);
		
		$this->render("admin/items.tpl", $data);
		
	}
	
	public function orders(){
		$this->checkAdmin();
		
		$orders = Order::all();
		
		$data = array(
				"orders" => $orders
		);
		
		$this->render("admin/orders.tpl", $data);
	}
}
