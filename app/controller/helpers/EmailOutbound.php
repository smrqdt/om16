<?php
/**
 * Helper class for Email notifications.
 */
class EmailOutbound {
	
	public static function sendBilling($order){

		$subject = "Deine Rechnung vom Tapeshop";

		// Get Adress (and Name) of the user that ordered
		$adress = $order->user->currentAddress();

		// Create the Header of the Messagetext
		$messageHeader = "Hallo ".$adress->name.",\n
					\n
					Vielen Dank für deine Ticketbestellung !\n
					\n
					Im Folgenden deine Rechnung und die Überweisungsdaten, sobald das Geld bei uns eingeht, schicken wir dir deine Tickets. Bitte überweise möglichst bald, denn wir können aufgrund der großen Nachfrage keine Tickets reservieren. Nur  was bis Montag bei uns eingeht, kann berücksichtigt werden.\n
					\n
					Rechnungs-/Kundennummer: TS-".$order->id."
					\n
					";

		// Create the Billing of the Items for the Messagetext

		$messageOrderItems = "";

		foreach($order->orderitems as $orderItem){
			$item = $orderItem->item;
			$messageOrderItem = $orderItem->amount." x ".$item->name." - ".($orderItem->price/100)." €\n";
			$messageOrderItems = $messageOrderItems.$messageOrderItem;
		}

		$messageOrderItems = $messageOrderItems.
		"1 x Pauschale für Verpackung und Versand - 1,- €\n
		------------------------------------------------------------------------------------------\n
		Gesamtbetrag: ".$order->getSum().",- €";

		// Create the Footer of the Messagetext
		$messageFooter = "
					Bitte überweise den Gesamtbetrag mit Folgenden Daten sobald wie möglich:\n
					\n
					Inhaber: Maximilian Schneider-Ludorff\n
					KontoNr: 535370324\n
					BLZ: 510 500 15\n
					Kreditinstitut: Naspa Limburg\n
					Verwendungszweck: Ticket - TS-".$order->id."\n
					\n
					Viele Grüße, wir freuen uns auf dich !\n
					\n
					Maximilian Schneider-Ludorff\n
					-------------------------------------------------------\n
					TAPEFABRIK // Logistik\n
					\n
					E-Mail: ".SUPPORTADRESS."\n
					Web: http://www.tapefabrik.de\n";

		$message = $messageHeader.$messageOrderItems.$messageFooter;
		EmailOutbound::sendNotificationMail($order->user->email, $subject, $message);
	}

	public static function sendNotificationMail($adress, $subject, $message){
		$header = 'From: '.SHOPADRESS. "\r\n" .
		    'Reply-To: ' .SUPPORTADRESS. "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		mail($adress, $subject, $message, $header);
	}

	public static function sendPaymentConfirmation($order){

		echo "<h1>User-Adresse</h1>";

		echo '<pre>'; print_r($order->user->currentAddress()); echo '</pre>';

		foreach($order->orderitems as $orderitem){
			echo "<h1>OrderItem</h1>";
			echo '<pre>'; print_r($orderitem); echo '</pre>';
			echo "<h1>Item of Order Item</h1>";
			echo '<pre>'; print_r($orderitem->item); echo '</pre>';
		}

		return true;

	}

	public static function sendShippedConfirmation($order){
		return true;
	}

	/**
	 * Send a notification email.
	 * @param Order $order
	 */
	public function sendNotification($order){
		print_r($order);
		echo '<pre>'; print_r($order); echo '</pre>';
		
		// Check which mail is to be send.
		switch($order->status) {
			case "new":
			EmailOutbound::sendBilling($order);
			break;

			case "payed":
			EmailOutbound::sendPaymentConfirmation($order);
			break;

			case "shipped":
			EmailOutbound::sendShippedConfirmation($order);
			break;
		}
	}

}