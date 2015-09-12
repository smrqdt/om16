<?php
namespace Tapeshop\Controllers\Helpers;

use fpdf\FPDF;

/**
 * @property \Tapeshop\Models\Order order
 */
class Billing extends FPDF
{

	function Header()
	{
		$this->Meta();

		// Print the Header
		$this->SetFont('Arial', '', 10);
		//TODO
		$this->Image(APP_PATH . 'assets/img/om15logo.jpg', 115, 20, -280, -280);

		$this->Ln(20);
		$this->Cell(16.5);

		$this->Cell(472, 3, "Junge Piraten e.V.");
		$this->Ln(6);

		$this->Cell(16.5);
		$this->Cell(472, 3, utf8_decode("Pflugstraße 9a"));
		$this->Ln(6);

		$this->Cell(16.5);
		$this->Cell(472, 3, "10115 Berlin");
		$this->Ln(6);

		$this->Ln(12);
		$this->Cell(16.5);

		$this->Cell(472, 3, utf8_decode($this->order->address->name . " " . $this->order->address->lastname));
		$this->Ln(6);

		$this->Cell(16.5);
		$this->Cell(472, 3, utf8_decode($this->order->address->street . " " . $this->order->address->building_number));
		$this->Ln(6);

		$this->Cell(16.5);
		$this->Cell(472, 3, utf8_decode($this->order->address->postcode . " " . $this->order->address->city));

		$this->Ln(6);
		$this->Cell(16.5);


		$this->Body();
	}

	function Meta()
	{
	}

	function Body()
	{

		$this->Ln(60);
		$this->Cell(16.5);
		$this->Cell(130, 0, utf8_decode("Hallo " . $this->order->address->name . ","));
		$this->Cell(0, 0, date('d.m.Y', $this->order->ordertime->getTimestamp()));

		$this->Ln(6);
		$this->Cell(16.5);

		$message = "


danke, dass du bei der Openmind Konferenz am 12. und 13. Oktober 2015 warst!\n
\n
Hier die Rechnung für deine Bestellung mit der Rechnungsnummer 20150912" .str_pad($this->order->id, 4, '0', STR_PAD_LEFT)  . ".\n
\n";

		foreach ($this->order->orderitems as $orderitem) {
			$message = $message . $orderitem->amount . " x - " . $orderitem->item->name . " (" . number_format($orderitem->getSum() / 100, 2, ",", ".") . " Euro)";
			$message .= "\n\n";
		}

		$message .= "\nGesamt: " . number_format($this->order->getSum() / 100, 2, ",", ".") . " Euro";
		$message = $message . "\n\n\n\n
Nicht umsatzsteuerpflichtig nach § 19 Abs. 1 UStG (Kleinunternehmerregelung).\n

Betrag dankend erhalten.
\n
Steuernummer: 27/669/51491\n\n
Eingetragen beim Amtsgericht Charlottenburg, VR 30966 B\n\n
Der Verein wird je einzeln vertreten durch Simon Marquardt (Schatzmeister) und Leo Bellersen\n
(Generalsekretär). Bei Fragen wende dich an finanzen@junge-piraten.de.\n\n\n\n
Viele Grüße,\n
dein Openmind Team !\n";

		$this->MultiCell(0, 2.2, utf8_decode($message));
	}
}
