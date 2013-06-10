<?php

class User implements DBObject {

	public $id = null;
	public $email = null;
	public $username = null;
	public $password = null;
	public $name = null;
	public $lastname = null;
	public $street = null;
	public $street_number = null;
	public $plz = null;
	public $city = null;
	public $country = null;
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
			if(array_key_exists("name", $params)){
				$this->name = $params["name"];
			}
			if(array_key_exists("lastname", $params)){
				$this->lastname = $params["lastname"];
			}
			if(array_key_exists("street", $params)){
				$this->street = $params["street"];
			}
			if(array_key_exists("street_number", $params)){
				$this->street_number = $params["street_number"];
			}
			if(array_key_exists("plz", $params)){
				$this->plz = $params["plz"];
			}
			if(array_key_exists("city", $params)){
				$this->city = $params["city"];
			}
			if(array_key_exists("country", $params)){
				$this->country = $params["country"];
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
			$result = $db->insert( "INSERT INTO tbl_user ( email, username, password, name, lastname, street, street_number, plz, city, country) " .
					"VALUES ( :email, :username, :password, :name, :lastname, :street, :street_number, :plz, :city, :country);",
					array( ":email" => 				$this->email,
							":username" => 			$this->username,
							":password" => 			$this->password,
							":name" => 				$this->name,
							":lastname" => 			$this->lastname,
							":street" => 			$this->street,
							":street_number" => 	$this->street_number,
							":plz" => 				$this->plz,
							":city" => 				$this->city,
							":country" => 			$this->country
					));

			$this->id = $db->lastInsertId();

			return $this->id;

		} else {
			$result = $db->update( "UPDATE tbl_user SET email=:email, username=:username, password=:password, admin=:admin WHERE id = :id;",
					array( ":email" => 				$this->email,
							":username" => 			$this->username,
							":password" => 			$this->password,
							":name" => 				$this->name,
							":lastname" => 			$this->lastname,
							":street" => 			$this->street,
							":street_number" => 	$this->street_number,
							":plz" => 				$this->plz,
							":city" => 				$this->city,
							":country" => 			$this->country,
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