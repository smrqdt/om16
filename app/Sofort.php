<?php
use ActiveRecord\DateTime;
require_once ("lib/SofortLib_1.5.5/library/sofortLib.php");

class SofortPayment extends \Slim\Middleware {

	function call(){
		$this->app->hook('slim.before', array($this, 'addRoutes'));

		$this->next->call();
	}

	public function pay(){
		// 		$l = new SofortLibLogger();
		// 		$l->log('test', '/Users/robert/Sites/tapeshop/log.txt');
		$req = $this->app->request();
		$order = Order::find($req->post('orderId'));
		$v = new \Valitron\Validator($req->post());
		$v->rule('required', array('bankcode', 'accountNumber', 'accountHolder'));
		$v->rule('regex', 'bankcode', '/[0-9]{8}/');
		$v->rule('regex', 'accountNumber', '/[0-9]{1,10}/');
		if($v->validate()){
			$bankcode = $req->post('bankcode');
			$accountNumber = $req->post('accountNumber');
			$accountHolder = $req->post('accountHolder');
				
			$Sofort = new SofortLib_Multipay(SOFORT_CONFIG);
			$Sofort->setSofortueberweisung();
			echo ($order->getSum() + $order->getFeeFor('sofort'))/100.0;
			$Sofort->setAmount(number_format(($order->getSum() + $order->getFeeFor('sofort'))/100.0, 2));
			$Sofort->setReason(SOFORT_REASON, $order->getOrderId());

			//TODO fix bank code
			$Sofort->setSenderAccount('88888888', $accountNumber, $accountHolder);

			$url = "http://" . $_SERVER["SERVER_NAME"] . $this->app->urlFor('payment.sofort.success', array("hash" => $order->hashlink));
			$Sofort->setSuccessUrl($url);
			$url = "http://" . $_SERVER["SERVER_NAME"] . $this->app->urlFor('payment.sofort.abort', array("hash" => $order->hashlink));
			$Sofort->setAbortUrl($url);
			$url = "http://" . $_SERVER["SERVER_NAME"] . $this->app->urlFor('payment.sofort.timeout', array("hash" => $order->hashlink));
			$Sofort->setTimeoutUrl($url);
			$url = "http://" . $_SERVER["SERVER_NAME"] . $this->app->urlFor('payment.sofort.notification');
			$Sofort->setNotificationUrl($url);
			$Sofort->sendRequest();
				
			if($Sofort->isError()) {
				// remote API responded with error
// 				$this->app->flash('error', $Sofort->getError());
// 				$url = APP_PATH . "order/". $order->hashlink;
// 				$this->app->redirect($url);
echo $Sofort->getError();
			} else {
				// set payment infos
				$order->payment_id = $Sofort->getTransactionId();
				$order->payment_fee = $order->getFeeFor('sofort');
				$order->payment_method_id = 2;
				$order->save();
				
				// redirect user to remote payment site
				$paymentUrl = $Sofort->getPaymentUrl();
				$this->app->redirect($paymentUrl);
			}
		}else{
			// show validation errors
			$outputErrors = array();
			foreach ($v->errors() as $key => $value) {
				$outputErrors[] = $value[0];
			}
			$this->app->flash('error', $outputErrors);
			$url = APP_PATH . "order/". $order->hashlink;
			$this->app->redirect($url);
		}
	}
	
	public function notification(){
		//TODO: fix logging
		$l = new SofortLibLogger();
		$l->log('notification received', '/var/www/vhosts/euleule.name/httpdocs/tapeshop/log.txt');
		$notification = new SofortLib_Notification($this->app->request()->getBody());
		$notification->getNotification();

		$transactionId = $notification->getTransactionId();

		$order = Order::find(
				'first',
				array(
						'payment_id' => $transactionId
				)
		);

		if($order == null){
			echo "No order found for transaction ID " . $transactionId;
			exit;
		}

		// fetch some information for the transaction id retrieved above
		$transactionData = new SofortLib_TransactionData(SOFORT_CONFIG);
		$transactionData->setTransaction($transactionId);
		$transactionData->sendRequest();

		$order->payment_status = $transactionData->getStatus();
		if($transactionData->getStatus() == "received"){
			$oder->payment_status = $transactionData->getStatusReason();
			if($transactionData->getStatusReason() == "credited" || $transactionData->getStatusReason() == "overpayment"){
				$order->paymenttime = new DateTime();
				$order->status = "payed";
			}
		}else if($transactionData->getStatus() == "refunded"){
			$oder->payment_status = $transactionData->getStatusReason();
		}
		$order->save();
		echo "Received status " . $transactionData->getStatus() . " with reaseon ".$transactionData->getStatusReason();
	}

	public function timeout($hash){
		$order = Order::find(
				'first',
				array(
						'conditions' => array('hashlink = ?', $hash)
				)
		);

		$this->cleanup($order);

		$this->app->flash('warning', _('payment.sofort.timeout'));
		$url = APP_PATH . "order/". $order->hashlink;
		$this->app->redirect($url);
	}

	public function abort($hash){
		$order = Order::find(
				'first',
				array(
						'conditions' => array('hashlink = ?', $hash)
				)
		);

		$this->cleanup($order);

		$this->app->flash('warning', _('payment.sofort.abort'));
		$url = APP_PATH . "order/". $order->hashlink;
		$this->app->redirect($url);
	}

	public function success($hash){
		$order = Order::find(
				'first',
				array(
						'conditions' => array('hashlink = ?', $hash)
				)
		);
		
		$order->payment_method_id = 2;
		$order->save();
		$this->app->flash('success', _('payment.sofort.success'));
		$url = APP_PATH . "order/". $order->hashlink;
		$this->app->redirect($url);	}

	private function cleanup($order){
		$order->payment_id = null;
		$order->payment_fee = 0;
		$order->payment_method_id = null;
		$order->save();
	}

	public function addRoutes(){
		$this->app->post('/payment/sofort/pay', array($this, 'pay'));
		$this->app->post('/payment/sofort/notification', array($this, 'notification'))->name('payment.sofort.notification');
		$this->app->get('/payment/sofort/timeout/:hash', array($this, 'timeout'))->name('payment.sofort.timeout');
		$this->app->get('/payment/sofort/success/:hash', array($this, 'success'))->name('payment.sofort.success');
		$this->app->get('/payment/sofort/abort/:hash', array($this, 'abort'))->name('payment.sofort.abort');
	}
}