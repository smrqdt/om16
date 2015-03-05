<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use Tapeshop\Controller;
use Tapeshop\Models\Address;
use Tapeshop\Models\Item;
use Tapeshop\Models\User;

/**
 * Handle shop operations.
 */
class ShopController extends Controller {

	/**
	 * Show list of all items.
	 */
	public function index() {
		$items = Item::all(array("conditions" => array("deleted = false"), "order" => "name asc"));

		$data = array(
			"items" => $items
		);

		$this->render("shop/index.html", $data);
	}

    public function shop(){
        $this->render("shop/shop.tpl");
    }

	/**
	 * Check if a user is authenticated and redirect them to Input the shipping address, or review the order.
	 */
	public function checkout() {
		$cart = $this->getCart();
		$sum = 0;
		$shipping = 0;

		foreach ($cart as $item) {
			$sum += ($item["item"]->price / 100) * $item["amount"];
			$shipping = max(array($shipping, $item["item"]->shipping));
		}
		$sum += ($shipping / 100);
		$data = array(
			"cart" => $cart,
			"shipping" => $shipping,
			"sum" => $sum
		);

		if (isset($this->user)) {
			$data["user"] = $this->user;
			$this->render("shop/reviewOrder.html", $data);
		} else {
			$this->render("shop/checkout.html", $data);
		}
	}

	/**
	 * Handles the address input of a user and creates a new user on the fly.
	 */
	public function noSignup() {
		$v = $this->validator($this->post());
		$v->rule('required', array('email', 'name', 'street', 'city'));

		if ($v->validate()) {
			$c = User::connection();
			$c->transaction();
			try {
				/** @var $user \Tapeshop\Models\User */
				$user = User::find_by_email($this->post('email'));
				if ($user == null) {
					$u = array(
						"email" => $this->post("email"),
						"password" => $this->auth->getProvider()->initPassword($this->gen_uuid())
					);

					$user = new User($u);
					$user->save();
				} else {
					$oldAddress = $user->currentAddress();
					$oldAddress->current = false;
					$oldAddress->save();
				}
				$this->user = $user;

				$a = array(
					"user_id" => $user->id,
					"name" => $this->post("name"),
					"lastname" => $this->post("lastname"),
					"street" => $this->post("street"),
					"building_number" => $this->post("building_number"),
					"postcode" => $this->post("postcode"),
					"city" => $this->post("city"),
					"country" => $this->post("country")
				);

				$address = new Address($a);
				$address->save();

				// create session user object
				$u["id"] = $user->id;
				$u["logged_in"] = false;
				$_SESSION["auth_user"] = $u;
				$c->commit();
			} catch (ActiveRecordException $e) {
				$c->rollback();
				$this->app->flashNow('error', 'An error occured! Please try again.' . $e->getMessage());
			}
		} else {
			$this->app->flash('error', $this->errorOutput($v->errors()));
			$this->useDataFromRequest('checkoutform', array('email', 'name', 'lastname', 'street', 'building_number', 'postcode', 'city', 'country'));
		}

		$this->redirect('checkout');
	}

	public function changeAddress() {
		/** @var User $user */
		$user = $this->user;
		/** @var Address $address */
		$address = $user->currentAddress();
		$formData = array(
			"email" => $user->email,
			"name" => $address->name,
			"lastname" => $address->lastname,
			"street" => $address->street,
			"building_number" => $address->building_number,
			"postcode" => $address->postcode,
			"city" => $address->city,
			"country" => $address->country
		);

		$this->auth->logout(false);

		$this->render("shop/checkout.html", array("checkoutform" => $formData));
	}

	/**
	 * Show Ticketscript page.
	 */
	public function ticketscript() {
		$this->render('ticketscript.html');
	}
}