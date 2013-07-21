<?php

class UserController extends Controller{
	
	public function delete($id){
		$this->checkAdmin();
	
		$user = User::find($id);
		$user->delete();
	
		// TODO check for errors
		$this->redirect('admin');
	}
	
	public function edit($id){
		$this->checkAdmin();
		$userObject = User::find($id);
		$data = array(
				"user" => $this->user,
				"noCartItems" => count($this->getCart()),
				"userObject" => $userObject
		);
		$this->render("user/edit.tpl", $data);
	}
	
	// TODO check for correct password and change password.
	public function save($id){
		$this->checkAdmin();
	
		$userObject = User::find($id);
	
		$userObject->email = $this->post("email");
		$userObject->save();
	
		$address = $userObject->currentAddress();
		
		// if orders are associated with an adress, create a new adress
		if($address->orders){
			$address->current = false;
			$address->save();
			$address = new Address();
			$address->user_id = $userObject->id;
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