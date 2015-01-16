<?php
namespace Tapeshop\Controllers\Helpers;
use Tapeshop\Controllers\OrderStatus;

require_once 'vendor/swiftmailer/swiftmailer/lib/swift_required.php';
/**
 * Helper class for Email notifications.
 */
class EmailOutbound {
	//TODO use templates, e.g. smarty
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

		$subject = "Deine Rechnung vom Tapeshop";

		// Get Adress (and Name) of the user that ordered
		$adress = $order->user->currentAddress();

		// Create the Header of the Messagetext
		$messageHeader = "Hallo " . $adress->name . ",

Vielen Dank für deine Ticketbestellung !

Im Folgenden deine Rechnung und die Überweisungsdaten, sobald das Geld bei uns eingeht, schicken wir dir deine Tickets. Bitte überweise möglichst bald, denn wir können aufgrund der großen Nachfrage keine Tickets reservieren. Nur was bis Montag bei uns eingeht, kann berücksichtigt werden.\n

Rechnungs-/Kundennummer: TS-" . $order->id;

		// Create the Billing of the Items for the Messagetext

		$messageOrderItems = "";

		foreach ($order->orderitems as $orderItem) {
			$item = $orderItem->item;
			$messageOrderItem = $orderItem->amount . " x " . $item->name . " - " . ($orderItem->price / 100) . " €\n";
			$messageOrderItems = $messageOrderItems . $messageOrderItem;
		}

		$messageOrderItems = $messageOrderItems .
			"1 x Pauschale für Verpackung und Versand - " . (($order->shipping) / 100) . ",- €\n
------------------------------------------------------------------------------------------\n
Gesamtbetrag: " . (($order->getSum()) / 100) . ",- €";

		// Create the Footer of the Messagetext
		$messageFooter = "
Bitte überweise den Gesamtbetrag mit Folgenden Daten sobald wie möglich:\n

Inhaber: Maximilian Schneider-Ludorff
KontoNr: 535370324
BLZ: 510 500 15
Kreditinstitut: Naspa Limburg
Verwendungszweck: Ticket - TS-" . $order->id . "\n

Viele Grüße, wir freuen uns auf dich !\n";

		$message = $messageHeader . $messageOrderItems . $messageFooter;
		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}

	public static function sendNotificationMail($adress, $subject, $message) {

		$message = $message . "Maximilian Schneider-Ludorff\n
-------------------------------------------------------\n
TAPEFABRIK // Logistik\n
\n
E-Mail: " . SUPPORTADRESS . "\n
Web: http://www.tapefabrik.de\n";

		$mailToSend = \Swift_Message::newInstance()
		  ->setSubject($subject)
		  ->setFrom(array(SHOPADRESS => 'Tapefabrik Ticketshop'))
		  ->setTo($adress)
		  ->setBody($message);

		$transport = \Swift_MailTransport::newInstance();
		$mailer = \Swift_Mailer::newInstance($transport);
		return $mailer->send($mailToSend);
	}

	public static function sendPaymentConfirmation($order) {
		// Get Adress (and Name) of the user that ordered

		$subject = "Bezahlung für Bestellung ".$order->id." ist bei uns eingegangen !";

		$message = 	"Hallo ".$order->address->name.",\n
Der Gesamtbetrag für deine Bestellung mit der Nummer TS-2015-".$order->id." ist bei uns eingegangen.\n
Vielen Dank !\n
\n
Deine Bestellung wird so bald wie möglich verschickt, sobald das passiert ist bekommst du erneut eine Nachricht von uns.\n\n";

		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}

	public static function sendShippedConfirmation($order) {
		$subject = "Die Bestellung TS-2015-".$order->id." wurde versendet !";

		$message = 	"Hallo ".$order->address->name.",\n
Deine Bestellung mit der Nummer TS-2015-".$order->id." wurde versendet.\n
 Vielen Dank für deine Bestellung bei uns!\n
\n";
		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}
}