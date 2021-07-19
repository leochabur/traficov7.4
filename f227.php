<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
include('../../fpdf.php');



class PDF extends FPDF
{
// Cabecera de p�gina
function Header()
{
    // Logo
    $this->Image('../../masterbus-log.png',10,8,33);     // <img src="masterbus-logo.png" border="0">
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // T�tulo
    $this->Cell(45,10,"Infome de Confibialidad - Mes: $_GET[mes] - A�o: $_GET[anio]",0,0,'C');
    // Salto de l�nea
    $this->Ln(20);
}

// Pie de p�gina
function Footer()
{
    // Posici�n: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // N�mero de p�gina
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creaci�n del objeto de la clase heredada
$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',10);

//print_r($resumen);
$pdf->Output();
//$pdf->Output();

?>
