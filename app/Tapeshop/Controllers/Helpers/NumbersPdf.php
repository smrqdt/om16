<?php

namespace Tapeshop\Controllers\Helpers;


use fpdf\FPDF;

class NumbersPdf extends FPDF{

//Current column
	var $col=0;
//Ordinate of column start
	var $y0;

	function Header()
	{
		//Page header
		global $title;

		$this->SetFont('Arial','B',15);
		$w=$this->GetStringWidth($title)+6;
		$this->SetX((210-$w)/2);
		$this->Ln(10);
		//Save ordinate
		$this->y0=$this->GetY();
	}

	function Footer()
	{
		//Page footer
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->SetTextColor(128);
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}

	function SetCol($col)
	{
		//Set position at a given column
		$this->col=$col;
		$x=10+$col*25;
		$this->SetLeftMargin($x);
		$this->SetX($x);
	}

	function AcceptPageBreak()
	{
		//Method accepting or not automatic page break
		if($this->col<7)
		{
			//Go to next column
			$this->SetCol($this->col+1);
			//Set ordinate to top
			$this->SetY($this->y0);
			//Keep on page
			return false;
		}
		else
		{
			//Go back to first column
			$this->SetCol(0);
			//Page break
			return true;
		}
	}

	function ChapterTitle($num,$label)
	{
		//Title
		$this->SetFont('Arial','',12);
		$this->SetFillColor(200,220,255);
		$this->Cell(0,6,"$label",0,1,'L',1);
		$this->Ln(4);
		//Save ordinate
		$this->y0=$this->GetY();
	}

	function ChapterBody($fichier)
	{
		//Read text file
		$txt=$fichier;
		//Font
		$this->SetFont('Arial','',12);
		//Output text in a 2 cm width column
		$this->MultiCell(20,5,$txt);
		$this->Ln();
		//Go back to first column
		$this->SetCol(0);
	}

	function PrintChapter($num,$title,$file)
	{
		//Add chapter
		$this->AddPage();
		$this->ChapterTitle($num,$title);
		$this->ChapterBody($file);
	}
}