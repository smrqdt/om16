<?php
namespace Tapeshop;

use ActiveRecord\DateTime;
use Tapeshop\Models\Order;
use Tapeshop\Controllers\OrderController;
use Tapeshop\Models\PaymentMethod;

class PaypalPayment extends \Slim\Middleware {

	function call(){
		$this->app->hook('slim.before', array($this, 'addRoutes'));

		$this->next->call();
	}

	public function notification(){

		// Send an empty HTTP 200 OK response to acknowledge receipt of the notification
		header('HTTP/1.1 200 OK');

		// 		Extract variables from the notification for later processing.
		// 		Note that how you process a particular notification depends on its type. For example, if a notification applies to a completed payment, you could extract these variables from the message:

		// Assign payment notification values to local variables
		$item_name        = $_POST['item_name'];
		$item_number      = $_POST['item_number'];
		$payment_status   = $_POST['payment_status'];
		$payment_amount   = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id           = $_POST['txn_id'];
		$receiver_email   = $_POST['receiver_email'];
		$payer_email      = $_POST['payer_email'];

		//Use the notification to build the acknowledgement message required by the IPN authentication protocol.
		// Build the required acknowledgement message out of the notification just received
		$req = 'cmd=_notify-validate';               // Add 'cmd=_notify-validate' to beginning of the acknowledgement

		foreach ($_POST as $key => $value) {         // Loop through the notification NV pairs
			$value = urlencode(stripslashes($value));  // Encode these values
			$req  .= "&$key=$value";                   // Add the NV pairs to the acknowledgement
		}
		//Post the acknowledgement back to PayPal, so PayPal can determine whether the original notification was tampered with.
		// Set up the acknowledgement request headers
		$header  = "POST /cgi-bin/webscr HTTP/1.1\r\n";                    // HTTP POST request
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		// Open a socket for the acknowledgement request
		$fp = fsockopen('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

		// Send the HTTP POST request back to PayPal for validation
		fputs($fp, $header . $req);
		//Parse PayPal's response to your acknowledgement to determine whether the original notification was OK - if so, process it.
		while (!feof($fp)) {                     // While not EOF
			$res = fgets($fp, 1024);               // Get the acknowledgement response
			if (strcmp ($res, "VERIFIED") == 0) {  // Response contains VERIFIED - process notification

				// Send an email announcing the IPN message is VERIFIED
				$mail_From    = "IPN@example.com";
				$mail_To      = "rokr42@gmail.com"; //TODO: set email addresses
				$mail_Subject = "VERIFIED IPN";
				$mail_Body    = $req;
				mail($mail_To, $mail_Subject, $mail_Body, $mail_From);

				// Authentication protocol is complete - OK to process notification contents

				// Possible processing steps for a payment include the following:

				// Check that txn_id has not been previously processed
		/*		$order = Order::find('first', array("conditions" => array("payment_id = ?", $txn_id)));
				
				if($order != null){
					print "Transaction ID already used.";
					return;
				}
				
				// Check that receiver_email is your Primary PayPal email
				// Check that payment_amount/payment_currency are correct

				// TODO: extract paypal fees from value
				// update payment method and payment fee
				$order = Order::find($item_name);
				
				if($order->getSum() + $order->getFeeFor('paypal') == $payment_amount){
					
				}
				
				
				$order->payment_method_id = 1;
				$order->payment_status = $payment_status;
				$order->payment_id = $txn_id;
				$order->payment_fee = ($payment_amount * 100) - $order->getSum();

				// Check that the payment_status is Completed
				if($payment_status == "Completed"){
					$order->status = 'payed';
				}

				try{
					$order->save();
					$mailSuccess = EmailOutbound::sendNotification($order);
				}catch(Exception $e){
					print $e;
				}
		*/
			}else if (strcmp ($res, "INVALID") == 0) { //Response contains INVALID - reject notification

				// Authentication protocol is complete - begin error handling

				// Send an email announcing the IPN message is INVALID
				$mail_From    = "IPN@example.com";
				$mail_To      = "rokr42@gmail.com"; //TODO: set email addresses
				$mail_Subject = "INVALID IPN";
				$mail_Body    = $req;

				mail($mail_To, $mail_Subject, $mail_Body, $mail_From);
			}
		}

		// 		Close the file and end the PHP script.
		fclose($fp);  // Close the file
	}

	public function addRoutes(){
		$this->app->post('/payment/paypal/notification', array($this, 'notification'))->name('payment.paypal.notification');
	}
}