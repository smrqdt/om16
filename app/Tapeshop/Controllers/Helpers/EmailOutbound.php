<?php
namespace Tapeshop\Controllers\Helpers;
use Slim\Slim;
use Smarty;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Tapeshop\Controllers\OrderStatus;

/**
 * Helper class for Email notifications.
 */
class EmailOutbound {

	const TEMPLATE_BILLING = "email/billing.tpl";
	const TEMPLATE_PAYMENT = "email/payment.tpl";
	const TEMPLATE_SHIPPING = "email/shipping.tpl";
	const TEMPLATE_REMINDER = "email/reminder.tpl";

	/**
	 * Send a reminder email.
	 * @param \Tapeshop\Models\Order $order
	 * @return boolean
	 */
	public static function sendReminder($order){
		$smarty = new Smarty();
		$smarty->setTemplateDir(Slim::getInstance()->view()->getTemplatesDirectory());

		$data = EmailOutbound::getDataForOrder($order);

		$message = $smarty->fetch(EmailOutbound::TEMPLATE_REMINDER, $data);

		return EmailOutbound::sendNotificationMail($order->user->email, $message);
	}

	/**
	 * Send a notification email.
	 * @param \Tapeshop\Models\Order $order
	 * @return boolean
	 */
	public function sendNotification($order) {
		// Check which mail is to be send.
		switch ($order->status) {
			case OrderStatus::NEW_ORDER:
				return EmailOutbound::sendBilling($order);
				break;

			case OrderStatus::PAYED:
				return EmailOutbound::sendPaymentConfirmation($order);
				break;

			case OrderStatus::SHIPPED:
				return EmailOutbound::sendShippedConfirmation($order);
				break;
		}
		return false;
	}

	/**
	 * @param \Tapeshop\Models\Order $order
	 * @return bool
	 */
	public static function sendBilling($order) {
		$smarty = new Smarty();
		$smarty->setTemplateDir(Slim::getInstance()->view()->getTemplatesDirectory());

		$data = EmailOutbound::getDataForOrder($order);

		$message = $smarty->fetch(EmailOutbound::TEMPLATE_BILLING, $data);

		return EmailOutbound::sendNotificationMail($order->user->email, $message);
	}

	public static function sendPaymentConfirmation($order) {
		$smarty = new Smarty();
		$smarty->setTemplateDir(Slim::getInstance()->view()->getTemplatesDirectory());

		$data = EmailOutbound::getDataForOrder($order);

		$message = $smarty->fetch(EmailOutbound::TEMPLATE_PAYMENT, $data);

		return EmailOutbound::sendNotificationMail($order->user->email, $message);
	}

	public static function sendShippedConfirmation($order) {
		$smarty = new Smarty();
		$smarty->setTemplateDir(Slim::getInstance()->view()->getTemplatesDirectory());

		$data = EmailOutbound::getDataForOrder($order);

		$message = $smarty->fetch(EmailOutbound::TEMPLATE_SHIPPING, $data);

		return EmailOutbound::sendNotificationMail($order->user->email, $message);
	}

	public static function sendNotificationMail($adress, $message) {
		/** @var Swift_Message $mailToSend */
		$mailToSend = Swift_Message::newInstance()
			->setSubject(SHOP_EMAIL_SUBJECT)
			->setFrom(array(SHOP_EMAIL_FROM => SHOP_NAME))
			->setTo($adress)
			->setBody($message);

		if(SMTP_AUTH_DISABLED == true){
//			$transport = Swift_SmtpTransport::newInstance(SMTP_HOST, SMTP_PORT, 'ssl');
			$transport = Swift_SmtpTransport::newInstance(SMTP_HOST, SMTP_PORT);
		}else{
			$transport = Swift_SmtpTransport::newInstance(SMTP_HOST, SMTP_PORT, 'ssl')
				->setUsername(SMTP_USER)
				->setPassword(SMTP_PASSWORD);
		}

		$mailer = Swift_Mailer::newInstance($transport);

		return $mailer->send($mailToSend);
	}

	private static function getDataForOrder($order){
		return array(
			"email"=> SHOP_EMAIL_REPLYTO,
			"orderPrefix" => ORDER_PREFIX,
			"name" => $order->address->name,
			"shopName" => SHOP_NAME,
			"order" => $order,
			"url" => SHOP_URL
		);
	}
}
