<?php
  session_start();
set_time_limit(0);
//print '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
require('../../fpdf.php');
require('../../controlador/bdadmin.php');
require('../../modelo/utils/utils.php');



class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
 /*   $this->Image('masterbus-log.png',10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $pdf->text(190,7,"PLANILLA DE CONTROL HORARIOS/PASAJEROS                Fecha: 16/09/2016");   */
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
/*    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');  */
}
}

// Creación del objeto de la clase heredada
$pdf = new PDF('P');

$pdf->AddPage();


                     $export = conexcion();

      $sql="select lower(o.nombre) as nombre, interno,
if ((select valor from opciones where opcion = 'empleador-master' and id_estructura = $_SESSION[structure]) = ch1.id_empleador,
if(ch1.id_empleado is null, (if (ch2.id_empleado is null, '', ch2.apellido)),
                           (if(ch2.id_empleado is null, ch1.apellido,concat(ch1.apellido,' - ',ch2.apellido)))), upper(e.razon_social)) as chofer,
                           if(i_v = 'i', 'ENTRADA', 'SALIDA') as entra, i_v, cli.razon_social, date_format(fservicio, '%d/%m/%Y') as fservicio
from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
left join unidades u on u.id = o.id_micro
left join empleados ch1 on ch1.id_empleado = o.id_chofer_1
left join empleados ch2 on ch2.id_empleado = o.id_chofer_2
left join empleadores e on e.id = ch1.id_empleador
left join clientes cli on cli.id = o.id_cliente and cli.id_estructura = o.id_estructura_cliente
where fservicio = '$_GET[fec]' and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and id_turno = $_GET[tno] and not suspendida and not borrada
order by i_v, o.hcitacion";
         //    die($sql);

      $result = mysql_query($sql, $export) or die(mysql_error($export));
      $data = mysql_fetch_array($result);
      $pdf->SetFillColor(120);
      $pdf->SetFont('Times','',9);
      $pdf->Cell(185,7,"Servicio correspondientes a $data[razon_social]    Fecha: $data[fservicio]",1, 1, 'C', true);
      $pdf->SetFont('Times','',6);
      $auxY = $pdf->getY();
      $auxX = $pdf->getX();
      $pdf->SetFillColor(190);
      while ($data){
            $pdf->setXY($auxX, $auxY);
            $iv = $data[i_v];
            $pdf->Cell(90,5,"$data[entra]",1, 1, 'C', true);
            $pdf->setXY($auxX, $auxY+7);
            $pdf->Cell(30,5,'Servicio',1, 0, 'C', true);
            $pdf->Cell(10,5,'Interno',1, 0, 'C', true);
            $pdf->Cell(30,5,'Conductor',1, 0, 'C', true);
            $pdf->Cell(10,5,'Hora',1, 0, 'C', true);
            $pdf->Cell(10,5,'Pax',1, 1, 'C', true);


            while (($data) &&($iv == $data[i_v])){
                  if ($data[i_v] == 'i'){
                  $nombre = str_replace(' - entrada','',$data['nombre']);
                  $nombre = str_replace('entrada','',$nombre);
                  $nombre = str_replace('-entrada','',$nombre);
                  $nombre = str_replace(' entrada','',$nombre);
                  $nombre = str_replace(' -entrada','',$nombre);
                  }
                  else{
                  $nombre = str_replace(' - salida','',$data['nombre']);
                  $nombre = str_replace('salida','',$nombre);
                  $nombre = str_replace('-salida','',$nombre);
                  $nombre = str_replace(' salida','',$nombre);
                  $nombre = str_replace(' -salida','',$nombre);
                  }
                  $pdf->setX($auxX);
                  $pdf->Cell(30,3,ucwords($nombre),1, 0, 'L', false);
                  $pdf->Cell(10,3,$data[interno],1, 0, 'C', false);
                  $pdf->Cell(30,3, utf8_decode($data[chofer]),1, 0, 'L', false);
                  $pdf->Cell(10,3,'',1, 0, 'C', false);
                  $pdf->Cell(10,3,'',1, 1, 'C', false);
                  $data = mysql_fetch_array($result);
            }
            //die($auxX);
            $auxX+=95;
     //       $pdf->setXY($auxX, $auxY);
           // $pdf->setY($auxY);
      }

$pdf->Output();
//$pdf->Output();

?>
