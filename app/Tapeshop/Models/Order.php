<?php
namespace Tapeshop\Models;

class Order extends \ActiveRecord\Model {
	
	static $belongs_to = array(
			array('user'),
			array('address')
	);
	
	static $has_many = array(
			array('orderitems', 'class_name' => '\Tapeshop\Models\Orderitem'),
			array('items', 'through' => 'orderitems')
	);

	public function getSum(){
		$sum = 0;
		foreach ($this->orderitems as $item){
			$sum += $item->amount * $item->price;
		}
		return ($sum + $this->shipping + $this->payment_fee);
	}
	
	public function getOrderId(){
		return ORDER_PREFIX . $this->id;
	}
	
	public function getFeeFor($method){
		switch($method){
			case 'sofort':
				$pm = PaymentMethod::find('first', array("conditions" => array("name = 'sofort'")));
				return $pm->fix + $this->getSum() * $pm->fee;
			case 'paypal':
				$pm = PaymentMethod::find('first', array("conditions" => array("name = 'paypal'")));
				return $pm->fix + $this->getSum() * $pm->fee;
			default:
				return 0;
		}
	}
}