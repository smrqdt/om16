<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Item;
use Tapeshop\Models\Itemnumber;
use Tapeshop\Models\Size;

/**
 * Handle item operations.
 */
class ItemController extends Controller {

	/**
	 * Show item.
	 * @param int $id Id of the item
	 */
	public function show($id) {
		try {
			$item = Item::find($id, array("conditions" => array("deleted = false")));
			if ($item == null) {
				$this->app->flash('error', 'Item not found');
				$this->redirect('home');
			} else {
				$data = array(
					"item" => $item
				);
				$this->render('item/show.html', $data);
			}
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}
	}

	/**
	 * Edit an item.
	 * @param int $id
	 */
	public function edit($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found.');
			$this->redirect('home');
		}

		if ($this->app->request()->isPost()) {
			// update image
			if ($_FILES['image']['name'] != '') {
				// TODO improve path lookup
				$uploaddir = dirname(__FILE__) . '/../../../upload/';
				$uploadfile = $uploaddir . basename($_FILES['image']['name']);

				if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
					$item->image = APP_PATH . 'upload/' . basename($_FILES['image']['name']);
				} else {
					$this->app->flashNow('error', 'Something went wrong while uploading the image.');
				}
			}

			try {
				$item->save();
				$this->app->flashNow('success', 'Item saved.');
			} catch (ActiveRecordException $e) {
				$this->app->flash('error', 'Could not save Image: ' . $e->getMessage());
			}

			// validate form fields
			$v = $this->validator($this->post());
			$v->rule('required', array('name', 'price'));
			$v->rule('numeric', array('price', 'shipping'));
			if ($v->validate()) {
				// update item
				$item->name = $this->post("name");
				$item->description = $this->post("description");
				$item->price = $this->post("price") * 100;
				$item->shipping = $this->post("shipping") * 100;
				$item->ticketscript = $this->post("ticketscript");

				try {
					$item->save();
				} catch (ActiveRecordException $e) {
					$this->app->flashNow('error', 'Changes could not be saved! ' . $e->getMessage());
					$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shippping', 'ticketscript'));
				}
			} else {
				$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript'));
				$this->app->flashNow('error', $this->errorOutput($v->errors()));
			}
		}

		$data = array(
			"item" => $item
		);
		$this->render('item/edit.html', $data);
	}

	/**
	 * Delete an item.
	 * @param int $id Id of the item.
	 */
	public function delete($id) {
		//TODO catch RecordNotFound exception
		$item = Item::find($id);

		$item->deleted = true;

		try {
			$item->save();
			$this->app->flash('success', "Item deleted!");
		} catch (ActiveRecordException $e) {
			$this->app->flash('error', 'Deletion failed! ' . $e->getMessage());
		}

		$this->redirect('adminitems');
	}

	/**
	 * Add a size to an item.
	 * @param int $id
	 */
	public function addSize($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found.');
			$this->redirect('home');
		}

		if ($this->app->request()->isPost()) {
			try {
				$item->create_sizes(array(
					"size" => $this->post("size")
				));
			} catch (ActiveRecordException $e) {
				$this->app->flash('error', 'Could not add size.' . $e->getMessage());
			}
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $id)), false);
	}

	/**
	 * Delete a size for an item.
	 * @param Size $id
	 */
	public function deleteSize($id) {
		$this->checkAdmin();
		$itemid = 0;;

		try {
			$size = Size::find($id);
			$itemid = $size->item_id;
			$size->delete();
		} catch (ActiveRecordException $e) {
			$this->app->flashNow('error', 'Could not delete size! ' . $e->getMessage());
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $itemid)), false);
	}

	/**
	 * Create a new item.
	 */
	public function create() {
		$this->checkAdmin();

		if ($this->app->request()->isPost()) {
			$v = $this->validator($this->post());
			$v->rule('required', array('name', 'price'));
			$v->rule('numeric', array('price', 'shipping'));
			if ($v->validate()) {
				$item = new Item();
				$item->name = $this->post("name");
				$item->description = $this->post("description");
				$item->price = $this->post("price") * 100;
				$item->shipping = $this->post("shipping") * 100;
				$item->ticketscript = $this->post("ticketscript");

				if ($_FILES['image']['name'] != '') {
					$uploaddir = dirname(__FILE__) . '/../../upload/';
					$uploadfile = $uploaddir . basename($_FILES['image']['name']);

					if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
						$item->image = APP_PATH . 'upload/' . basename($_FILES['image']['name']);
					} else {
						$this->app->flashNow('error', 'Something went wrong while uploading the image.');
					}
				}

				try {
					$item->save();
				} catch (ActiveRecordException $e) {
					$this->app->flash('error', 'Could not create Item! ' . $e->getMessage());
					$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript'));
				}

				$this->redirect('adminitems');
			} else {
				$this->app->flashNow('error', $this->errorOutput($v->errors()));
				$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript'));
			}
		}

		$data = array(
			"item" => new Item()
		);
		$this->render('item/new.html', $data);
	}

	/**
	 * Remove the image of an Item
	 * @param Item $id
	 */
	public function removeImage($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flashNow('error', 'Item not found');
			$this->redirect('home');
		}

		$item->image = '';
		try {
			$item->save();
		} catch (ActiveRecordException $e) {
			$this->app->flash('error', 'Could not remove image! ' . $e->getMessage());
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $id)), false);
	}

	/**
	 * Add more item numbers to the shop.
	 * @param Item $id
	 */
	public function addNumbers($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}

		$param = $this->post('amount');

		// I think this is kind of a bad idea, because it fails on concurrent creates.
		// find max item number for this item

		$c = Item::connection();
		try {
			$c->transaction();
			$maxItemNumber = Itemnumber::find_by_sql('SELECT max(number) AS number FROM itemnumbers WHERE item_id=?', array($id));
			for ($i = 0; $i < $param; $i++) {
				$itemNumber = new Itemnumber();
				$itemNumber->item_id = $item->id;
				$itemNumber->number = $maxItemNumber[0]->number + $i + 1;
				$itemNumber->save();
			}
			$c->commit();
			$this->app->flashNow('success', "$param item numbers added.");
		} catch (ActiveRecordException $e) {
			$c->rollback();
			$this->app->flashNow('error', 'Could not add item numbers! ' . $e->getMessage());
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $id)), false);
	}

	/**
	 * Set item numbers to taken so they will not be given out be the shop.
	 * @param Item $id
	 */
	public function takeNumbers($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}

		$param = $this->post('numbers');

		$numbers = preg_split('/[^\d-]+/', $param);
		foreach ($numbers as $number) {
			if (preg_match('/[\d]+/', $number)) {
				$this->takeNumber($item, $number);
			} elseif (preg_match('/[\d]+-[\d]+/', $number)) {
				$range = preg_split('[\-]', $number);
				for ($i = min($range); $i < max($range) + 1; $i++) {
					$this->takeNumber($item, $i);
				}
			}
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $id)), false);
	}

	/**
	 * Helper function to process a taken number.
	 * @param Item $item
	 * @param int $number
	 */
	private function takeNumber($item, $number) {
		$itemNumber = Itemnumber::find(
			'first',
			array(
				'conditions' => array(
					'item_id' => $item->id,
					'number' => $number
				)
			)
		);

		if ($itemNumber != null) {
			if (!$itemNumber->free) {
				$this->app->flashNow('warn', "Item number $itemNumber->number was already taken!");
			} else {
				$itemNumber->free = false;
				try {
					$itemNumber->save();
				} catch (ActiveRecordException $e) {
					$this->app->flash('error', "Could not save changes to Number $number");
				}
			}
		} else {
			$this->app->flashNow('warn', "Number $number does not exist!");
		}
	}

	/**
	 * Set item numbers to invalid
	 * @param Item $id
	 */
	public function invalidateNumbers($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}

		$param = $this->post('numbers');
		$numbers = preg_split('/[^\d-]+/', $param);

		foreach ($numbers as $number) {
			if (preg_match('/[\d]+/', $number)) {
				$this->invalidateNumber($item, $number);
			} elseif (preg_match('/[\d]+-[\d]+/', $number)) {
				$range = preg_split('[\-]', $number);
				for ($i = min($range); $i < max($range) + 1; $i++) {
					$this->invalidateNumber($item, $i);
				}
			}
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $id)), false);
	}

	/**
	 * Helper function to invalidate a item number.
	 * @param Item $item
	 * @param int $number
	 */
	private function invalidateNumber($item, $number) {
		$itemNumber = Itemnumber::find(
			'first',
			array(
				'conditions' => array(
					'item_id' => $item->id,
					'number' => $number
				)
			)
		);

		if ($itemNumber != null) {
			if (!$itemNumber->valid) {
				$this->app->flashNow('warn', "Item number $itemNumber->number was already invalid!");
			} else {
				$itemNumber->valid = false;
				try {
					$itemNumber->save();
				} catch (ActiveRecordException $e) {
					$this->app->flash('error', "Could not save changes to Number $number");
				}
			}
		} else {
			$this->app->flashNow('warn', "Number $number does not exist!");
		}
	}

	/**
	 * Add item numbers to an item
	 * @param int $id Id of the item
	 */
	function makeNumbered($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}

		try {
			$item->numbered = true;
			$item->save();
			$this->app->flashNow('success', "Item saved.");
		} catch (ActiveRecordException $e) {
			$this->app->flashNow('error', 'Could not add item numbers! ' . $e->getMessage());
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $id)), false);
	}
}