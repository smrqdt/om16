<?php

class Item extends ActiveRecord\Model {

	static $has_many = array(
			array('sizes'),
			array('orderitems'),
			array('itemnumbers')
	);

	/*
	 * TODO: I think this can also be achieved with the OR mapper and a query, but I am to lazy to figure it out.
	*/
	function getFreeNumbers(){
		$free = array();
		foreach($this->itemnumbers as $n){
			if($n->valid && $n->free){
				array_push($free, $n);
			}
		}
		return $free;
	}
	
	function getUnrequestedNumberCount(){
		$numbers = array();
		$count = count($this->getFreeNumbers());
		
		if($count == 0){
			return 0;
		}
		
		foreach($this->orderitems as $oi){
			if($oi->order->status != 'payed'){
				$count -= $oi->amount;
			}
		}
		return $count;
	}

	/*
	 * TODO: I think this can also be achieved with the OR mapper and a query, but I am to lazy to figure it out.
	*/
	function getInvalidNumbers(){
		$invalid = array();
		foreach($this->itemnumbers as $n){
			if(!$n->valid){
				array_push($invalid, $n);
			}
		}
		return $invalid;
	}
}