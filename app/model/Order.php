<?php

class Order extends ActiveRecord\Model {
	
	static $belongs_to = array(
			array('user')
	);
	
	static $has_many = array(
			array('orderitems'),
			array('items', 'through' => 'orderitems')
	);
	
	public function getSum(){
		$sum = 0;
		foreach ($this->orderitems as $item){
			$sum += $item->amount * $item->item->price;
		}
		return $sum;
	}
}