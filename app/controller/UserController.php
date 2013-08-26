<?php

class UserController extends Controller{
	
	public function delete($id){
		$this->checkAdmin();
		
		try{
			$user = User::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'User not found!');
			$this->redirect('admin');
		}

		try{
			$user->delete();
		}catch(ActiveRecord\ActiveRecordException $e){
			$this->app->flash('error', 'Could not delete user! ' . $this->errorOutput($e));
		}
	
		$this->redirect('admin');
	}
	
	public function edit($id){
		$this->checkAdmin();
		try{
			$userObject = User::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'User not found!');
			$this->redirect('admin');
		}
		
		$data = array(
				"userObject" => $userObject
		);
		$this->render("user/edit.html", $data);
	}
	
	// TODO check for correct password and change password.
	// TODO missing input validation
	// TODO missing error checks
	public function save($id){
		$this->checkAdmin();
		
		try{
			$userObject = User::find($id);
		}catch(ActiveRecord\RecordNotFound $e){
			$this->app->flash('error', 'User not found!');
			$this->redirect('admin');
		}
		
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