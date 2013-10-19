<?php

include 'vendor/autoload.php';
include 'config.php';

ActiveRecord\Config::initialize(function($cfg) {
	$cfg->set_model_directory('.');
	$cfg->set_connections(array('development' => DB_PROVIDER.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOSTNAME.'/'.DB_NAME));
});

$orders = Order::find('all', array('conditions' => array("status = 'payed'")));

foreach($orders as $order){
	foreach($order->orderitems as $orderitem){
		if($orderitem->item->numbered){
			if($orderitem->amount > count($orderitem->itemnumbers)){
				$freenumbers = $orderitem->item->getFreeNumbers();
				
				if(count($freenumbers) >= $orderitem->amount){
					for($i = 0; $i < $orderitem->amount - count($orderitem->itemnumbers); $i++){
						$itemnumber = $freenumbers[$i];
						$itemnumber->orderitem_id = $orderitem->id;
						try{
							$itemnumber->save();
						}catch(ActiveRecord\ActiveRecordException $e){
							echo "Could not addign item number for  " . $orderitem->item->name . "\n";
						}
					}
				}else{
					echo "Not enough numbers left for " . $orderitem->item->name . "\n";
				}
			}
		}
	}
}
echo "done";