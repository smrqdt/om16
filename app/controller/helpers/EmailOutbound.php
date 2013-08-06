<?php
/**
 * Helper class for Email notifications.
 */
class EmailOutbound {
	
	/**
	 * Send a notification email.
	 * @param Order $order
	 */
	static function sendNotification($order){
		
		// Check which mail is to be send.
		print_r($order);

	}

	static function sendBilling($order){
		$message = "Hallo ".VORNAME.",

					Vielen Dank für deine Ticketbestellung !

					Im Folgenden deine Rechnung und die Überweisungsdaten, sobald das Geld bei uns eingeht, schicken wir dir deine Tickets. Bitte überweise möglichst bald, denn wir können aufgrund der großen Nachfrage keine Tickets reservieren. Nur  was bis Montag bei uns eingeht, kann berücksichtigt werden.

					Rechnungs-/Kundennummer: ".RECHNUNGSNUMMER."

					#ANZAHL# x VVK-Ticket - Tapefabrik #3 mit Die Bestesten, Mach One u.a. - 21,- €
					1 x Pauschale für Verpackung und Versand - 1,- €
					------------------------------------------------------------------------------------------
					Gesamtbetrag: #GESAMTBETRAG#,- €

					Bitte überweise den Gesamtbetrag mit Folgenden Daten sobald wie möglich:

					Inhaber: Maximilian Schneider-Ludorff
					KontoNr: 535370324
					BLZ: 510 500 15
					Kreditinstitut: Naspa Limburg
					Verwendungszweck: Ticket - <DEINE Rechnungsnummer>

					Viele Grüße, wir freuen uns auf dich !

					Maximilian Schneider-Ludorff
					-------------------------------------------------------
					TAPEFABRIK // Logistik

					E-Mail: tickets@tapefabrik.de
					Web: http://www.tapefabrik.de";
	}

	static function sendNotificationMail($adress, $subject, $message){
		$header = 'From: '.SHOPADRESS. "\r\n" .
		    'Reply-To: ' .SUPPORTADRESS. "\r\n" .
		    'X-Mailer: PHP/' . phpversion();

		mail($adress, $subject, $message, $header);
	}

}