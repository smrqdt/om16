<?php

class UserController extends Controller{
	public function deleteUser($id){
		$this->checkAdmin();
	
		$user = User::find($id);
		$user->delete();
	
		// TODO check for errors
		$this->redirect('admin');
	}
}