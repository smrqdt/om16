<?php
namespace Tapeshop\Controllers\Helpers;
use Tapeshop\Controllers\OrderStatus;

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

		$header = 'From: ' . "noreply@example.com" . "\r\n" . 'Reply-To: ' . "noreply@example.com" . "\r\n" . 'X-Mailer: PHP/' . phpversion();

		$message = $message . "Maximilian Schneider-Ludorff\n
-------------------------------------------------------\n
TAPEFABRIK // Logistik\n
\n
E-Mail: " . SUPPORTADRESS . "\n
Web: http://www.tapefabrik.de\n";

		return mail($adress, $subject, $message, $header);
	}

	public static function sendPaymentConfirmation($order) {
		$subject = "Bezahlung für Bestellung " . $order->id . " ist bei uns eingegangen !";

		$message = "Hallo " . $order->address->name . ",\n
Der Gesamtbetrag für deine Bestellung mit der Nummer" . $order->id . " ist bei uns eingegangen.\n
\n
Vielen Dank !\n
\n
Deine Bestellung wird baldmöglichst verschickt, sobald das passiert ist bekommst du erneut eine Nachricht von uns.\n";

		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}

	public static function sendShippedConfirmation($order) {
		$subject = "Die Bestellung T-04-" . $order->id . " wurde versendet !";

		$message = "Hallo " . $order->address->name . ",\n
Deine Bestellung mit der Nummer T-04-" . $order->id . " wurde versendet.\n
\n
Vielen Dank für deine Bestellung bei uns!\n
\n";
		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}
}