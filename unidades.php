<?php
     set_time_limit(0);
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
require('fpdf.php');
require('controlador/bdadmin.php');
require('modelo/utils/utils.php');




class PDF extends FPDF
{
// Cabecera de página                                                                        ad
function Header()
{
    // Logo
    $this->Image('masterbus-log.png',10,8,33);     // <img src="masterbus-logo.png" border="0">
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(45,10,"Unidades utilizadas durante ".ucwords(nombremes($_GET['mes']))." de $_GET[anio]",0,0,'C');
    // Salto de línea
    $this->Ln(20);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();

$pdf->SetFont('Times','',10);

                     $export = conexcion();//mysql_connect('200.80.61.27', 'mbexpuser', 'Mb2013Exp');
                     //mysql_select_db('mbexport', $export);

$sql="SELECT ts.tipo, interno, if (anio is null, '', anio) as anio, if (nueva_patente is null, patente, nueva_patente) as patente, cantasientos
FROM ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join tiposervicio ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
inner join unidades u on u.id = o.id_micro
where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_GET[str] and id_cliente = $_GET[cli] and not borrada and not suspendida
group by s.id_TipoServicio, id_micro
order by tipo, interno";
//die($sql);
$result = mysql_query($sql, $export);
$data = mysql_fetch_array($result);

$total = 0;
$antig = 0;
while ($data){
            $tipo = $data['tipo'];
            $pdf->AddPage();
            $i=1;
            $pdf->SetFont('Times','',12);
            $pdf->Cell(45,10,"Tipo de servicio: $tipo",0,0,'C');
            $pdf->SetFont('Times','',10);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 0);
            $pdf->Cell(15,7,'Interno',1, 0, 'C', True);
            $pdf->Cell(20,7,'Dominio',1, 0, 'C', True);
            $pdf->Cell(16,7,'Capacidad',1, 0, 'C', True);
            $pdf->Cell(20,7,'Año',1, 0, 'C', True);
            $pdf->SetFont('Times','',9);
            $pdf->Ln();
            $parcialCant = 0;
            $parcialAnti = 0;
            $intReales = 0;
            while (($tipo == $data['tipo'])&&($data)){
                              $intReales++;
                              if ($data['anio']){
                                 $parcialCant++;
                                 $parcialAnti+= $data['anio'];
                              }
                              $pdf->Cell(15,6,$data['interno'],1);
                              $pdf->Cell(20,6,$data['patente'],1);
                              $pdf->Cell(16,6,$data['cantasientos'],1);
                              $pdf->Cell(20,6,$data['anio'],1,'R');
                              $pdf->Ln();
                              $data = mysql_fetch_array($result);
                              $suma+=$data[15];
                              $cant++;
                        //      $resuemn[$tipo][$turno][$data[4]]+=$data[15];
            }
            $pdf->SetFont('Times','',12);
            $pdf->Cell(71,7,"Cantidad unidades utilizadas: $intReales ",1);
            $pdf->Ln();
            $pdf->Cell(71,7,"Antiguedad promedio: ".(round($parcialAnti/$parcialCant)),1);
}
           // die();
//print_r($resumen);
$pdf->Output();
//$pdf->Output();

?>
