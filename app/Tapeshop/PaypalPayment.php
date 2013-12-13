<?php
namespace Tapeshop;

use ActiveRecord\DateTime;

use Tapeshop\Models\Order;
use Tapeshop\Models\PaymentMethod;

class PaypalPayment extends \Slim\Middleware {

	function call(){
		$this->app->hook('slim.before', array($this, 'addRoutes'));

		$this->next->call();
	}

	/**
	 * Handle Paypal IPN requests.
	 */
	public function notification(){
		// Array containing configuration parameters. (not required if config file is used)
		$config = array(
				// values: 'sandbox' for testing
				//         'live' for production
				"mode" => "sandbox"

				// These values are defaulted in SDK. If you want to override default values, uncomment it and add your value.
				// "http.ConnectionTimeOut" => "5000",
				// "http.Retry" => "2",
		);

		$ipnMessage = new \PPIPNMessage(null, $config);
		$data = $ipnMessage->getRawData();

		foreach($ipnMessage->getRawData() as $key => $value) {
			error_log("IPN: $key => $value");
		}

		// Authentication protocol is complete - OK to process notification contents
		if($ipnMessage->validate()) {
			error_log("Success: Got valid IPN data");
			// Extract variables from the notification for later processing.
			// Note that how you process a particular notification depends on its type. For example, if a notification applies to a completed payment, you could extract these variables from the message:
			// Assign payment notification values to local variables
			$item_name        = $data['item_name'];
			$item_number      = $data['item_number'];
			$payment_status   = $data['payment_status'];
			$payment_amount   = $data['mc_gross'];
			$payment_currency = $data['mc_currency'];
			$txn_id           = $data['txn_id'];
			$receiver_email   = $data['receiver_email'];
			$payer_email      = $data['payer_email'];

			$order = Order::find('first', array("conditions" => array("payment_id = ?", $txn_id)));

			if($order != null){
				error_log("Transaction ID already used");
				return;
			}
				
			if($receiver_email != PAYPAL_EMAIL){
				error_log("Wrong receiver email");
				return;
			}

			$order = Order::find($item_name);
				
			if($order == null){
				error_log("No order with id " . $item_name ." found.");
				return;
			}

			if($order->getSum() + $order->getFeeFor('paypal') <= $payment_amount){
				// Check that the payment_status is Completed
				if($payment_status == "Completed"){
					$order->status = 'payed';
					$order->paymenttime = new DateTime();
				}
			}

			// update payment method and payment fee
			$order->payment_method_id = 1;
			$order->payment_status = $payment_status;
			$order->payment_id = $txn_id;
			$order->payment_fee = $order->getFeeFor('paypal');

			try{
				$order->save();
				$mailSuccess = EmailOutbound::sendNotification($order);
			}catch(Exception $e){
				error_log($e->getMessage());
			}
		} else {
			error_log("Error: Got invalid IPN data");
		}
	}

	public function addRoutes(){
		$this->app->post('/payment/paypal/notification', array($this, 'notification'))->name('payment.paypal.notification');
	}
}