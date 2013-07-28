<?php

class Item extends ActiveRecord\Model {

	static $has_many = array(
			array('sizes'),
			array('orderitems'),
			array('itemnumbers')
	);

	function getFreeNumbers(){
		return Itemnumber::find('all', array('conditions' => array('item_id = ? and valid = true and free = true', $this->id))); 
	}
	
	function getUnrequestedNumberCount(){
		$numbers = array();
		$count = count($this->getFreeNumbers());
		
		if($count == 0){
			return 0;
		}
		
		foreach($this->orderitems as $oi){
			if($oi->order->status == 'new'){
				$count -= $oi->amount;
			}
		}
		return $count;
	}

	function getInvalidNumbers(){
		return Itemnumber::find('all', array('conditions' => array('item_id = ? and valid = false', $this->id)));
	}
}