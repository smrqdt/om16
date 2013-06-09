<?php

class Size implements DBObject {

	public $id = null;
	public $item = null;
	public $size = null;
	
	public function __construct() {
		$params = func_get_args();

		if (func_num_args() > 0) {
			$params = $params[0];

			if(array_key_exists("id", $params)){
				$this->id = $params["id"];
			}

			if(array_key_exists("item", $params)){
				$this->item = $params["item"];
			}

			if(array_key_exists("size", $params)){
				$this->size = $params["size"];
			}
		}
	}

	public function save(){
		$db = Database::get();

		if( $this->id == null ) {
			$result = $db->insert( "INSERT INTO tbl_size (item, size) " .
					"VALUES ( :item, :size);",
					array( ":item" => $this->item,
							":size" => $this->size
					) );

			$this->id = $db->lastInsertId();

			return $this->id;

		} else {

			$result = $db->update( "UPDATE tbl_size SET item=:item, size=:size WHERE id = :id;",
					array( ":item" => $this->item,
							":size" => $this->size,
							":id" => $this->id ) );

			return $result;
		}
	}

	public function delete(){
		$db = Database::get();
		return $db->delete( "DELETE FROM tbl_size WHERE id = :id;", array( ":id" => $this->id ) );
	}

	public static function find($id){
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_size WHERE id = :id;", array( ":id" => $id ) );

		if( $resultSet != null ) {
			$size = new Size($resultSet);
			return $size;
		}
		return null;
	}

	public static function getAll(){
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_size;" );

		$sizeArray = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $row ) {
				array_push( $sizeArray, new Size($row));
			}
		}
		return $sizeArray;
	}
}