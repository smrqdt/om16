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
				$v = $this->validator($this->post());
				$v->rule('required', array('name', 'price'));
				$v->rule('numeric', array('price'));
				if($v->validate()){
					$item->name = $this->post("name");
					$item->description = $this->post("description");
					$item->price = $this->post("price") * 100;
					// TODO image upload for items
// 					$item->image = $this->post("image");
					$item->save();
				}else{
					$this->app->flashNow('error', $this->errorOutput($v->errors()));
				}
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
	
	public function delete($id){
		$item = Item::find($id);
		$item->delete();
		$this->redirect('adminitems');
	}
	
	public function addSize($id){
		$this->checkAdmin();
		$item = Item::find($id);
		if($item != null){
			if($this->app->request()->isPost()){
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