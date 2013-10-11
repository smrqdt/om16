<?php
/**
 * Handle shop operations.
 */
class ShopController extends Controller {

	/**
	 * Show list of all items.
	 */
	public function index(){
		$items = Item::all(array("conditions" => array("deleted = false")));
		
		$data = array(
				"items" => $items
		);

		$this->render("shop/index.html", $data);
	}

	/**
	 * Check if a user is authenticated and redirect them to Input the shipping address, or review the order.
	 */
	public function checkout(){
		$cart = $this->getCart();
		$sum = 0;
		$shipping = 0;

		foreach ($cart as $item){
			$sum += ($item["item"]->price / 100) * $item["amount"];
			$shipping = max(array($shipping, $item["item"]->shipping));
		}
		$sum += ($shipping / 100);
		$data = array(
				"cart" => $cart,
				"shipping" => $shipping,
				"sum" => $sum
		);

		if(isset($this->user)){
			$data["user"] = $this->user;
			$this->render("shop/reviewOrder.html", $data);
		}else{
			$this->render("shop/checkout.html", $data);
		}
	}

	/**
	 * Handles the address input of a user and creates a new user on the fly.
	 */
	public function noSignup(){
		$v = $this->validator($this->post());
		$v->rule('required', array('email', 'name', 'street', 'city'));
		
		if($v->validate()){
			$c = User::connection();
			$c->transaction();
			try{
				$user = User::find_by_email($this->post('email'));
				if($user == null){
					$u = array(
							"email" => $this->post("email"),
							"password" => $this->auth->getProvider()->initPassword($this->gen_uuid())
					);
					
					$user = new User($u);
					$user->save();
				}else{
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
			}catch(ActiveRecord\ActiveRecordException $e){
				$c->rollback();
				$this->app->flashNow('error', 'An error occured! Please try again.' . $e->getMessage());
			}
		}else{
			$this->app->flash('error', $this->errorOutput($v->errors()));
			$this->useDataFromRequest('checkoutform', array('email', 'name', 'lastname', 'street', 'building_number', 'postcode', 'city', 'country'));
		}

		$this->redirect('checkout');
	}
	
	/**
	 * Show Ticketscript page.
	 */
	public function ticketscript(){
		$this->render('ticketscript.html');
	}
}