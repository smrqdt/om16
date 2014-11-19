<?php
namespace Tapeshop\Controllers\Helpers;

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
			case "new":
				return EmailOutbound::sendBilling($order);
				break;

			case "payed":
				return EmailOutbound::sendPaymentConfirmation($order);
				break;

			case "shipped":
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

		$subject = "Danke! Dein Bestellung TS-2015-".$order->id." im Tapefabrik Shop";

		// Get Adress (and Name) of the user that ordered
		$adress = $order->user->currentAddress();

		// Create the Header of the Messagetext
		$messageHeader = "Hallo ".$adress->name.",

Vielen Dank für deine Ticketbestellung !

Im Folgenden deine Rechnung und die Überweisungsdaten. Sobald das Geld bei uns eingeht, sind deine Tickets fest für dich hinterlegt. Der Ticketversand startet in diesem Jahr am 20. November. Bitte überweise möglichst bald, denn wir können aufgrund der großen Nachfrage keine Tickets reservieren. Nur was innerhalb von 14 Tagen bei uns eingeht, kann berücksichtigt werden.

Wir versuchen jedes Jahr eine ganz besondere Veranstaltung auf die Beine zu stellen, ohne dich wäre das nicht möglich. Vielen Dank für deinen Support !\n

Rechnungs-/Kundennummer: TS-2015-".$order->id."\n\n";

		// Create the Billing of the Items for the Messagetext

		$messageOrderItems = "";

		foreach($order->orderitems as $orderItem){
			$item = $orderItem->item;
			$messageOrderItem = $orderItem->amount." x ".$item->name." - ".($orderItem->price/100)." €\n";
			$messageOrderItems = $messageOrderItems.$messageOrderItem;
		}

		$messageOrderItems = $messageOrderItems.
		"1 x Pauschale für Verpackung und Versand - ".(($order->shipping)/100)." €\n
------------------------------------------------------------------------------------------\n
Gesamtbetrag: ".(($order->getSum())/100).",- €";

// Create the Footer of the Messagetext
$messageFooter = "
\nBitte überweise den Gesamtbetrag mit Folgenden Daten sobald wie möglich an:\n

Inhaber: Maximilian Schneider-Ludorff
Konto-Nr: 089 308 7700
BLZ: 700 400 48
IBAN: DE96 7004 0048 0893 0877 00
BIC: COBADEFFXXX
Kreditinstitut: Commerzbank München
Verwendungszweck: Ticket - TS-2015-".$order->id."\n

Viele Grüße, wir freuen uns auf dich !\n";

		$message = $messageHeader.$messageOrderItems.$messageFooter;
		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}

	public static function sendNotificationMail($adress, $subject, $message) {

		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=utf-8";
		$headers[] = "From: ".SHOPADRESS;
		$headers[] = "Reply-To: ".SUPPORTADRESS;
		$headers[] = "Subject: ".$subject;
		$headers[] = "X-Mailer: PHP/".phpversion();

		$message = $message."Maximilian Schneider-Ludorff\n
-------------------------------------------------------\n
TAPEFABRIK // http://www.tapefabrik.de // TICKETSHOP\n
E-Mail: ".SUPPORTADRESS."\n
Web: http://www.tapefabrik.de\n";

		return mail($adress, $subject, $message, implode("\r\n", $headers));
	}

	public static function sendPaymentConfirmation($order) {
		// Get Adress (and Name) of the user that ordered
		$adress = $order->user->currentAddress();

		$subject = "Bezahlung für Bestellung ".$order->id." ist bei uns eingegangen !";

		$message = 	"Hallo ".$order->address->name.",\n
Der Gesamtbetrag für deine Bestellung mit der Nummer TS-2015-".$order->id." ist bei uns eingegangen.\n
Vielen Dank !\n
Deine Bestellung wird so bald wie möglich verschickt, sobald das passiert ist bekommst du erneut eine Nachricht von uns.\n\n";

		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}

	public static function sendShippedConfirmation($order) {
		// Get Adress (and Name) of the user that ordered
		$adress = $order->user->currentAddress();

		$subject = "Die Bestellung TS-2015-".$order->id." wurde versendet !";

		$message = 	"Hallo ".$order->address->name.",\n
Deine Bestellung mit der Nummer TS-2015-".$order->id." wurde versendet.\n
Vielen Dank für deine Bestellung bei uns!\n
\n";
		return EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}
}