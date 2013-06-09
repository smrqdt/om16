<?php

class Item implements DBObject {

	public $id = null;
	public $name = null;
	public $description = null;
	public $price = null;
	public $image = null;

	public function __construct() {
		$params = func_get_args();

		if (func_num_args() > 0) {
			$params = $params[0];

			if(array_key_exists("id", $params)){
				$this->id = $params["id"];
			}

			if(array_key_exists("name", $params)){
				$this->name = $params["name"];
			}

			if(array_key_exists("description", $params)){
				$this->description = $params["description"];
			}

			if(array_key_exists("price", $params)){
				$this->price = floatval($params["price"]) / 100;
			}

			if(array_key_exists("image", $params)){
				$this->image = $params["image"];
			}
		}
	}

	public function getSizes(){
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_size WHERE item = :item;", array( ":item" => $this->id ) );

		$result = array();

		foreach($resultSet as $s){
			$size = new Size($s);
			array_push($result, $size);
		}
		return $result;
	}

	public function save(){
		$db = Database::get();

		if( $this->id == null ) {
			$result = $db->insert( "INSERT INTO tbl_item ( name, description, price, image) " .
					"VALUES ( :name, :description, :price, :image);",
					array( ":name" => $this->name,
							":description" => $this->description,
							":price" => intval($this->price * 100),
							":image" => $this->image
					) );

			$this->id = $db->lastInsertId();

			return $this->id;

		} else {

			$result = $db->update( "UPDATE tbl_item SET name=:name, description=:description, price=:price WHERE id = :id;",
					array( ":name" => $this->name,
							":description" => $this->description,
							":price" => $this->price,
							":image" => $this->image,
							":id" => $this->id ) );

			return $result;
		}
	}

	public function delete(){
		$db = Database::get();
		return $db->delete( "DELETE FROM tbl_item WHERE id = :id;", array( ":id" => $this->id ) );
	}

	public static function find($id){
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_item WHERE id = :id;", array( ":id" => $id ) );

		if( $resultSet != null ) {
			$item= new Item();
			$item->id = $resultSet["id"];
			$item->name = $resultSet["name"];
			$item->description = $resultSet["description"];
			$item->price = $resultSet["price"];
			$item->image = $resultSet["image"];

			return $item;
		}
		return null;
	}

	public static function getAll(){
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_item;" );

		$itemArray = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $row ) {
				array_push( $itemArray, new Item($row));
			}
		}
		return $itemArray;
	}
}