<?php
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
// Cabecera de página
function Header()
{
    // Logo
    $this->Image('masterbus-log.png',10,8,33);     // <img src="masterbus-logo.png" border="0">
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(45,10,"Sucesos correspondientes a ".ucwords(nombremes($_GET['mes']))." de $_GET[anio]",0,0,'C');
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
$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',6);

                     $export = conexcion();//mysql_connect('200.80.61.27', 'mbexpuser', 'Mb2013Exp');
                     //mysql_select_db('mbexport', $export);
                     $sql="select o.*, time_format(timediff(Hora_Real_Llegada_Planta, Hora_Diagramada_Llegada_Planta), '%H:%i') as difLlegada, time_format(timediff(Hora_Real_Salida_Planta, Hora_Diagramada_Salida_Planta), '%H:%i') as difsalida, ts.tipo, os.comentario
from(
SELECT o.id, date_format(o.fservicio, '%d/%m/%Y') as fecha,
       if (o.id_servicio is null, (select id_tipo_servicio from tipotnoordenes where id_orden = o.id), s.id_tipoServicio) as tipo_serv,
       turno, upper(o.nombre) as servicio,
       if (i_v is null, if(o.id_ciudad_destino = 3, 'Llegada a Planta', 'Salida de Planta'), if (i_v = 'i', 'Llegada a Planta', 'Salida de Planta')) as Entrada_Salida,
        upper(ori.ciudad) as Origen, Interno, u.cantasientos, o.cantpax, round((o.cantpax/cantasientos)*100,2) as Eficiencia,
       if (i_v is null, if(o.id_ciudad_destino = 3, time_format(o.hllegada, '%H:%i'), null), if (i_v = 'i', time_format(o.hllegada, '%H:%i'), null)) as Hora_Diagramada_Llegada_Planta,
       if (i_v is null, if(o.id_ciudad_destino = 3, time_format(o.hllegadaplantareal, '%H:%i'), null), if (i_v = 'i', time_format(o.hllegadaplantareal,'%H:%i'), null)) as Hora_Real_Llegada_Planta,
       if (i_v is null, if(o.id_ciudad_destino = 3, null, time_format(o.hsalida,'%H:%i')), if (i_v = 'i', null, time_format(o.hsalida,'%H:%i'))) as Hora_Diagramada_Salida_Planta,
       if (i_v is null, if(o.id_ciudad_destino = 3, null, time_format(o.hsalidaplantareal,'%H:%i')), if (i_v = 'i', null, time_format(o.hsalidaplantareal,'%H:%i'))) as Hora_Real_Salida_Planta,

       if (i_v is null,
                     if(o.id_ciudad_destino = 3,
cast(if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                     ,
cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                   ), if (i_v = 'i',
cast(if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                                                 ,
  cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2)))) as Confiabilidad, o.fservicio, i_v

FROM ordenes o
left JOIN servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
left JOIN turnos tu ON tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
left JOIN ciudades ori ON ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
left JOIN ciudades des ON des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
left join unidades u on u.id = o.id_micro
where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida) o
left join obsSupervisores os on os.id_orden = o.id
left JOIN tiposervicio ts ON ts.id = o.tipo_serv
where (Confiabilidad between 0 and 90) and (tipo_serv not in (3, 16))
order by o.fservicio";
//die($sql);
            $result = mysql_query($sql, $export);
            $i=1;
            $pdf->SetFillColor(255, 255, 0);
            $pdf->Cell(12,7,'Fecha',1, 0, 'C', True);
            $pdf->Cell(15,7,'Tipo',1, 0, 'C', True);
            $pdf->Cell(8,7,'Turno',1, 0, 'C', True);
            $pdf->Cell(45,7,'Servicio',1, 0, 'C', True);
            $pdf->Cell(16,7,'Entrada_Salida',1, 0, 'C', True);
            $pdf->Cell(25,7,'Origen',1, 0, 'C', True);
            $pdf->Cell(7,7,'Interno',1, 0, 'C', True);
            $pdf->Cell(5,7,'Cap.',1, 0, 'C', True);
            $pdf->Cell(5,7,'Pax',1, 0, 'C', True);
            $pdf->Cell(8,7,'H. std',1, 0, 'C', True);
            $pdf->Cell(8,7,'H. Real',1, 0, 'C', True);
            $pdf->Cell(9,7,'Minutos',1, 0, 'C', True);
            $pdf->Cell(9,7,'Confiab.',1, 0, 'C', True);
            $pdf->Cell(110,7,'Comentario',1, 0, 'C', True);
            $pdf->Ln();
            $data = mysql_fetch_array($result);
            while ($data){
                  $std = $data[13];
                  $real = $data[14];
                  $minutos=$data[19];
                  if ($data['i_v'] == 'i'){
                     $std = $data[11];
                     $real = $data[12];
                     $minutos = $data[18];
                  }
                              $pdf->Cell(12,6,$data[1],1);
                              $pdf->Cell(15,6,$data[20],1);
                              $pdf->Cell(8,6,utf8_decode($data[3]),1);
                              $pdf->Cell(45,6,$data[4],1,'R');
                              $pdf->Cell(16,6,$data[5],1,'R');
                              $pdf->Cell(25,6, utf8_decode($data[6]),1,'R');
                              $pdf->Cell(7,6,$data[7],1,'R');
                              $pdf->Cell(5,6,$data[8],1,'R');
                              $pdf->Cell(5,6,$data[9],1,'R');
                              $pdf->Cell(8,6,$std,1,'R');
                              $pdf->Cell(8,6,$real,1,'R');//hora real
                              $pdf->Cell(9,6,$minutos,1,'R');
                              $pdf->Cell(9,6,$data[15],1,'R');
                              $pdf->Cell(110,6, utf8_decode($data[21]),1,'R');
                              $pdf->Ln();
                              $data = mysql_fetch_array($result);
                              $suma+=$data[15];
                              $cant++;
                              $resuemn[$tipo][$turno][$data[4]]+=$data[15];
            }
//print_r($resumen);
$pdf->Output();
//$pdf->Output();

?>
