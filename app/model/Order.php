<?php

class Order implements DBObject {

	public $id = null;
	public $number = null;
	public $bill = null;
	public $user = null;
	public $ordertime = null;
	public $paymenttime = null;
	public $shippingtime = null;
	public $status = null;
	public $hashlink = null;

	public function __construct() {
		$params = func_get_args();

		if (func_num_args() > 0) {
			$params = $params[0];

			if(array_key_exists("id", $params)){
				$this->id = $params["id"];
			}

			if(array_key_exists("number", $params)){
				$this->number = $params["number"];
			}

			if(array_key_exists("bill", $params)){
				$this->bill = $params["bill"];
			}

			if(array_key_exists("user", $params)){
				$this->user = floatval($params["user"]);
			}

			if(array_key_exists("ordertime", $params)){
				$this->ordertime = $params["ordertime"];
			}

			if(array_key_exists("paymenttime", $params)){
				$this->paymenttime = $params["paymenttime"];
			}

			if(array_key_exists("shippingtime", $params)){
				$this->shippingtime = $params["shippingtime"];
			}

			if(array_key_exists("status", $params)){
				$this->status = $params["status"];
			}

			if(array_key_exists("hashlink", $params)){
				$this->hashlink = $params["hashlink"];
			}
		}
	}

	public function getItems(){
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_order_items WHERE orderId = :order;", array( ":order" => $this->id ) );

		$result = array();

		foreach($resultSet as $s){
			$orderItem= new OrderItem($s);
			array_push($result, $orderItem);
		}
		return $result;
	}

	public function getUser(){
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_user WHERE id = :id;", array( ":id" => $this->user ) );

		if( $resultSet != null ) {
			$user = new User($resultSet);
			return $user;
		}
		return null;
	}

	public function save(){
		$db = Database::get();

		if( $this->id == null ) {
			$date = new DateTime();
			$otime = date('Y-m-d H:i:s',$date->getTimestamp());
			$link = $this->gen_uuid();
			$result = $db->insert(	"INSERT INTO tbl_order ( number, bill, user, ordertime, paymenttime, shippingtime, status, hashlink) " .
									"VALUES ( :number, :bill, :user, :ordertime, :paymenttime, :shippingtime, :status, :hashlink);",
									array( ":number" => $this->number,
										":bill" => $this->bill,
										":user" => $this->user,
										":ordertime" => $otime,
										":paymenttime" => null,
										":shippingtime" => null,
										":status" => 'new',
										":hashlink" => $link
									)
								);

			$this->id = $db->lastInsertId();
			$this->hashlink = $link;

			return $this->id;

		} else {

			$result = $db->update( "UPDATE tbl_order SET number=:number, bill=:bill, user=:user, ordertime=:ordertime, paymenttime=:paymenttime, shippingtime=:shippingtime, status=:status, hashlink=:hashlink WHERE id = :id;",
					array( ":number" => $this->number,
							":bill" => $this->bill,
							":user" => $this->user,
							":ordertime" => $this->ordertime,
							":paymenttime" => $this->paymenttime,
							":shippingtime" => $this->shippingtime,
							":status" => $this->status,
							":hashlink" => $this->hashlink,
							":id" => $this->id ) );

			return $result;
		}
	}

	public function delete(){
		$db = Database::get();
		return $db->delete( "DELETE FROM tbl_order WHERE id = :id;", array( ":id" => $this->id ) );
	}

	public static function find($id){
		$db = Database::get();

		$resultSet = $db->selectSingle( "SELECT * FROM tbl_order WHERE id = :id;", array( ":id" => $id ) );

		if( $resultSet != null ) {
			$item = new Order($resultSet);
			return $item;
		}
		return null;
	}
	
	public static function findHash($hash){
		$db = Database::get();
	
		$resultSet = $db->selectSingle( "SELECT * FROM tbl_order WHERE hashlink = :hash;", array( ":hash" => $hash ) );
	
		if( $resultSet != null ) {
			$item = new Order($resultSet);
			return $item;
		}
		return null;
	}

	public static function getAll(){
		$db = Database::get();

		$resultSet = $db->select( "SELECT * FROM tbl_order;" );

		$orderArray = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $row ) {
				array_push( $orderArray, new Order($row));
			}
		}
		return $orderArray;
	}

	private function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),

				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,

				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,

				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	
	public function getSum(){
		$sum = 0;
		foreach ($this->getItems() as $item){
			$sum += $item->amount * $item->getItem()->price;
		}
		return $sum;
	}
}