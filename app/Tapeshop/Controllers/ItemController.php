<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Item;
use Tapeshop\Models\Itemnumber;

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
				$this->app->flash('error', 'Artikel nicht gefunden!');
				$this->redirect('home');
			} else {
				$data = array(
					"item" => $item
				);
				$this->render('item/show.html', $data);
			}
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Artikel nicht gefunden!');
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
			$this->app->flash('error', 'Artikel nicht gefunden!');
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
					$this->app->flashNow('error', 'Upload fehlgeschlagen. Bitte prüfe ob das Verzeichnis  "upload" existiert und Schreibrechte dafür vorhanden sind.');
				}
			}

			try {
				$item->save();
				$this->app->flashNow('success', 'Artikel gespeichert.');
			} catch (ActiveRecordException $e) {
				$this->app->flash('error', 'Konnte das bild nicht speichern: ' . $e->getMessage());
			}

			// validate form fields
			$v = $this->validator($this->post());
			$v->rule('required', array('name', 'price'))->message("Du musst einen Preis angeben");
			$v->rule('numeric', array('price', 'shipping'))->message("Bitte gib eine Zahle ein.");
			if ($v->validate()) {
				// update item
				$item->name = $this->post("name");
				$item->description = $this->post("description");
				$item->price = $this->post("price") * 100;
				$item->shipping = $this->post("shipping") * 100;
				$item->ticketscript = $this->post("ticketscript");
				$item->ticketcode = $this->post("ticketcode");
				$item->support_ticket = $this->post("support_ticket");
				$item->sort_order = $this->post("sort_order");

				try {
					$item->save();
				} catch (ActiveRecordException $e) {
					$this->app->flashNow('error', 'Änderungen konnten nicht gespeichert werden! ' . $e->getMessage());
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
			$this->app->flash('success', "Artikel gelöscht!");
		} catch (ActiveRecordException $e) {
			$this->app->flash('error', 'Löschen fehlgeschlagen! ' . $e->getMessage());
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
				$item->support_ticket = $this->post("support_ticket") || false;

				if ($_FILES['image']['name'] != '') {
					$uploaddir = dirname(__FILE__) . '/../../../upload/';
					$uploadfile = $uploaddir . basename($_FILES['image']['name']);

					if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
						$item->image = APP_PATH . 'upload/' . basename($_FILES['image']['name']);
					} else {
						$this->app->flashNow('error', 'Upload fehlgeschlagen. Bitte prüfe ob das Verzeichnis  "upload" existiert und Schreibrechte dafür vorhanden sind.');
					}
				}

				try {
					$item->save();
				} catch (ActiveRecordException $e) {
					$this->app->flash('error', 'Konnte Artikel nicht anlegen! ' . $e->getMessage());
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
			$this->app->flash('error', 'Artikel nicht gefunden!');
			$this->redirect('home');
		}

		$item->image = '';
		try {
			$item->save();
		} catch (ActiveRecordException $e) {
			$this->app->flash('error', 'Konnte Bild nicht entfernen! ' . $e->getMessage());
		}

		$this->redirect($this->app->urlFor('editItem', array('id' => $id)), false);
	}

	public function numbersPdf($id) {
		$this->checkAdmin();

		$item = null;

		try {
			$item = Item::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'Artikel nicht gefunden!');
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
}
