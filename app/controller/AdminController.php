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
