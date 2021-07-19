<?php
     set_time_limit(0);
  session_start();
  error_reporting(E_ALL & ~E_NOTICE); 
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
require('../../fpdf.php');
require('../../modelo/utils/dateutils.php');
include '../../modelsORM/manager.php';





class PDF extends FPDF
{

    public $fecha;
    public $inicio, $consumos, $ingresos;

    function __construct($si, $consumos, $ingresos)
    {
      parent::__construct();
      $this->inicio = $si;
      $this->consumos = $consumos;
      $this->ingresos = $ingresos;
    }

    // Cabecera de página
    function Header()
    {
        $this->setXY(10,8);
        $this->Cell(60,8,'Fecha',1, 0, 'C', false);
        // Logo
        $this->Image('../../masterbus-logo.png',11,9,33);     // <img src="masterbus-logo.png" border="0">
        $this->setXY(70,8);
        $this->SetFont('Arial','',10);
        $this->Cell(60,8,'FORM 042.22 Despacho de Gasoil',1, 1, 'C', false);        
        // Arial bold 15
        $this->setXY(130,8);
        // Movernos a la derecha
        $this->Cell(0,8,'Fecha Vigencia: 04/01/2017',1, 0, 'C', false);
        $this->Ln();
        // Título

        $this->setX(10);
        $this->Cell(40,5,'Fecha ',1, 0, 'C', false);
        $this->Cell(40,5,'Litros Inicial',1, 0, 'C', false);        
        $this->Cell(40,5,'Consumos',1, 0, 'C', false);
        $this->Cell(40,5,'Ingresos',1, 0, 'C', false);      
        $this->Cell(0,5,'Litros Final',1, 0, 'C', false);          
        $this->ln();
        $this->setX(10);
        $this->Cell(40,5,$this->fecha->format('d/m/Y'),1, 0, 'C', false);
        $this->Cell(40,5,$this->inicio,1, 0, 'C', false);        
        $this->Cell(40,5,$this->consumos,1, 0, 'C', false);
        $this->Cell(40,5,$this->ingresos,1, 0, 'C', false);        
        $this->Cell(0,5,($this->inicio-$this->consumos+$this->ingresos),1, 0, 'C', false);         
        $this->ln();          
    }

    // Pie de página
 /*   function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }*/
}
$fecha = DateTime::createFromFormat('d/m/Y', $_GET['fec']);
$tipoFluido = $entityManager->createQuery("SELECT t FROM TipoFluido t WHERE t.id = :id")->setParameter('id', $_GET['pr'])->getOneOrNullResult();
$q = $entityManager->createQuery("SELECT cc
                                  FROM CargaCombustible cc
                                  WHERE cc.fecha < :fecha AND cc.tipoFluido = :tipo");    
$result = $q->setParameter('fecha', $fecha->format('Y-m-d'))->setParameter('tipo', $tipoFluido)->getResult();
$saldoi = 0;
foreach ($result as $value) {
    if ($value->getIngreso()){
      $saldoi+=$value->getLitros();
    }
    else{
      $saldoi-=$value->getLitros();
    }
} 

$q = $entityManager->createQuery("SELECT cc
                                  FROM CargaCombustible cc
                                  WHERE cc.fecha = :fecha AND cc.tipoFluido = :tipo");    
$result = $q->setParameter('fecha', $fecha->format('Y-m-d'))->setParameter('tipo', $tipoFluido)->getResult();
$consumos = 0;
$ingresos = 0;
foreach ($result as $value) {
    if ($value->getIngreso()){
      $ingresos+=$value->getLitros();
    }
    else{
      $consumos+=$value->getLitros();
    }
}     

// Creación del objeto de la clase heredada
$pdf = new PDF($saldoi, $consumos, $ingresos);
$pdf->fecha = $fecha;
$pdf->inicio = $saldoi;
$pdf->fin = $saldof;
$pdf->AliasNbPages();
$pdf->AddPage('P', 'Legal');
$pdf->SetFont('Times','',8);
$pdf->SetAutoPageBreak(true,0);  

$q = $entityManager->createQuery("SELECT cc, u.interno as interno
                                  FROM CargaCombustible cc
                                  INNER JOIN cc.unidad u
                                  WHERE cc.fecha=:fecha AND cc.tipoFluido = :tipo
                                  ORDER BY u.interno, cc.fechaAlta");    
$result = $q->setParameter('fecha', $fecha->format('Y-m-d'))->setParameter('tipo', $tipoFluido)->getResult();
$i=1;
///////////col 1
$pdf->SetFillColor(255, 255, 0);
$pdf->Cell(12,7,'Interno ',1, 0, 'C', True);
$pdf->Cell(17,7,'Tacografo',1, 0, 'C', True);
$pdf->Cell(15,7,'Litros',1, 0, 'C', True);
$pdf->Cell(21,7,'Hora',1, 0, 'C', True);
//////////////////col 2
$pdf->setX($pdf->getX()+1);
$pdf->Cell(12,7,'Interno',1, 0, 'C', True);
$pdf->Cell(17,7,'Tacografo',1, 0, 'C', True);
$pdf->Cell(15,7,'Litros',1, 0, 'C', True);
$pdf->Cell(21,7,'Hora',1, 0, 'C', True);
////////////col 3
$pdf->setX($pdf->getX()+1);
$pdf->Cell(12,7,'Interno',1, 0, 'C', True);
$pdf->Cell(17,7,'Tacografo',1, 0, 'C', True);
$pdf->Cell(15,7,'Litros',1, 0, 'C', True);
$pdf->Cell(21,7,'Hora',1, 0, 'C', True);
$pdf->Ln();
$i = 0;
$x = $pdf->getX();
$y = $pdf->getY();
$mult = 0;
foreach ($result as $carga) {
    
    if ((($i % 62) == 0) && ($i > 0)){
      $x+=65;
      $pdf->setY($y);
      $mult++;
    }
    $pdf->setX($x+$mult);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(12,5,$carga['interno'],1, 0, 'C', True);
    $pdf->Cell(17,5,$carga[0]->getOdometro(),1, 0, 'C', True);
    $pdf->Cell(15,5,$carga[0]->getLitros(),1, 0, 'C', True);
    $pdf->Cell(21,5,$carga[0]->getFechaAlta()->format('H:i'),1, 0, 'C', True);
    $pdf->Ln();
    $i++;
}

$pdf->Output();


?>
