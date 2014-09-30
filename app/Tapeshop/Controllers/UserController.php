<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Address;
use Tapeshop\Models\User;

class UserController extends Controller {

	public function delete($id) {
		$this->checkAdmin();

		/** @var $user \Tapeshop\Models\User */
		$user = null;

		try {
			$user = User::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'User not found!');
			$this->redirect('admin');
		}

		try {
			$user->delete();
		} catch (ActiveRecordException $e) {
			$this->app->flash('error', 'Could not delete user! ' . $e->getMessage());
		}

		$this->redirect('admin');
	}

	public function edit($id) {
		$this->checkAdmin();

		/** @var $user \Tapeshop\Models\User */
		$user = null;

		try {
			$user = User::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'User not found!');
			$this->redirect('admin');
		}

		$data = array(
			"userObject" => $user
		);
		$this->render("user/edit.html", $data);
	}

	// TODO check for correct password and change password.
	// TODO missing input validation
	// TODO missing error checks
	public function save($id) {
		$this->checkAdmin();

		/**  @var $user \Tapeshop\Models\User */
		$user = null;

		try {
			$user = User::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', 'User not found!');
			$this->redirect('admin');
		}

		$user->email = $this->post("email");
		$user->save();

		/** @var $address \Tapeshop\Models\Address */
		$address = $user->currentAddress();

		// if orders are associated with an adress, create a new adress
		if ($address->orders) {
			$address->current = false;
			$address->save();
			$address = new Address();
			$address->user_id = $user->id;
		}
		$address->name = $this->post("name");
		$address->lastname = $this->post("lastname");
		$address->street = $this->post("street");
		$address->building_number = $this->post("street_number");
		$address->postcode = $this->post("plz");
		$address->city = $this->post("city");
		$address->country = $this->post("country");
		$address->save();

		$this->redirect('admin');
	}
}