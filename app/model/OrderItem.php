<?php

class OrderItem implements DBObject {

	public $id = null;
	public $item = null;
	public $order = null;
	public $amount = null;
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

			if(array_key_exists("order", $params)){
				$this->order = $params["order"];
			}

			if(array_key_exists("amount", $params)){
				$this->amount = $params["amount"];
			}

			if(array_key_exists("size", $params)){
				$this->size = $params["size"];
			}
		}
	}

	public function save(){
		$db = Database::get();

		if( $this->id == null ) {
			$result = $db->insert( "INSERT INTO tbl_order_items (item, orderId, amount, size) " .
					"VALUES ( :item, :order, :amount, :size);",
					array( ":item" => $this->item,
							":order" => $this->order,
							":amount" => $this->amount,
							":size" => $this->size
					) );

			$this->id = $db->lastInsertId();

			return $this->id;

		} else {

			$result = $db->update( "UPDATE tbl_order_items SET item=:item, order=:order, amount=:amount, size=:size WHERE id = :id;",
					array( ":item" => $this->item,
							":order" => $this->order,
							":amount" => $this->amount,
							":size" => $this->size,
							":id" => $this->id ) );

			return $result;
		}
	}

	public function delete(){
		$db = Database::get();
		return $db->delete( "DELETE FROM tbl_order_items WHERE id = :id;", array( ":id" => $this->id ) );
	}

	public static function find($id){
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_order_items WHERE id = :id;", array( ":id" => $id ) );

		if( $resultSet != null ) {
			$order = new OrderItem($resultSet);
			return $order;
		}
		return null;
	}

	public static function getAll(){
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_order_items;" );

		$orderItemsArray = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $row ) {
				array_push( $orderItemsArray, new order($row));
			}
		}
		return $orderItemsArray;
	}

	public function getItem(){
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_item WHERE id = :id;", array( ":id" => $this->item ) );

		if( $resultSet != null ) {
			$order = new Item($resultSet);
			return $order;
		}
		return null;
	}

	public function getOrder(){
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_order WHERE id = :id;", array( ":id" => $this->order ) );

		if( $resultSet != null ) {
			$order = new Order($resultSet);
			return $order;
		}
		return null;
	}
}