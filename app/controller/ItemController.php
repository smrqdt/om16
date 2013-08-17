<?php

class ItemController extends Controller {

	public function show($id){
		try{
			$item = Item::find($id);
			$data = array(
					"item" => $item
			);
			$this->render('item/show.tpl', $data);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}
	}

	public function edit($id){
		$this->checkAdmin();
		
		try{
			$item = Item::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Item not found.');
			$this->redirect('home');
		}
		
		if($this->app->request()->isPost()){
			// update image
			if($_FILES['image']['name'] != '') {
				$uploaddir = dirname(__FILE__).'/../../upload/';
				$uploadfile = $uploaddir . basename($_FILES['image']['name']);
			
				if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
					$item->image = APP_PATH.'upload/'.basename($_FILES['image']['name']);
				} else {
					$this->app->flashNow('error', 'Something went wrong while uploading the image.');
				}
			}
			
			try{
				$item->save();
				$this->app->flashNow('success', 'Item saved.');
			}catch(ActiveRecord\ActiveRecordException $e){
				$this->app->flash('error', 'Could not save Image: ' . $this->errorOutput($e));
			}
			
			// validate form fields
			$v = $this->validator($this->post());
			$v->rule('required', array('name', 'price'));
			$v->rule('numeric', array('price', 'shipping'));
			if($v->validate()){
				// update item
				$item->name = $this->post("name");
				$item->description = $this->post("description");
				$item->price = $this->post("price") * 100;
				$item->shipping = $this->post("shipping") * 100;
				$item->ticketscript = $this->post("ticketscript");

				try{					
					$item->save();
				}catch(ActiveRecord\ActiveRecordException $e){
					$this->app->flashNow('error', 'Changes could not be saved! ' . $this->errorOutput($e));
					$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shippping', 'ticketscript'));
				}
			}else{
				$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript'));
				$this->app->flashNow('error', $this->errorOutput($v->errors()));
			}
		}

		$data = array(
				"item" => $item
		);
		$this->render('item/edit.tpl', $data);
	}

	public function delete($id){
		$item = Item::find($id);
		
		try{
			if($item->delete()){
				$this->app->flash('success', "Item deleted!");
			}
		}catch (ActiveRecord\ActiveRecordException $e){
			$this->app->flash('error', 'Deletion failed! '. $this->errorOutput($e));
		}

		$this->redirect('adminitems');
	}

	/**
	 * Add a size to an item.
	 * @param unknown $id
	 */
	public function addSize($id){
		$this->checkAdmin();
		try{
			$item = Item::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Item not found.');
			$this->redirect('home');
		}
		
		if($this->app->request()->isPost()){
			try{
				$item->create_sizes(array(
						"size" => $this->post("size")
				));
			}catch(ActiveRecord\ActiveRecordException $e){
				$this->flash('error', 'Could not add size.' . $this->errorOutput($e));
			}
		}

		$data = array(
				"item" => $item
		);
		$this->show($id);
	}

	/**
	 * Delete a size for an item.
	 * @param Item $id
	 */
	public function deleteSize($id){
		$this->checkAdmin();
		
		try{
			$size = Size::find($id);
			$itemid = $size->item_id;
			$size->delete();
		}catch(ActiveRecord\ActiveRecordException $e){
			$this->flashNow('error', 'Could not delete size! '. $this->errorOutput($e));
		}
		
		$this->show($itemid);
	}

	public function create(){
		$this->checkAdmin();

		if($this->app->request()->isPost()){
			$v = $this->validator($this->post());
			$v->rule('required', array('name', 'price'));
			$v->rule('numeric', array('price'));
			if($v->validate()){
				$item = new Item()	;
				$item->name = $this->post("name");
				$item->description = $this->post("description");
				$item->price = $this->post("price") * 100;
				$item->shipping = $this->post("shipping") * 100;
				$item->ticketscript = $this->post("ticketscript");
					
				if(isset($_FILES['image']) && $_FILES['image'] == '') {
					$uploaddir = dirname(__FILE__).'/../../upload/';
					$uploadfile = $uploaddir . basename($_FILES['image']['name']);

					if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
						$item->image = APP_PATH.'upload/'.basename($_FILES['image']['name']);
					} else {
						$this->app->flashNow('error', 'Something went wrong while uploading the image.');
					}
				}
				
				try{
					$item->save();
				}catch(ActiveRecord\ActiveRecordException $e){
					$this->flash('error', 'Could not create Item! '. $this->errorOutput($e));
					$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript'));
					$this->render('item/new.tpl', $data);
				}
				
				$this->redirect('adminitems');
			}else{
				$this->app->flashNow('error', $this->errorOutput($v->errors()));
				$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript'));
			}
		}

		$data = array(
				"item" => new Item()
		);
		$this->render('item/new.tpl', $data);
	}

	/**
	 * Remove the image of an Item
	 * @param Item $id
	 */
	public function removeImage($id){
		$this->checkAdmin();
		
		try{
			$item = Item::find($id);
		}catch (ActiveRecord\RecordNotFound $e){
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
		
		$item->image = '';
		try {
			$item->save();
		}catch (ActiveRecord\ActiveRecordException $e){
			$this->app->flash('error', 'Could not remove image! ' . $this->errorOutput($e));
		}

		$cart = $this->getCart();

		$data = array(
				"item" => $item
		);

		$this->show($id);
	}
	
	/**
	 * Add more item numbers to the shop.
	 * @param Item $id
	 */
	public function addNumbers($id){
		$this->checkAdmin();
		try{
			$item = Item::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}
		
		$param = $this->post('amount');
		
		// I think this is kind of a bad idea, because it fails on concurrent creates.
		// find max item number for this item
		
		$c = Item::connection();
		try{
			$c->transaction();
			$maxItemnumber = Itemnumber::find_by_sql("SELECT max(number) as number FROM itemnumbers WHERE item_id=?",array($id));
			for($i = 0; $i < $param; $i++){
				$itemNumber = new Itemnumber();
				$itemNumber->item_id = $item->id;
				$itemNumber->number = $maxItemnumber[0]->number + $i + 1 ;
				$itemNumber->save();
			}
			$c->commit();
			$this->app->flashNow('success', "$param item numbers added.");
		}catch(ActiveRecord\ActiveRecordException $e){
			$c->rollback();
			$this->app->flashNow('error', 'Could not add item numbers! ' . $this->errorOutput($e));		
		}
		
		$this->show($id);
	}
	
	/**
	 * Set item numbers to taken so they will not be given out be the shop.
	 * @param Item $id
	 */
	public function takeNumbers($id){
		$this->checkAdmin();
		
		try {
			$item = Item::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}
		
		$param = $this->post('numbers');

		$numbers = preg_split('/[^\d-]+/',$param);
		foreach($numbers as $number){
			if(preg_match('/[\d]+/', $number)){
				$this->takeNumber($item, $number);
			}elseif(preg_match('/[\d]+-[\d]+/', $number)){
				$range = preg_split('[\-]', $number);
				for ($i = min($range); $i < max($range) + 1; $i++){
					$this->takeNumber($item, $i);
				}
			}
		}
		
		$this->show($id);
	}
	
	/**
	 * Helper function to process a taken number.
	 * @param Item $item
	 * @param int $number
	 */
	private function takeNumber($item, $number){
		$itemNumber = Itemnumber::find(
				'first',
				array(
						'conditions' => array(
								'item_id'=> $item->id,
								'number' => $number
						)
				)
		);
		
		if($itemNumber != null){
			if(!$itemNumber->free){
				$this->app->flashNow('warn', "Item number $itemNumber->number was already taken!");
			}else{
				$itemNumber->free = false;
				try{
					$itemNumber->save();
				}catch(ActiveRecord\ActiveRecordException $e){
					$this->app->flash('error', "Could not save changes to Number $number");
				}
			}
		}else{
			$this->app->flashNow('warn', "Number $number does not exist!");
		}
	}
	
	/**
	 * Set item numbers to invalid
	 * @param Item $id
	 */
	public function invalidateNumbers($id){
		$this->checkAdmin();
		
		try{
			$item = Item::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}
		
		$param = $this->post('numbers');
		$numbers = preg_split('/[^\d-]+/',$param);

		foreach($numbers as $number){
			if(preg_match('/[\d]+/', $number)){
				$this->invalidateNumber($item, $number);
			}elseif(preg_match('/[\d]+-[\d]+/', $number)){
				$range = preg_split('[\-]', $number);
				for ($i = min($range); $i < max($range) + 1; $i++){
					$this->invalidateNumber($item, $i);
				}
			}
		}
		
		$this->show($id);
	}
	
	/**
	 * Helper function to invalidate a item number.
	 * @param Item $item
	 * @param int $number
	 */
	private function invalidateNumber($item, $number){
		$itemNumber = Itemnumber::find(
				'first',
				array(
						'conditions' => array(
								'item_id'=> $item->id,
								'number' => $number
						)
				)
		);
		
		if($itemNumber != null){
			if(!$itemNumber->valid){
				$this->app->flashNow('warn', "Item number $itemNumber->number was already invalid!");
			}else{
				$itemNumber->valid = false;
				try{
					$itemNumber->save();
				}catch(ActiveRecord\ActiveRecordException $e){
					$this->app->flash('error', "Could not save changes to Number $number");
				}
			}
		}else{
			$this->app->flashNow('warn', "Number $number does not exist!");
		}
	}
	
	function makeNumbered($id){
		$this->checkAdmin();
		try{
			$item = Item::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}
		
		try{
			$item->numbered = true;
			$item->save();
			$this->app->flashNow('success', "Item saved.");
		}catch(ActiveRecord\ActiveRecordException $e){
			$this->app->flashNow('error', 'Could not add item numbers! ' . $this->errorOutput($e));
		}
		
		$this->show($id);
	}
}