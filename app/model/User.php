<?php

class User implements DBObject {

	public $id = null;
	public $email = null;
	public $username = null;
	public $password = null;
	public $address = null;
	public $admin = null;

	public function __construct() {
		$params = func_get_args();

		if (func_num_args() > 0) {
			$params = $params[0];
			if(array_key_exists("id", $params)){
				$this->id = $params["id"];
			}
			if(array_key_exists("email", $params)){
				$this->email = $params["email"];
			}
			if(array_key_exists("username", $params)){
				$this->username = $params["username"];
			}
			if(array_key_exists("password", $params)){
				$this->password = $params["password"];
			}
			if(array_key_exists("address", $params)){
				$this->address = $params["address"];
			}
			if(array_key_exists("admin", $params)){
				$this->admin = $params["admin"];
			}
		}
	}

	public static function find( $id )	{
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_user WHERE id = :id;", array( ":id" => $id ) );

		if( $resultSet != null ) {
			$user= new User($resultSet);
			return $user;
		}
		return null;
	}

	public function save() {
		$db = Database::get();

		if( $this->id == null ) {
			$result = $db->insert( "INSERT INTO tbl_user ( email, username, password, address) " .
					"VALUES ( :email, :username, :password, :address);",
					array( ":email" => 				$this->email,
							":username" => 			$this->username,
							":password" => 			$this->password,
							":address" => 			$this->address
					));

			$this->id = $db->lastInsertId();

			return $this->id;

		} else {
			$result = $db->update( "UPDATE tbl_user SET email=:email, username=:username, password=:password, admin=:admin WHERE id = :id;",
					array( ":email" => 				$this->email,
							":username" => 			$this->username,
							":password" => 			$this->password,
							":address" => 			$this->address,
							":admin" => 			$this->admin,
							":id" =>				$this->id ) );

			return $result;
		}
	}



	public function delete() {
		$db = Database::get();
		return $db->delete( "DELETE FROM tbl_user WHERE id = :id;", array( ":id" => $this->id ) );
	}

	public function getOrders() {
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_order WHERE user = :user;", array( ":user" => $this->id ) );

		$result = array();
		foreach($resultSet as $order){
			$order = new Order($order);
			array_push($result, $order);
		}
		return $result;
	}

	public static function getAll() {
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_user;" );

		$userArray = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $row ) {
				array_push( $userArray, new User($row));
			}
		}
		return $userArray;
	}
}