<?php

class ItemController extends Controller {

	public function show($id){
		$item = Item::find($id);
		if($item != null){
			$cart = $this->getCart();
		
			$data = array(
					"user" => $this->user,
					"item" => $item,
					"noCartItems"=> count($cart)
			);
			$this->render('item/show.tpl', $data);
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}

	public function edit($id){
		$this->checkAdmin();
		$item = Item::find($id);
		if($item != null){
			if($this->app->request()->isPost()){
				//submit changes
			}
			
			$cart = $this->getCart();

			$data = array(
					"user" => $this->user,
					"item" => $item,
					"noCartItems"=> count($cart)
			);
			$this->render('item/edit.tpl', $data);
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}
	
	public function addSize($id){
		$this->checkAdmin();
		$item = Item::find($id);
		if($item != null){
			if($this->app->request()->isPost()){
				//submit changes
				$item->create_sizes(array(
						"size" => $this->post("size")
				));
			}
				
			$cart = $this->getCart();
		
			$data = array(
					"user" => $this->user,
					"item" => $item,
					"noCartItems"=> count($cart)
			);
			$this->show($id);
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}
	
	public function deleteSize($id){
		$this->checkAdmin();
		
		// TODO catch errors
		$size = Size::find($id);
		$itemid = $size->item_id;
		$size->delete();
		$this->show($itemid);
	}
}