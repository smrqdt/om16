<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Item;
use Tapeshop\Models\Itemnumber;
use Tapeshop\Models\Orderitem;

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
			$item = Item::find_by_pk($id, array("conditions" => array("deleted = false")));
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
				$item->ticketcode = $this->post("ticketcode");

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
		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flashNow("error", gettext("error.item.notfound"));
			$this->app->redirect('adminitems');
		}

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
				$item->ticketcode = $this->post("ticketcode");

				if ($_FILES['image']['name'] != '') {
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
				} catch (ActiveRecordException $e) {
					$this->app->flash('error', 'Could not create Item! ' . $e->getMessage());
					$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript', 'ticketcode'));
				}

				$this->redirect('adminitems');
			} else {
				$this->app->flashNow('error', $this->errorOutput($v->errors()));
				$this->useDataFromRequest('itemform', array('name', 'description', 'price', 'shipping', 'ticketscript', 'ticketcode'));
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
			$this->app->flash('error', 'Item not found');
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

	public function numbersPdf($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Item not found');
			$this->redirect('home');
		}

		$valid = Itemnumber::find('all', array("conditions" => array("item_id = ? AND valid = true", $id), "order" => "number"));
		$invalid = Itemnumber::find('all', array("conditions" => array("item_id = ? AND valid = false", $id), "order" => "number"));
		$msg = "";
		foreach ($valid as $v) {
			$msg .= $v->number . "\n";
		}

		$pdf = new \Tapeshop\Controllers\Helpers\NumbersPdf('P', 'mm', 'A4');
		$pdf->SetTitle($item->name);
		$pdf->SetAuthor('');
		$pdf->PrintChapter(1, gettext("item.edit.numbers"), $msg);

		$msg = "";
		foreach ($invalid as $v) {
			$msg .= $v->number . "\n";
		}

		$pdf->PrintChapter(2, gettext("item.edit.invalidnumbers"), $msg);

		$pdf->Output();
		$this->app->response()->header("Content-Type", "application/pdf");
	}

    public function showTicketcodes($id){
        $this->checkAdmin();

        $item = null;

        try {
            $item = Item::find($id);
        } catch (RecordNotFound $e) {
            $this->app->flash('error', 'Item not found');
            $this->redirect('home');
        }

        $data = array(
            "item"=>$item
        );

        $this->render('admin/ticketcodes.tpl', $data);
    }

    public function invalidateTicketcode($orderitem_id){
        $this->checkAdmin();

        /**@var Orderitem $orderItem*/
        $orderItem = null;

        try {
            $orderItem = Orderitem::find($orderitem_id);
            $orderItem->ticketcode_valid = false;
            $orderItem->save()
;        } catch (RecordNotFound $e) {
            $this->app->flash('error', 'Orderitem not found');
            $this->redirect('home');
        }

        $this->redirect($this->app->urlFor('ticketcodes', array("id" => $orderItem->item_id)), false);
    }

    public function reactivateTicketcode($orderitem_id){
        $this->checkAdmin();

        /**@var Orderitem $orderItem*/
        $orderItem = null;

        try {
            $orderItem = Orderitem::find($orderitem_id);
            $orderItem->ticketcode_valid = true;
            $orderItem->save()
            ;        } catch (RecordNotFound $e) {
            $this->app->flash('error', 'Orderitem not found');
            $this->redirect('home');
        }

        $this->redirect($this->app->urlFor('ticketcodes', array("id" => $orderItem->item_id)), false);
    }
}