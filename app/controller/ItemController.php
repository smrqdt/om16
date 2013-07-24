<?php

class ItemController extends Controller {

	public function show($id){
		$item = Item::find($id);
		if($item != null){
			$data = array(
					"item" => $item
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
				// update image
				if($_FILES['image'] != '') {
					$uploaddir = dirname(__FILE__).'/../../upload/';
					print "<br> UPLOADDIR: ".$uploaddir;
					$uploadfile = $uploaddir . basename($_FILES['image']['name']);
				
					if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
						$item->image = APP_PATH.'upload/'.basename($_FILES['image']['name']);
					} else {
						$this->app->flashNow('error', 'Something went wrong while uploading the image.');
					}
				}
				$item->save();
				
				// validate form fields
				$v = $this->validator($this->post());
				$v->rule('required', array('name', 'price'));
				$v->rule('numeric', array('price'));
				if($v->validate()){
					// update item
					$item->name = $this->post("name");
					$item->description = $this->post("description");
					$item->price = $this->post("price") * 100;
						
					$item->save();
				}else{
					$this->useDataFromRequest('itemform', array('name', 'description', 'price'));
					$this->app->flashNow('error', $this->errorOutput($v->errors()));
				}
			}

			$data = array(
					"item" => $item
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

	/**
	 * Add a size to an item.
	 * @param unknown $id
	 */
	public function addSize($id){
		$this->checkAdmin();
		$item = Item::find($id);
		if($item != null){
			if($this->app->request()->isPost()){
				$item->create_sizes(array(
						"size" => $this->post("size")
				));
			}

			$data = array(
					"item" => $item
			);
			$this->show($id);
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}

	/**
	 * Delete a size for an item.
	 * @param Item $id
	 */
	public function deleteSize($id){
		$this->checkAdmin();

		// TODO catch errors
		$size = Size::find($id);
		$itemid = $size->item_id;
		$size->delete();
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
					
				if(isset($_FILES['image']) && $_FILES['image'] == '') {
					$uploaddir = dirname(__FILE__).'/../../upload/';
					$uploadfile = $uploaddir . basename($_FILES['image']['name']);

					if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
						$item->image = APP_PATH.'upload/'.basename($_FILES['image']['name']);
					} else {
						$this->app->flashNow('error', 'Something went wrong while uploading the image.');
					}
				}
					
				$item->save();
				$this->redirect('adminitems');
			}else{
				$this->app->flashNow('error', $this->errorOutput($v->errors()));
				$this->useDataFromRequest('itemform', array('name', 'description', 'price'));
			}
		}
			
		$cart = $this->getCart();

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
		$item = Item::find($id);
		if($item != null){
			$item->image = '';
			$item->save();

			$cart = $this->getCart();

			$data = array(
					"item" => $item
			);
			// TODO remove image from file system
			$this->show($id);
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}
	
	/**
	 * A more item numbers to the shop.
	 * @param Item $id
	 */
	public function addNumbers($id){
		$this->checkAdmin();
		$item = Item::find($id);
		$param = $this->post('amount');
		if($item != null){
			// FIXME I think this is kind of a bad idea, because it fails on concurrent creates.
			// find max item number for this item
			$maxItemnumber = Itemnumber::find_by_sql("SELECT max(number) as number FROM itemnumbers WHERE item_id=?",array($id));

			for($i = 0; $i < $param; $i++){
				$itemNumber = new Itemnumber();
				$itemNumber->item_id = $item->id;
				$itemNumber->number = $maxItemnumber[0]->number + $i + 1 ;
				$itemNumber->save();
			}
			$this->app->flashNow('success', "$param item numbers added.");
			$this->show($id);
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}
	
	/**
	 * Set item numbers to taken so they will not be given out be the shop.
	 * @param Item $id
	 */
	public function takeNumbers($id){
		$this->checkAdmin();
		$item = Item::find($id);
		$param = $this->post('numbers');
		if($item != null){
			$count = 0;
			$numbers = preg_split('/[^\d-]+/',$param);
			foreach($numbers as $number){
				if(preg_match('/[\d]+/', $number)){
					$itemNumber = Itemnumber::find(
							'first',
							array(
									'conditions' => array(
											'item_id'=> $item->id,
											'number' => $number
									)
							)
					);
					// TODO check and warn if a number is already taken
					$itemNumber->free = false;
					$itemNumber->save();
					$count++;
				}elseif(preg_match('/[\d]+-[\d]+/', $number)){
					$range = preg_split('[\-]', $number);
					for ($i = min($range); $i < max($range) + 1; $i++){
						$itemNumber = Itemnumber::find(
								'first',
								array(
										'conditions' => array(
												'item_id'=> $item->id,
												'number' => $i
										)
								)
						);
						$itemNumber->free = false;
						$itemNumber->save();
						$count++;
					}
				}
			}
			$this->show($id);
			$this->app->flashNow('success', "$count item numbers marked as taken.");
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}
	
	/**
	 * Set item numbers to invalid
	 * @param Item $id
	 */
	public function invalidateNumbers($id){
		$this->checkAdmin();
		$item = Item::find($id);
		$param = $this->post('numbers');
		if($item != null){
			$count = 0;
			$numbers = preg_split('/[^\d-]+/',$param);

			foreach($numbers as $number){
				if(preg_match('/[\d]+/', $number)){
					$itemNumber = Itemnumber::find(
							'first',
							array(
									'conditions' => array(
											'item_id'=> $item->id,
											'number' => $number
									)
							)
					);
					$itemNumber->valid = false;
					$itemNumber->save();
					$count++;
				}elseif(preg_match('/[\d]+-[\d]+/', $number)){
					$range = preg_split('[\-]', $number);
					for ($i = min($range); $i < max($range) + 1; $i++){
						$itemNumber = Itemnumber::find(
								'first',
								array(
										'conditions' => array(
												'item_id'=> $item->id,
												'number' => $i
										)
								)
						);
						$itemNumber->valid = false;
						$itemNumber->save();
						$count++;
					}
				}
			}
			$this->app->flashNow('success', "$count item numbers marked as invalid");
			$this->show($id);
		}else{
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}
	}
}