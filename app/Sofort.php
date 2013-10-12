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
		$order = Order::find($this->app->request()->post('orderId'));
		$v = new \Valitron\Validator($this->app->request()->post());
		$v->rule('required', array('bankcode', 'accountNumber', 'accountHolder'));
		$v->rule('regex', 'bankcode', '/[0-9]{8}/');
		$v->rule('regex', 'accountNumber', '/[0-9]{1,10}/');
		if($v->validate()){
			$bankcode = $this->app->request()->post('bankcode');
			$accountNumber = $this->app->request()->post('accountNumber');
			$accountHolder = $this->app->request()->post('accountHolder');
			
			$Sofort = new SofortLib_Multipay(SOFORT_CONFIG);
			$Sofort->setSofortueberweisung();
			$Sofort->setAmount($order->getSum()/100.0);
			$Sofort->setReason(SOFORT_REASON, $order->getOrderId());
			// bank code, account, holder
			//TODO fix bank code
			$Sofort->setSenderAccount('88888888', $accountNumber, $accountHolder);
			$url = 'http://' .$_SERVER['SERVER_NAME'] . $this->app->urlFor('order', array('hash' => $order->hashlink));
// 			$url = 'http://local.euleule.name'. $this->app->urlFor('order', array('hash' => $order->hashlink));
// 			print_r($_SERVER);
// 			echo $url;
			$Sofort->setSuccessUrl($url);
			$Sofort->setAbortUrl($url);
			$Sofort->setTimeoutUrl($url);
			$url = 'http://' .$_SERVER['SERVER_NAME'] .$this->app->urlFor('payment.sofort.notification');
// 			$url = 'http://local.euleule.name' .$this->app->urlFor('payment.sofort.notification');
			$Sofort->setNotificationUrl($url);
			$Sofort->sendRequest();
			
			if($Sofort->isError()) {
				//PNAG-API didn't accept the data
				echo $Sofort->getError();
			} else {
				//buyer must be redirected to $paymentUrl else payment cannot be successfully completed!
				$order->payment_id = $Sofort->getTransactionId();
				$order->save();
				$paymentUrl = $Sofort->getPaymentUrl();
				//header('Location: '.$paymentUrl);
				$this->app->redirect($paymentUrl);
			}
		}else{
			$outputErrors = array();
			foreach ($v->errors() as $key => $value) {
				$outputErrors[] = $value[0];
			}
			$this->app->flash('error', $outputErrors);
			$this->app->redirect($this->app->urlFor('order', array('hash' => $order->hashlink)));
		}
	}
	
	public function notification(){
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
			exit;
		}
		
		// fetch some information for the transaction id retrieved above
		$transactionData = new SofortLib_TransactionData(SOFORT_CONFIG);
		$transactionData->setTransaction($transactionId);
		$transactionData->sendRequest();
				
// 		echo '<table border="1">';
// 		echo '<tr><td>transaction was: </td><td align="right">'.$transactionData->getTransaction().'</td></tr>';
// 		echo '<tr><td>start date is: </td><td align="right">'.$transactionData->getSofortaboStartDate().'</td></tr>';
// 		echo '<tr><td>amount is: </td><td align="right">'.$transactionData->getAmount().' '.$transactionData->getCurrency().'</td></tr>';
// 		echo '<tr><td>interval is: </td><td align="right">'.$transactionData->getSofortaboInterval().'</td></tr>';
// 		echo '<tr><td>minimum payments: </td><td align="right">'.$transactionData->getSofortaboMinimumPayments().'</td></tr>';
// 		echo '<tr><td>status is: </td><td align="right">'.$transactionData->getStatus(). ' - '. $transactionData->getStatusReason().'</td></tr>';
// 		echo '</table>';
		
		if($transactionData->getStatus() == "received"){
			$order->paymenttime = new DateTime();
			$order->save();
		}	
	}
	
	public function timeout($hash){
		$order = Order::find(
				'first',
				array(
						'hashlink' => $hash
				)
		);

		$this->app->flash('warning', _('payment.sofort.timeout'));
		$this->app->redirect($this->app->urlFor('order', array('hash' => $order->hashlink)));
	}
	
	public function success(){
		$order = Order::find(
				'first',
				array(
						'hashlink' => $hash
				)
		);
		$order->payment_method_id = 2;
		$order->save();
		$this->app->flash('success', _('payment.sofort.success'));
		$this->app->redirect($this->app->urlFor('order', array('hash' => $order->hashlink)));
	}
	
	public function addRoutes(){
		$this->app->post('/payment/sofort/pay', array($this, 'pay'));
		$this->app->post('/payment/sofort/notification', array($this, 'notification'))->name('payment.sofort.notification');
		$this->app->get('/payment/sofort/timeout/:hash', array($this, 'timeout'))->name('payment.sofort.timeout');
		$this->app->get('/payment/sofort/success/:hash', array($this, 'success'))->name('payment.sofort.success');
	}
}