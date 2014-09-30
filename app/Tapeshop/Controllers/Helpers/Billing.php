<?php
namespace Tapeshop\Controllers\Helpers;

use fpdf\FPDF;

/**
 * @property \Tapeshop\Models\Order order
 */
class Billing extends FPDF {

	function Header() {
		$this->Meta();

		// Print the Header
		$this->SetFont('Sanchez', '', 10);
		$this->Image(APP_PATH . 'assets/img/Ticketbestellungen.png', 0, 0, -150, -150);
		$this->SetTextColor(255, 255, 255);

		$this->Ln(49.5);
		$this->Cell(16.5);

		$this->Cell(472, 3, utf8_decode($this->order->address->name . " " . $this->order->address->lastname));
		$this->Ln(6);

		$this->Cell(16.5);
		$this->Cell(472, 3, utf8_decode($this->order->address->street . " " . $this->order->address->building_number));
		$this->Ln(6);

		$this->Cell(16.5);
		$this->Cell(472, 3, utf8_decode($this->order->address->postcode . " " . $this->order->address->city));

		$this->Body();
	}

	function Meta() {
		$this->AddFont('Sanchez', '', 'sanchez.php');
	}

	function Body() {

		$this->Ln(37);
		$this->Cell(16.5);

		$this->SetTextColor(0, 0, 0);

		// TODO use templates, e.g. smarty
		$message =
			"Hallo " . $this->order->address->name . ",\n
\n
wir möchten uns bei dir noch einmal für deine Bestellung bedanken.\n
\n
Nach dem ersten Event in Limburg, versuchen wir auch in Wiesbaden, den freshesten Rap\n
Deutschlands auf die Bühnen zu stellen - ohne dich wär das nicht möglich !\n
\n
Wir freuen uns sehr auf deinen Besuch, alle Informationen die du brauchst findest du weiterhin\n
unter:\n
\n
      - tapefabrik.de\n
      - facebook.com/tapefabrik\n
\n
Wenn du möchtest lade doch bei Facebook noch deine Leute ein, uns hilft jeder Besucher !\n
\n
Hier deine Bestellung mit der Rechnungsnummer T-04-" . $this->order->id . ".\n
\n";

		foreach ($this->order->orderitems as $orderitem) {
			$message = $message . $orderitem->amount . " x - '" . $orderitem->item->name . "'\n\n";
		}

		$message = $message . "\n
Bis zum 25. Januar - Dein Tapefabrik Team !\n";
		$this->MultiCell(0, 2.2, utf8_decode($message));
	}
}
