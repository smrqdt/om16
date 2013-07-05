<?php

class Order extends ActiveRecord\Model {
	
	static $belongs_to = array(
			array('user'),
			array('address')
	);
	
	static $has_many = array(
			array('orderitems'),
			array('items', 'through' => 'orderitems')
	);
	
	public function getSum(){
		$sum = 0;
		foreach ($this->orderitems as $item){
			$sum += $item->amount * $item->price;
		}
		return $sum;
	}
}