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
    Select *, upper(ori.origen) as City_Origen
    from(
        SELECT o.id as id_orden, date_format(o.fservicio, '%d/%m/%Y') as fecha, upper(o.nombre) as servicio, interno,
               if (if (tto.i_v is null, s.i_v, tto.i_v) = 'i', 'Llegada a Planta', 'Salida de Planta') as Entrada_Salida,
               if (tto.id_turno is null, s.id_turno, tto.id_turno) as id_turno,
               if (tto.id_tipo_servicio is null, s.id_TipoServicio, tto.id_tipo_servicio) as id_tipo_servicio,
               o.cantpax, round((o.cantpax/cantasientos)*100,2) as Eficiencia, cantasientos,
               if (if (tto.i_v is null, s.i_v, tto.i_v) = 'i', time_format(o.hllegada, '%H:%i'), null) as Hora_Diagramada_Llegada_Planta,
               if (if (tto.i_v is null, s.i_v, tto.i_v) = 'i', time_format(o.hllegadaplantareal,'%H:%i'), null) as Hora_Real_Llegada_Planta,
               if (if (tto.i_v is null, s.i_v, tto.i_v) = 'i', null, time_format(o.hsalida,'%H:%i')) as Hora_Diagramada_Salida_Planta,
               if (if (tto.i_v is null, s.i_v, tto.i_v) = 'i', null, time_format(o.hsalidaplantareal,'%H:%i')) as Hora_Real_Salida_Planta,
               if (tto.i_v is null, s.i_v, tto.i_v) as i_v,
              if (if (tto.i_v is null, s.i_v, tto.i_v) = 'i',
                       cast(if (o.hllegadaplantareal <= o.hllegada,
                                  1,
                                  if((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                      (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                                      if((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                         (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                                          0)))*100 as decimal(5,2)),
                       cast(if (o.hsalidaplantareal <= o.hsalida,
                                  1,
                                  if((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                                       if((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                           (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                                           0)))*100 as decimal(5,2))) as Confiabilidad, o.fservicio, o.id_estructura as id_estructura_orden, o.id_cliente, o.borrada, o.suspendida, id_ciudad_origen, id_ciudad_destino

        FROM (SELECT * FROM ordenes o where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_GET[str] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida) o
        left join (select id as id_srv, i_v, id_turno, id_TipoServicio from servicios where id_estructura = $_GET[str]) s on s.id_srv = o.id_servicio
        left join (Select id_orden, i_v, id_turno, id_tipo_servicio from tipotnoordenes where id_estructura = $_GET[str]) tto on tto.id_orden = o.id
        left join (select interno, cantasientos, id as id_micro from unidades) u on u.id_micro = o.id_micro) o
    left JOIN (select * from turnos where id_estructura = $_GET[str]) tu ON tu.id = id_turno
    left JOIN (select id as id_origen, ciudad as origen from ciudades where id_estructura = $_GET[str]) ori ON ori.id_origen = o.id_ciudad_origen
    left JOIN (select id as id_destino, ciudad as destino from ciudades where id_estructura = $_GET[str]) des ON des.id_destino = o.id_ciudad_destino) o
left join obsSupervisores os on os.id_orden = o.id_orden
left JOIN tiposervicio ts ON ts.id = id_tipo_servicio
where (Confiabilidad between 0 and 90) and (id_tipo_servicio not in (3, 16))
order by o.fservicio";

$sql="select date_format(fservicio, '%d/%m/%Y') as fecha, ts.tipo, tu.turno, nombre as servicio, i_v, origen, interno, cantasientos, cantpax,
       time_format(hllegada, '%H:%i') as hllegada, time_format(hsalida, '%H:%i') as hsalida,
       time_format(hllegadaplantareal, '%H:%i') as hllegadaplantareal, time_format(hsalidaplantareal, '%H:%i') as hsalidaplantareal,
       time_format(if (i_v = 'i', (timediff(o.hllegadaplantareal, o.hllegada)), (timediff(hsalidaplantareal, hsalida))), '%H:%i') as minutos,
       comentario,
             if (i_v = 'i',
                       cast(if (o.hllegadaplantareal <= o.hllegada,
                                  1,
                                  if((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                      (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                                      if((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                         (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                                          0)))*100 as decimal(5,2)),
                       cast(if (o.hsalidaplantareal <= o.hsalida,
                                  1,
                                  if((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                                       if((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                           (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                                           0)))*100 as decimal(5,2))) as Confiabilidad

from(
    select i_v, id_micro, id_turno, id_tipo_servicio, id_ciudad_origen, fservicio, nombre, cantpax, hllegada, hsalida, hllegadaplantareal, hsalidaplantareal, id_orden
    from(
         select if (o.i_v is not null, o.i_v, s.i_v) as i_v,
                if (o.id_turno is not null, o.id_turno, s.id_turno) as id_turno,
                if (o.id_tipo_servicio is not null, o.id_tipo_servicio, s.id_TipoServicio) as id_tipo_servicio, id_micro, id_ciudad_origen,
                fservicio, nombre, cantpax, hllegada, hsalida, hllegadaplantareal, hsalidaplantareal, o.id as id_orden
         from(
              SELECT id_servicio, id, fservicio, nombre, cantpax, hllegada, hsalida, hllegadaplantareal, hsalidaplantareal, id_ciudad_origen,
                     id_micro, tto.i_v, tto.id_turno, tto.id_tipo_servicio
              FROM ordenes o
              left join tipotnoordenes tto on tto.id_orden = o.id
              where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_GET[str] and o.id_cliente = $_GET[cli] and not borrada and not suspendida and (hllegadaplantareal is not null || hsalidaplantareal is not null)
         ) o
         left join (select id as id_srv, i_v, id_turno, id_TipoServicio from servicios where id_estructura = $_GET[str]) s on s.id_srv = o.id_servicio
    ) o
) o
left join (select interno, cantasientos, id as id_micro from unidades) u on u.id_micro = o.id_micro
inner JOIN (select * from turnos where id_estructura = $_GET[str]) tu ON tu.id = id_turno
inner JOIN (select id, tipo from tiposervicio where id not in (3, 16) and id_estructura = $_GET[str]) ts ON ts.id = id_tipo_servicio
left JOIN (select id as id_origen, ciudad as origen from ciudades where id_estructura = $_GET[str]) ori ON ori.id_origen = id_ciudad_origen
left join obsSupervisores os on os.id_orden = o.id_orden
order by o.fservicio, o.hsalida";
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
                  if ($data['i_v'] == 'v'){
                     $std = $data['hsalida'];
                     $real = $data['hsalidaplantareal'];
                     $minutos=$data['minutos'];
                     $e_s = "Salida de Planta";
                  }
                  elseif ($data['i_v'] == 'i'){
                     $std = $data['hllegada'];
                     $real = $data['hllegadaplantareal'];
                     $minutos=$data['minutos'];
                     $e_s = "Entrada a Planta";
                  }
                  /*if ($minutos > 0){
                        if ($minutos <= 5){
                           $conf = 1-($minutos*0.02);
                        }
                        elseif($minutos < 20){
                           $conf = (0.9-(($minutos-5)*0.06));
                        }
                        else
                            $conf=0;

                  }
                                          print $minutos."<br>";
                  */
                  $conf=$data['Confiabilidad'];
                  if ($conf <=90){
                              $pdf->Cell(12,6,$data['fecha'],1);
                              $pdf->Cell(15,6,$data['tipo'],1);
                              $pdf->Cell(8,6,utf8_decode($data['turno']),1);
                              $pdf->Cell(45,6,$data['servicio'],1,'R');
                              $pdf->Cell(16,6,$e_s,1,'R');
                              $pdf->Cell(25,6, utf8_decode($data['origen']),1,'R');
                              $pdf->Cell(7,6,$data['interno'],1,'R');
                              $pdf->Cell(5,6,$data['cantasientos'],1,'R');
                              $pdf->Cell(5,6,$data['cantpax'],1,'R');
                              $pdf->Cell(8,6,$std,1,'R');
                              $pdf->Cell(8,6,$real,1,'R');//hora real
                              $pdf->Cell(9,6,$minutos,1,'R');
                              $pdf->Cell(9,6,$conf,1,'R');
                              $pdf->Cell(110,6, utf8_decode($data['comentario']),1,'R');
                              $pdf->Ln();
                  }
                              $data = mysql_fetch_array($result);
                              $suma+=$data[15];
                              $cant++;
                        //      $resuemn[$tipo][$turno][$data[4]]+=$data[15];
            }
           // die();
//print_r($resumen);
$pdf->Output();
//$pdf->Output();

?>
