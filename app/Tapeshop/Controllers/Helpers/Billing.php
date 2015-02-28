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
		$this->SetFont('Arial', '', 10);
		//TODO
//		$this->Image(APP_PATH . 'assets/img/Ticketbestellungen.png', 0, 0, -150, -150);
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
	}

	function Body() {

		$this->Ln(37);
		$this->Cell(16.5);

		$this->SetTextColor(0, 0, 0);

		// TODO use templates, e.g. smarty
		$message =
			"Hallo " . $this->order->address->name . ",\n
\n
danke für deine Bestellung!\n
\n
Hier deine Bestellung mit der Rechnungsnummer ". ORDER_PREFIX . $this->order->id . ".\n
\n";

		foreach ($this->order->orderitems as $orderitem) {
			$message = $message . $orderitem->amount . " x - '" . $orderitem->item->name."'";

            if($orderitem->item->ticketcode){
                $message.= "         Ticketcode: ".$orderitem->ticketcode;
            }
            $message.= "\n\n";

		}

		$message = $message . "\n
Viele Grüße,\n
dein Tapeshop Team !\n";

		$this->MultiCell(0, 2.2, utf8_decode($message));
	}
}
